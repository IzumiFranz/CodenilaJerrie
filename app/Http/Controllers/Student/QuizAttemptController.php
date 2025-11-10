<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\AuditLog;
use App\Mail\QuizResultMail;
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
        if (!$student || $attempt->student_id !== $student->id) {
            abort(403);
        }
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
        $student = auth()->user()->student;
        if (!$student || $attempt->student_id !== $student->id) {
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
    
            // Calculate scores
            $totalScore = $answers->sum('points_earned'); // adjust column if different
            $totalPoints = $answers->sum(fn($a) => $a->question->points ?? 0);
            $percentage = $totalPoints > 0 ? round(($totalScore / $totalPoints) * 100, 2) : 0;
    
            // Update attempt
            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
                'score' => $totalScore,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
            ]);
    
            // Complete internal logic (events, logging, etc.)
            $attempt->complete();
    
            DB::commit();
    
            // Log audit after commit
            AuditLog::log('quiz_submitted', $attempt);
    
            // 🔔 Send result email after commit
            $settings = $attempt->student->user->settings ?? (object)[
                'email_quiz_result' => true,
                'notification_quiz_result' => true
            ];
            
            if ($settings->email_quiz_result) {
                Mail::to($attempt->student->user->email)->queue(new QuizResultMail($attempt));
            }
            
            if ($settings->notification_quiz_result) {
                Notification::create([
                    'user_id' => $attempt->student->user_id,
                    'type' => $attempt->isPassed() ? 'success' : 'warning',
                    'title' => 'Quiz Result Available',
                    'message' => "Your result for '{$attempt->quiz->title}': {$percentage}%",
                    'action_url' => route('student.quiz-attempts.results', $attempt),]);
            }
    
            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('success', 'Quiz submitted successfully!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit quiz.');
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

    public function review(Quiz $quiz, QuizAttempt $attempt)
    {
        $student = auth()->user()->student;
        
        // Verify ownership
        if ($attempt->student_id !== $student->id || $attempt->quiz_id !== $quiz->id) {
            abort(403);
        }
        
        // Check if review is allowed
        if (!$quiz->canReview($attempt)) {
            if ($quiz->review_available_after) {
                $waitMinutes = $quiz->review_available_after - $attempt->completed_at->diffInMinutes(now());
                return back()->with('error', "Review will be available in {$waitMinutes} minutes.");
            }
            return back()->with('error', 'Review is not available for this quiz.');
        }
        
        // Load attempt with all data
        $attempt->load([
            'answers.question.choices',
            'answers.choice',
        ]);
        
        // Get questions in order
        if ($attempt->question_order) {
            $questionOrder = $attempt->question_order;
            $questions = $quiz->questions->sortBy(function($question) use ($questionOrder) {
                return array_search($question->id, $questionOrder);
            });
        } else {
            $questions = $quiz->questions;
        }
        
        return view('student.quiz-attempts.review', compact('quiz', 'attempt', 'questions'));
    }

    public function printReview(Quiz $quiz, QuizAttempt $attempt)
    {
        $student = auth()->user()->student;
        
        // Verify ownership and review access
        if ($attempt->student_id !== $student->id || !$quiz->canReview($attempt)) {
            abort(403);
        }
        
        $attempt->load([
            'answers.question.choices',
            'answers.choice',
        ]);
        
        if ($attempt->question_order) {
            $questionOrder = $attempt->question_order;
            $questions = $quiz->questions->sortBy(function($question) use ($questionOrder) {
                return array_search($question->id, $questionOrder);
            });
        } else {
            $questions = $quiz->questions;
        }
        
        return view('student.quiz-attempts.print-review', compact('quiz', 'attempt', 'questions'));
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
    
            // Calculate scores
            $totalScore = $answers->sum('points_earned');
            $totalPoints = $answers->sum(fn($a) => $a->question->points ?? 0);
            $percentage = $totalPoints > 0 ? round(($totalScore / $totalPoints) * 100, 2) : 0;
    
            // Update attempt
            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
                'score' => $totalScore,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
            ]);
    
            $attempt->complete();
    
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
                ->with('error', 'Failed to auto-submit quiz.');
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