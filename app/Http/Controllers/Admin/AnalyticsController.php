<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\Lesson;
use App\Models\Quiz;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Analytics dashboard data
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        // User growth
        $userGrowth = User::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Enrollment statistics
        $enrollmentStats = Enrollment::where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "enrolled" THEN 1 ELSE 0 END) as active')
            )
            ->first();

        // Content statistics
        $contentStats = [
            'total_lessons' => Lesson::count(),
            'published_lessons' => Lesson::where('is_published', true)->count(),
            'total_quizzes' => Quiz::count(),
            'published_quizzes' => Quiz::where('is_published', true)->count(),
        ];

        // Performance metrics
        $averageScore = QuizAttempt::where('status', 'completed')->avg('percentage') ?? 0;

        return view('admin.analytics.dashboard', compact(
            'userGrowth',
            'enrollmentStats',
            'contentStats',
            'averageScore',
            'currentAcademicYear',
            'currentSemester'
        ));
    }

    public function performance()
    {
        // Performance analytics
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        // Quiz performance by subject
        $quizPerformance = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->join('subjects', 'quizzes.subject_id', '=', 'subjects.id')
            ->where('quiz_attempts.status', 'completed')
            ->select(
                'subjects.subject_name',
                DB::raw('AVG(quiz_attempts.percentage) as average_score'),
                DB::raw('COUNT(*) as total_attempts')
            )
            ->groupBy('subjects.id', 'subjects.subject_name')
            ->orderBy('average_score', 'desc')
            ->get();

        // Student performance distribution
        $performanceDistribution = QuizAttempt::where('status', 'completed')
            ->select(
                DB::raw('CASE 
                    WHEN percentage >= 90 THEN "Excellent"
                    WHEN percentage >= 80 THEN "Very Good"
                    WHEN percentage >= 70 THEN "Good"
                    WHEN percentage >= 60 THEN "Fair"
                    ELSE "Needs Improvement"
                END as performance_category'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('performance_category')
            ->get();

        return view('admin.analytics.performance', compact(
            'quizPerformance',
            'performanceDistribution',
            'currentAcademicYear',
            'currentSemester'
        ));
    }

    public function trends()
    {
        // Trend analysis
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);

        // Monthly enrollment trends
        $enrollmentTrends = Enrollment::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Monthly quiz attempts
        $quizAttemptTrends = QuizAttempt::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('AVG(percentage) as average_score')
        )
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Content creation trends
        $lessonTrends = Lesson::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $quizTrends = Quiz::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.analytics.trends', compact(
            'enrollmentTrends',
            'quizAttemptTrends',
            'lessonTrends',
            'quizTrends',
            'currentAcademicYear'
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

