<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuizAttemptController extends Controller
{
    public function start(Request $request, Quiz $quiz)
    {
        // Check authorization
        $this->authorize('take', $quiz);

        $student = auth()->user()->student;

        // Check if student can take quiz
        if (!$quiz->studentCanTakeQuiz($student)) {
            return back()->with('error', 'You have reached the maximum number of attempts for this quiz.');
        }

        // Check for existing in-progress attempt
        $existingAttempt = $quiz->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existingAttempt) {
            return redirect()
                ->route('student.quiz-attempts.take', $existingAttempt)
                ->with('info', 'Resuming your in-progress quiz attempt.');
        }

        try {
            DB::beginTransaction();

            // Get next attempt number
            $attemptNumber = $quiz->attempts()
                ->where('student_id', $student->id)
                ->max('attempt_number') + 1;

            // Prepare question order
            $questions = $quiz->questions;
            if ($quiz->randomize_questions) {
                $questions = $questions->shuffle();
            }
            $questionOrder = $questions->pluck('id')->toArray();

            // Create new attempt
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'attempt_number' => $attemptNumber,
                'total_points' => $quiz->getTotalPoints(),
                'question_order' => $questionOrder,
                'ip_address' => $request->ip(),
            ]);

            $attempt->start();

            // Log attempt start
            AuditLog::log('quiz_attempt_started', $attempt);

            DB::commit();

            return redirect()
                ->route('student.quiz-attempts.take', $attempt)
                ->with('success', 'Quiz started successfully. Good luck!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to start quiz: ' . $e->getMessage());
        }
    }

    public function take(QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        // Check if already completed
        if ($attempt->isCompleted()) {
            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('info', 'This quiz has already been completed.');
        }

        $attempt->load([
            'quiz.subject',
            'quiz.instructor.user',
            'answers.question.choices'
        ]);

        // Get questions in the order they were presented
        $questionIds = $attempt->question_order;
        $questions = $attempt->quiz->questions()
            ->whereIn('question_bank.id', $questionIds)
            ->with(['choices' => function($query) use ($attempt) {
                if ($attempt->quiz->randomize_choices) {
                    $query->inRandomOrder();
                } else {
                    $query->orderBy('order');
                }
            }])
            ->get()
            ->sortBy(function($question) use ($questionIds) {
                return array_search($question->id, $questionIds);
            });

        // Get existing answers
        $existingAnswers = $attempt->answers()
            ->with('choice')
            ->get()
            ->keyBy('question_id');

        // Calculate remaining time
        $remainingTime = $attempt->getRemainingTime();
        
        // Check if time expired
        if ($remainingTime !== null && $remainingTime <= 0) {
            return $this->autoSubmit($attempt);
        }

        return view('student.quiz-attempts.take', compact(
            'attempt',
            'questions',
            'existingAnswers',
            'remainingTime'
        ));
    }

    public function saveAnswer(Request $request, QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if already completed
        if ($attempt->isCompleted()) {
            return response()->json(['error' => 'Quiz already completed'], 400);
        }

        $validated = $request->validate([
            'question_id' => ['required', 'exists:question_bank,id'],
            'choice_id' => ['nullable', 'exists:choices,id'],
            'answer_text' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $question = $attempt->quiz->questions()->findOrFail($validated['question_id']);

            // Create or update answer
            $answer = QuizAnswer::updateOrCreate(
                [
                    'attempt_id' => $attempt->id,
                    'question_id' => $validated['question_id'],
                ],
                [
                    'choice_id' => $validated['choice_id'] ?? null,
                    'answer_text' => $validated['answer_text'] ?? null,
                ]
            );

            // Auto-grade if possible
            if ($question->isMultipleChoice() || $question->isTrueFalse()) {
                $answer->gradeAutomatically();
            }

            return response()->json([
                'success' => true,
                'message' => 'Answer saved successfully',
                'answer_id' => $answer->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save answer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        // Check if already completed
        if ($attempt->isCompleted()) {
            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('info', 'This quiz has already been submitted.');
        }

        try {
            DB::beginTransaction();

            // Grade all auto-gradable answers
            $answers = $attempt->answers()->with('question')->get();
            foreach ($answers as $answer) {
                if ($answer->question->isMultipleChoice() || $answer->question->isTrueFalse()) {
                    $answer->gradeAutomatically();
                }
            }

            // Complete the attempt
            $attempt->complete();

            // Log submission
            AuditLog::log('quiz_attempt_submitted', $attempt);

            DB::commit();

            // TODO: Send email notification if quiz settings allow
            // if ($attempt->quiz->show_results) {
            //     Mail::to($attempt->student->user->email)->send(new QuizResultMail($attempt));
            // }

            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('success', 'Quiz submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit quiz: ' . $e->getMessage());
        }
    }

    public function results(QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        // Check if results are available
        if (!$attempt->quiz->show_results) {
            return back()->with('error', 'Results are not available for this quiz.');
        }

        $attempt->load([
            'quiz.subject',
            'quiz.instructor.user',
            'answers.question.choices',
            'answers.choice'
        ]);

        // Get all attempts for comparison
        $allAttempts = QuizAttempt::where('student_id', $attempt->student_id)
            ->where('quiz_id', $attempt->quiz_id)
            ->where('status', 'completed')
            ->orderBy('attempt_number')
            ->get();

        return view('student.quiz-attempts.results', compact('attempt', 'allAttempts'));
    }

    public function review(QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        // Check if answers can be viewed
        if (!$attempt->quiz->show_answers) {
            return back()->with('error', 'Answer review is not available for this quiz.');
        }

        $attempt->load([
            'quiz.subject',
            'quiz.instructor.user',
            'answers.question.choices',
            'answers.choice'
        ]);

        // Get questions in the order they were presented
        $questionIds = $attempt->question_order;
        $questions = $attempt->quiz->questions()
            ->whereIn('question_bank.id', $questionIds)
            ->with('choices')
            ->get()
            ->sortBy(function($question) use ($questionIds) {
                return array_search($question->id, $questionIds);
            });

        $answers = $attempt->answers()
            ->with(['choice', 'question'])
            ->get()
            ->keyBy('question_id');

        return view('student.quiz-attempts.review', compact(
            'attempt',
            'questions',
            'answers'
        ));
    }

    private function autoSubmit(QuizAttempt $attempt)
    {
        try {
            DB::beginTransaction();

            // Grade all auto-gradable answers
            $answers = $attempt->answers()->with('question')->get();
            foreach ($answers as $answer) {
                if ($answer->question->isMultipleChoice() || $answer->question->isTrueFalse()) {
                    $answer->gradeAutomatically();
                }
            }

            // Complete the attempt
            $attempt->complete();

            // Log auto-submission
            AuditLog::log('quiz_attempt_auto_submitted', $attempt, [], [
                'reason' => 'time_expired'
            ]);

            DB::commit();

            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('warning', 'Quiz was automatically submitted because time expired.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'Failed to auto-submit quiz: ' . $e->getMessage());
        }
    }


    public function exportPdf(QuizAttempt $attempt)
    {
        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        $attempt->load([
            'quiz.subject.course',
            'quiz.instructor.user',
            'answers.question.choices',
            'answers.choice'
        ]);

        $pdf = Pdf::loadView('student.quiz-attempts.pdf', compact('attempt'));
        
        $filename = 'Quiz_Result_' . $attempt->quiz->title . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

}