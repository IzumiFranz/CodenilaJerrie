<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Lesson;
use App\Models\Enrollment;

class StudentApiController extends Controller
{
    /**
     * Get student dashboard data
     */
    public function dashboard(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found'
            ], 404);
        }
        
        // Current academic year and semester
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();
        
        // Get enrollments
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->with(['section.course'])
            ->get();
        
        $sectionIds = $enrollments->pluck('section_id');
        
        // Get subject IDs from enrolled sections
        $subjectIds = \DB::table('instructor_subject_section')
            ->whereIn('section_id', $sectionIds)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->pluck('subject_id')
            ->unique();
        
        // Statistics
        $totalLessons = Lesson::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->count();
        
        $totalQuizzes = Quiz::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->count();
        
        $completedAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();
        
        $averageScore = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;
        
        // Recent activities
        $recentLessons = Lesson::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->with(['subject', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentQuizzes = Quiz::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->with(['subject', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentAttempts = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['quiz.subject'])
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student->load(['user', 'course']),
                'statistics' => [
                    'enrolled_sections' => $enrollments->count(),
                    'total_subjects' => $subjectIds->count(),
                    'total_lessons' => $totalLessons,
                    'total_quizzes' => $totalQuizzes,
                    'completed_attempts' => $completedAttempts,
                    'average_score' => round($averageScore, 2),
                ],
                'enrollments' => $enrollments,
                'recent_lessons' => $recentLessons,
                'recent_quizzes' => $recentQuizzes,
                'recent_attempts' => $recentAttempts,
            ]
        ]);
    }
    
    /**
     * Get student notifications
     */
    public function notifications(Request $request)
    {
        $notifications = $request->user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationRead(Request $request, $notificationId)
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        
        $notification->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }
    
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }
}