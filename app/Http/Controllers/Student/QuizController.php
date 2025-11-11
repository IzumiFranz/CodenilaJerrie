<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        // Get enrolled subjects for the current academic term
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        $enrolledSubjects = $student->sections()
            ->wherePivot('academic_year', $currentAcademicYear)
            ->wherePivot('semester', $currentSemester)
            ->wherePivot('status', 'enrolled')
            ->with('subjects')
            ->get()
            ->pluck('subjects')
            ->flatten()
            ->unique('id');

        // Base query: only published quizzes from enrolled subjects
        $query = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->with([
                'subject', 
                'instructor.user',
                'attempts' => fn($q) => $q->where('student_id', $student->id)
            ])
            ->withCount('questions');

        /** ------------------------------
         *  FILTERS
         *  ------------------------------ */

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by category (if quiz has category)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by difficulty (if quiz has difficulty)
        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by availability status
        if ($request->filled('status')) {
            $status = $request->status;
            $now = now();

            if ($status === 'available') {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('available_from')->orWhere('available_from', '<=', $now);
                })->where(function ($q) use ($now) {
                    $q->whereNull('available_until')->orWhere('available_until', '>=', $now);
                });
            } elseif ($status === 'upcoming') {
                $query->where('available_from', '>', $now);
            } elseif ($status === 'expired') {
                $query->where('available_until', '<', $now);
            } elseif ($status === 'not_attempted') {
                $query->whereDoesntHave('attempts', fn($q) => $q->where('student_id', $student->id));
            } elseif ($status === 'in_progress') {
                $query->whereHas('attempts', fn($q) => $q
                    ->where('student_id', $student->id)
                    ->where('status', 'in_progress'));
            } elseif ($status === 'completed') {
                $query->whereHas('attempts', fn($q) => $q
                    ->where('student_id', $student->id)
                    ->where('status', 'completed'));
            }
        }

        $quizzes = $query->orderBy('available_from', 'desc')->paginate(12);

        /** ------------------------------
         *  STATS + EXTRA INFO
         *  ------------------------------ */

        foreach ($quizzes as $quiz) {
            $quiz->student_attempts = $quiz->attempts()
                ->where('student_id', $student->id)
                ->where('status', 'completed')
                ->count();

            $quiz->best_score = $quiz->attempts()
                ->where('student_id', $student->id)
                ->where('status', 'completed')
                ->max('percentage');

            $quiz->can_take = $quiz->studentCanTakeQuiz($student);
        }

        // Statistics summary
        $totalQuizzes = Quiz::where('is_published', true)
            ->whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->count();

        $completedQuizzes = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->whereHas('attempts', fn($q) => $q->where('student_id', $student->id)->where('status', 'completed'))
            ->count();

        $inProgressQuizzes = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->whereHas('attempts', fn($q) => $q->where('student_id', $student->id)->where('status', 'in_progress'))
            ->count();

        $averageScore = \App\Models\QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $categories = \App\Models\Category::all();

        return view('student.quizzes.index', compact(
            'quizzes',
            'categories',
            'enrolledSubjects',
            'totalQuizzes',
            'completedQuizzes',
            'inProgressQuizzes',
            'averageScore'
        ));
    }

    public function show(Quiz $quiz)
    {
        // Check authorization
        $this->authorize('view', $quiz);

        $user = auth()->user();
        $student = $user->student;

        // Load all relationships needed for display
        $quiz->load([
            'subject.course',
            'instructor.user',
            'lesson',
            'questions' => fn($query) => $query->orderBy('quiz_question.order'),
        ]);

        // All previous attempts (completed)
        $previousAttempts = $quiz->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        // In-progress attempt
        $inProgressAttempt = $quiz->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        $hasInProgressAttempt = !is_null($inProgressAttempt);

        // Retake permission
        $canTakeQuiz = $quiz->studentCanTakeQuiz($student);
        if (!$quiz->allow_retake && $previousAttempts->count() > 0) {
            $canTakeQuiz = false;
        }

        return view('student.quizzes.show', compact(
            'quiz',
            'previousAttempts',
            'hasInProgressAttempt',
            'inProgressAttempt',
            'canTakeQuiz'
        ));
    }

    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }
}