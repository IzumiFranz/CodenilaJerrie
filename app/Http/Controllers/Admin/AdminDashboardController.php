<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Feedback;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // User Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $adminCount = User::where('role', 'admin')->count();
        $instructorCount = User::where('role', 'instructor')->count();
        $studentCount = User::where('role', 'student')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();

        // Academic Statistics
        $totalCourses = Course::count();
        $activeCourses = Course::where('is_active', true)->count();
        $totalSubjects = Subject::count();
        $activeSubjects = Subject::where('is_active', true)->count();
        $totalSections = Section::count();
        $activeSections = Section::where('is_active', true)->count();

        // Current Academic Year (can be configured in settings)
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        // Enrollment Statistics
        $totalEnrollments = Enrollment::where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->count();
        $activeEnrollments = Enrollment::where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->count();

        // Content Statistics
        $totalLessons = Lesson::count();
        $publishedLessons = Lesson::where('is_published', true)->count();
        $totalQuizzes = Quiz::count();
        $publishedQuizzes = Quiz::where('is_published', true)->count();

        // Quiz Attempts Statistics
        $totalAttempts = QuizAttempt::count();
        $completedAttempts = QuizAttempt::where('status', 'completed')->count();
        $inProgressAttempts = QuizAttempt::where('status', 'in_progress')->count();
        $averageScore = QuizAttempt::where('status', 'completed')->avg('percentage') ?? 0;

        // Feedback Statistics
        $totalFeedback = Feedback::count();
        $pendingFeedback = Feedback::where('status', 'pending')->count();
        $resolvedFeedback = Feedback::where('status', 'responded')->count();

        // Recent Activities (Last 10)
        $recentActivities = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Users (Last 5)
        $recentUsers = User::with([
            'student',
            'instructor',
            'admin'
        ])->orderBy('created_at', 'desc')->limit(5)->get();

        // Enrollments by Course (Top 5)
        $enrollmentsByCourse = Enrollment::select('sections.course_id', DB::raw('count(*) as total'))
            ->join('sections', 'enrollments.section_id', '=', 'sections.id')
            ->where('enrollments.academic_year', $currentAcademicYear)
            ->where('enrollments.semester', $currentSemester)
            ->where('enrollments.status', 'enrolled')
            ->groupBy('sections.course_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Monthly User Registration (Last 6 months)
        $monthlyRegistrations = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // System Health Indicators
        $systemHealth = [
            'storage_used' => $this->getStorageUsage(),
            'database_size' => $this->getDatabaseSize(),
            'cache_status' => $this->getCacheStatus(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'adminCount', 'instructorCount', 'studentCount',
            'inactiveUsers', 'suspendedUsers', 'totalCourses', 'activeCourses',
            'totalSubjects', 'activeSubjects', 'totalSections', 'activeSections',
            'totalEnrollments', 'activeEnrollments', 'totalLessons', 'publishedLessons',
            'totalQuizzes', 'publishedQuizzes', 'totalAttempts', 'completedAttempts',
            'inProgressAttempts', 'averageScore', 'totalFeedback', 'pendingFeedback',
            'resolvedFeedback', 'recentActivities', 'recentUsers', 'enrollmentsByCourse',
            'monthlyRegistrations', 'systemHealth', 'currentAcademicYear', 'currentSemester'
        ));
    }

    /**
     * Get current semester based on month
     */
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

    /**
     * Get storage usage (mock - implement actual logic)
     */
    private function getStorageUsage(): string
    {
        // Implement actual storage calculation
        return '2.5 GB / 10 GB';
    }

    /**
     * Get database size (mock - implement actual logic)
     */
    private function getDatabaseSize(): string
    {
        // Implement actual database size calculation
        return '150 MB';
    }

    /**
     * Get cache status
     */
    private function getCacheStatus(): string
    {
        try {
            cache()->remember('health_check', 1, fn() => true);
            return 'Operational';
        } catch (\Exception $e) {
            return 'Error';
        }
    }
}