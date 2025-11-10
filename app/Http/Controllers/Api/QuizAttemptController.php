<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Quiz, QuizAttempt, Question, StudentAnswer};
use Illuminate\Support\Facades\DB;

class QuizAttemptController extends Controller
{
    /**
     * Start a quiz attempt
     */
    public function start(Request $request, Quiz $quiz)
    {
        $student = $request->user()->student;

        if (!$quiz->isAvailableToStudent($student)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this quiz',
            ], 403);
        }

        // Prevent multiple active attempts
        $activeAttempt = QuizAttempt::where('student_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            return response()->json([
                'success' => true,
                'data' => $activeAttempt->load('quiz'),
            ]);
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz started successfully',
            'data' => $attempt->load('quiz'),
        ]);
    }

    /**
     * Show a specific attempt (questions and answers)
     */
    public function show(Request $request, QuizAttempt $attempt)
    {
        $student = $request->user()->student;

        if ($attempt->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this attempt',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $attempt->load(['quiz.questions.choices', 'answers']),
        ]);
    }

    /**
     * Save a student's answer
     */
    public function saveAnswer(Request $request, QuizAttempt $attempt)
    {
        $student = $request->user()->student;

        if ($attempt->student_id !== $student->id || $attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'You cannot modify this attempt',
            ], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'choice_id' => 'nullable|exists:choices,id',
            'answer_text' => 'nullable|string',
        ]);

        DB::transaction(function () use ($attempt, $student, $validated) {
            StudentAnswer::updateOrCreate(
                [
                    'quiz_attempt_id' => $attempt->id,
                    'question_id' => $validated['question_id'],
                ],
                [
                    'choice_id' => $validated['choice_id'] ?? null,
                    'answer_text' => $validated['answer_text'] ?? null,
                ]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Answer saved successfully',
        ]);
    }

    /**
     * Submit attempt for grading
     */
    public function submit(Request $request, QuizAttempt $attempt)
    {
        $student = $request->user()->student;

        if ($attempt->student_id !== $student->id || $attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'You cannot submit this attempt',
            ], 403);
        }

        DB::transaction(function () use ($attempt) {
            $attempt->status = 'completed';
            $attempt->completed_at = now();
            $attempt->calculateScore();
            $attempt->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Quiz submitted successfully',
            'data' => $attempt->fresh(['quiz', 'results']),
        ]);
    }

    /**
     * Get results summary
     */
    public function results(Request $request, QuizAttempt $attempt)
    {
        $student = $request->user()->student;

        if ($attempt->student_id !== $student->id || $attempt->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Results are not available yet',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'score' => $attempt->score,
                'percentage' => $attempt->percentage,
                'passed' => $attempt->isPassed(),
                'details' => $attempt->load('quiz.questions.choices', 'answers'),
            ],
        ]);
    }

    /**
     * Review attempt answers with correct responses
     */
    public function review(Request $request, QuizAttempt $attempt)
    {
        $student = $request->user()->student;

        if ($attempt->student_id !== $student->id || $attempt->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You cannot review this attempt yet',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $attempt->load([
                'quiz.questions.choices',
                'answers.choice',
                'quiz.subject'
            ]),
        ]);
    }
}
