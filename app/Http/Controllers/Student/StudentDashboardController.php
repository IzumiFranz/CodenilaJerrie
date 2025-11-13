<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'User is not a student.');
        }

        // Current Academic Year and Semester
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        // Get active enrollments
        $activeEnrollments = $student->enrollments()
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->with(['section.course', 'section.subjects'])
            ->get();

        // Get enrolled subjects
        $enrolledSubjects = $activeEnrollments
            ->pluck('section.subjects')
            ->flatten()
            ->unique('id');

        // Statistics
        $totalSubjects = $enrolledSubjects->count();
        
        // Available quizzes count
        $availableQuizzes = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->where(function($query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>=', now());
            })
            ->count();

        // Published lessons count
        $availableLessons = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->count();

        // Quiz attempts statistics
        $totalAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        $averageScore = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;

        // Recent quiz attempts (Last 5)
        $recentAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['quiz.subject', 'quiz.instructor.user'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Upcoming quizzes (Next 5)
        $upcomingQuizzes = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->where('available_from', '>', now())
            ->with(['subject', 'instructor.user'])
            ->orderBy('available_from')
            ->limit(5)
            ->get();

        // Recently published lessons (Last 5)
        $recentLessons = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->with(['subject', 'instructor.user'])
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        // Performance by subject
        $performanceBySubject = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->join('subjects', 'quizzes.subject_id', '=', 'subjects.id')
            ->where('quiz_attempts.student_id', $student->id)
            ->where('quiz_attempts.status', 'completed')
            ->whereIn('subjects.id', $enrolledSubjects->pluck('id'))
            ->select(
                'subjects.id',
                'subjects.subject_name',
                DB::raw('AVG(quiz_attempts.percentage) as avg_score'),
                DB::raw('COUNT(quiz_attempts.id) as attempts_count')
            )
            ->groupBy('subjects.id', 'subjects.subject_name')
            ->get();

        // Quizzes needing attention (not attempted yet)
        $pendingQuizzes = Quiz::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->where(function($query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('available_until')
                    ->orWhere('available_until', '>=', now());
            })
            ->whereDoesntHave('attempts', function($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', 'completed');
            })
            ->with(['subject', 'instructor.user'])
            ->orderBy('available_until')
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'currentAcademicYear',
            'currentSemester',
            'activeEnrollments',
            'enrolledSubjects',
            'totalSubjects',
            'availableQuizzes',
            'availableLessons',
            'totalAttempts',
            'averageScore',
            'recentAttempts',
            'upcomingQuizzes',
            'recentLessons',
            'performanceBySubject',
            'pendingQuizzes'
        ));
    }

    private function getCurrentSemester(): string
    {
        $month = now()->month;
        
        if ($month >= 6 && $month <= 10) {
            return '1st';
        } elseif ($month >= 11 || $month <= 3) {
            return '2nd';
        } else {
            return 'summer';
        }
    }
}