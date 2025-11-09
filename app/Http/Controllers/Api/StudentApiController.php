<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Quiz, QuizAttempt, Lesson};

class StudentApiController extends Controller
{
    /**
     * 📊 Get student dashboard stats
     */
    public function stats(Request $request)
    {
        $student = $request->user()->student;

        $stats = [
            'enrolled_subjects' => $student->enrollments()
                ->where('status', 'enrolled')
                ->count(),

            'available_quizzes' => Quiz::whereHas('subject', function($q) use ($student) {
                $q->whereIn('id', $student->getEnrolledSubjectIds());
            })
            ->where('is_published', true)
            ->count(),

            'total_attempts' => QuizAttempt::where('student_id', $student->id)
                ->where('status', 'completed')
                ->count(),

            'average_score' => QuizAttempt::where('student_id', $student->id)
                ->where('status', 'completed')
                ->avg('percentage') ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * 🧾 List available quizzes for student
     */
    public function quizzes(Request $request)
    {
        $student = $request->user()->student;

        $quizzes = Quiz::whereHas('subject', function($q) use ($student) {
                $q->whereIn('id', $student->getEnrolledSubjectIds());
            })
            ->where('is_published', true)
            ->with(['subject', 'instructor.user'])
            ->withCount('questions')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $quizzes
        ]);
    }

    /**
     * 📚 List available lessons
     */
    public function lessons(Request $request)
    {
        $student = $request->user()->student;

        $lessons = Lesson::whereHas('subject', function($q) use ($student) {
                $q->whereIn('id', $student->getEnrolledSubjectIds());
            })
            ->where('is_published', true)
            ->with(['subject', 'instructor.user'])
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $lessons
        ]);
    }

    /**
     * 🧠 Start a quiz attempt
     */
    public function attemptQuiz(Request $request, Quiz $quiz)
    {
        $student = $request->user()->student;

        // Authorization check
        if (!$quiz->studentCanTakeQuiz($student)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot take this quiz.'
            ], 403);
        }

        // Determine attempt number
        $attemptNumber = $quiz->attempts()
            ->where('student_id', $student->id)
            ->max('attempt_number') + 1;

        // Create a new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'attempt_number' => $attemptNumber,
            'total_points' => $quiz->getTotalPoints(),
            'question_order' => $quiz->questions->pluck('id')->toArray(),
            'ip_address' => $request->ip(),
        ]);

        $attempt->start();

        return response()->json([
            'success' => true,
            'data' => $attempt->load('quiz.questions.choices')
        ]);
    }
}
