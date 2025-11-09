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

        // Get enrolled subjects
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

        $query = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->with(['subject', 'instructor.user'])
            ->withCount('questions');

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $now = now();
            if ($request->status === 'available') {
                $query->where(function($q) use ($now) {
                    $q->whereNull('available_from')->orWhere('available_from', '<=', $now);
                })->where(function($q) use ($now) {
                    $q->whereNull('available_until')->orWhere('available_until', '>=', $now);
                });
            } elseif ($request->status === 'upcoming') {
                $query->where('available_from', '>', $now);
            } elseif ($request->status === 'expired') {
                $query->where('available_until', '<', $now);
            }
        }

        $quizzes = $query->orderBy('available_from', 'desc')->paginate(12);

        // Add attempt info for each quiz
        foreach ($quizzes as $quiz) {
            $quiz->student_attempts = $quiz->attempts()
                ->where('student_id', $student->id)
                ->where('status', 'completed')
                ->count();
            
            $quiz->can_take = $quiz->studentCanTakeQuiz($student);
            
            $quiz->best_score = $quiz->attempts()
                ->where('student_id', $student->id)
                ->where('status', 'completed')
                ->max('percentage');
        }

        return view('student.quizzes.index', compact(
            'quizzes',
            'enrolledSubjects'
        ));
    }

    public function show(Quiz $quiz)
    {
        // Check authorization
        $this->authorize('view', $quiz);

        $user = auth()->user();
        $student = $user->student;

        $quiz->load([
            'subject.course',
            'instructor.user',
            'questions' => function($query) {
                $query->orderBy('quiz_question.order');
            }
        ]);

        // Get student's attempts
        $attempts = $quiz->attempts()
            ->where('student_id', $student->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        // Check if student can take quiz
        $canTake = $quiz->studentCanTakeQuiz($student);

        // Check for in-progress attempt
        $inProgressAttempt = $quiz->attempts()
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        return view('student.quizzes.show', compact(
            'quiz',
            'attempts',
            'canTake',
            'inProgressAttempt'
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