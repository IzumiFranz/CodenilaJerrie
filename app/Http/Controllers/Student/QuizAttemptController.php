<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Mail\QuizResultMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class QuizAttemptController extends Controller
{
    public function start(Request $request, Quiz $quiz)
    {
        $this->authorize('take', $quiz);

        $student = auth()->user()->student;

        if (!$student) {
            abort(403, 'Student not found.');
        }

        // Check if retakes are allowed
        $completedAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        if ($completedAttempts > 0 && !$quiz->allow_retake) {
            return redirect()->back()->with('error', 'Retakes are not allowed for this quiz.');
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
                'status' => 'in_progress',
                'started_at' => now(),
            ]);

            $attempt->start();

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
        $student = auth()->user()->student;

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

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

        // Get ordered questions
        $questionIds = $attempt->question_order;
        $questions = $attempt->quiz->questions()
            ->whereIn('question_bank.id', $questionIds)
            ->with(['choices' => function ($query) use ($attempt) {
                if ($attempt->quiz->randomize_choices) {
                    $query->inRandomOrder();
                } else {
                    $query->orderBy('order');
                }
            }])
            ->get()
            ->sortBy(fn($q) => array_search($q->id, $questionIds));

        $existingAnswers = $attempt->answers()->with('choice')->get()->keyBy('question_id');

        $remainingTime = $attempt->getRemainingTime();
        if ($remainingTime !== null && $remainingTime <= 0) {
            return $this->autoSubmit($attempt);
        }

        return view('student.quiz-attempts.take', compact('attempt', 'questions', 'existingAnswers', 'remainingTime'));
    }

    public function saveAnswer(Request $request, QuizAttempt $attempt)
    {
        $student = auth()->user()->student;

        if ($attempt->student_id !== $student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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

            if ($question->isMultipleChoice() || $question->isTrueFalse()) {
                $answer->gradeAutomatically();
            }

            return response()->json(['success' => true, 'message' => 'Answer saved successfully', 'answer_id' => $answer->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save answer: ' . $e->getMessage()], 500);
        }
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        $student = auth()->user()->student;

        // Ensure only the student who owns the attempt can submit it
        if (!$student || $attempt->student_id !== $student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        // Prevent re-submitting an already completed quiz
        if ($attempt->isCompleted()) {
            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('info', 'This quiz has already been submitted.');
        }

        try {
            DB::beginTransaction();

            // Auto-grade objective-type questions
            $answers = $attempt->answers()->with('question')->get();
            foreach ($answers as $answer) {
                if ($answer->question->isMultipleChoice() || $answer->question->isTrueFalse()) {
                    $answer->gradeAutomatically();
                }
            }

            $totalScore  = $answers->sum('points_earned');
            $totalPoints = $answers->sum(fn($a) => $a->question->points ?? 0);
            $percentage  = $totalPoints > 0 ? round(($totalScore / $totalPoints) * 100, 2) : 0;

            // Update the attempt record
            $attempt->update([
                'status'        => 'completed',
                'completed_at'  => now(),
                'score'         => $totalScore,
                'total_points'  => $totalPoints,
                'percentage'    => $percentage,
            ]);

            $attempt->complete();
            AuditLog::log('quiz_submitted', $attempt);

            DB::commit();

            $instructor = $attempt->quiz->instructor;
            $settings = $instructor->user->settings ?? (object)[
                'email_quiz_submitted' => true,
                'notification_quiz_submitted' => true,
            ];

            // ✅ Send email if enabled
            if ($settings->email_quiz_submitted) {
                Mail::to($instructor->user->email)
                    ->queue(new QuizSubmittedMail($attempt));
            }

            // ✅ Create notifications if enabled
            if ($settings->notification_quiz_submitted) {
                Notification::create([
                    'user_id'    => $instructor->user_id,
                    'type'       => 'success',
                    'title'      => 'Quiz Submitted',
                    'message'    => "{$attempt->student->full_name} submitted {$attempt->quiz->title}",
                    'action_url' => route('instructor.quizzes.results', $attempt->quiz),
                ]);

                Notification::create([
                    'user_id'    => $attempt->student->user_id,
                    'type'       => $attempt->isPassed() ? 'success' : 'warning',
                    'title'      => 'Quiz Result Available',
                    'message'    => "Your result for '{$attempt->quiz->title}' is {$percentage}%",
                    'action_url' => route('student.quiz-attempts.results', $attempt),
                ]);
            }

            return redirect()
                ->route('student.quiz-attempts.results', $attempt)
                ->with('success', 'Quiz submitted successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Failed to submit quiz. Please try again.');
        }
    }

    public function results(QuizAttempt $attempt)
    {
        $student = auth()->user()->student;

        if ($attempt->student_id !== $student->id) {
            abort(403, 'Unauthorized access to quiz attempt.');
        }

        if (!$attempt->quiz->show_results) {
            return back()->with('error', 'Results are not available for this quiz.');
        }

        $attempt->load([
            'quiz.subject',
            'quiz.instructor.user',
            'answers.question.choices',
            'answers.choice'
        ]);

        // Previous attempts (for progress tracking)
        $previousAttempts = QuizAttempt::where('quiz_id', $attempt->quiz_id)
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('student.quiz-attempts.results', compact('attempt', 'previousAttempts'));
    }

    public function review(Quiz $quiz, QuizAttempt $attempt)
    {
        $student = auth()->user()->student;

        if ($attempt->student_id !== $student->id || $attempt->quiz_id !== $quiz->id) {
            abort(403);
        }

        if (!$quiz->canReview($attempt)) {
            if ($quiz->review_available_after) {
                $waitMinutes = $quiz->review_available_after - $attempt->completed_at->diffInMinutes(now());
                return back()->with('error', "Review will be available in {$waitMinutes} minutes.");
            }
            return back()->with('error', 'Review is not available for this quiz.');
        }

        $attempt->load(['answers.question.choices', 'answers.choice']);

        $questions = $attempt->question_order
            ? $quiz->questions->sortBy(fn($q) => array_search($q->id, $attempt->question_order))
            : $quiz->questions;

        return view('student.quiz-attempts.review', compact('quiz', 'attempt', 'questions'));
    }

    private function autoSubmit(QuizAttempt $attempt)
    {
        try {
            DB::beginTransaction();

            $answers = $attempt->answers()->with('question')->get();
            foreach ($answers as $answer) {
                if ($answer->question->isMultipleChoice() || $answer->question->isTrueFalse()) {
                    $answer->gradeAutomatically();
                }
            }

            $totalScore = $answers->sum('points_earned');
            $totalPoints = $answers->sum(fn($a) => $a->question->points ?? 0);
            $percentage = $totalPoints > 0 ? round(($totalScore / $totalPoints) * 100, 2) : 0;

            $attempt->update([
                'status' => 'completed',
                'completed_at' => now(),
                'score' => $totalScore,
                'total_points' => $totalPoints,
                'percentage' => $percentage,
            ]);

            $attempt->complete();

            AuditLog::log('quiz_attempt_auto_submitted', $attempt, [], ['reason' => 'time_expired']);

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