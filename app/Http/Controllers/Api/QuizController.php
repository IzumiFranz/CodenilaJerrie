<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Quiz, QuizAttempt};

class QuizController extends Controller
{
    /**
     * List all available quizzes for the student
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;

        $sectionIds = $student->enrollments()
            ->where('status', 'enrolled')
            ->pluck('section_id');

        $subjectIds = \DB::table('instructor_subject_section')
            ->whereIn('section_id', $sectionIds)
            ->pluck('subject_id')
            ->unique();

        $query = Quiz::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->with(['subject', 'instructor'])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Subject filter
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $quizzes = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $quizzes,
        ]);
    }

    /**
     * Show quiz details and previous attempts
     */
    public function show(Request $request, Quiz $quiz)
    {
        $student = $request->user()->student;

        if (!$quiz->isAvailableToStudent($student)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this quiz',
            ], 403);
        }

        $previousAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('quiz_id', $quiz->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz->load(['subject', 'instructor']),
                'previous_attempts' => $previousAttempts,
            ],
        ]);
    }
}
