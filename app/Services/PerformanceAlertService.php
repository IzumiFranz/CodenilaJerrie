<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Instructor;
use App\Models\Notification;
use Illuminate\Support\Collection;

class PerformanceAlertService
{
    const ALERT_THRESHOLD_FAILING = 60; // Below 60%
    const ALERT_THRESHOLD_LOW_ATTEMPTS = 2; // Failed 2 or more times
    const ALERT_THRESHOLD_NO_ATTEMPTS = 3; // No attempt for 3 days after quiz published
    
    public function checkAndGenerateAlerts(Instructor $instructor): array
    {
        $alerts = [
            'failing_students' => $this->findFailingStudents($instructor),
            'multiple_failures' => $this->findMultipleFailures($instructor),
            'no_attempts' => $this->findNoAttempts($instructor),
            'improved_students' => $this->findImprovedStudents($instructor),
        ];
        
        // Generate notifications for instructor
        $this->generateNotifications($instructor, $alerts);
        
        return $alerts;
    }
    
    private function findFailingStudents(Instructor $instructor): Collection
    {
        $quizIds = Quiz::where('instructor_id', $instructor->id)
            ->where('is_published', true)
            ->pluck('id');
        
        $failingAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->where('status', 'completed')
            ->whereRaw('percentage < (SELECT passing_score FROM quizzes WHERE id = quiz_attempts.quiz_id)')
            ->with(['student.user', 'quiz'])
            ->latest('completed_at')
            ->get()
            ->groupBy('student_id')
            ->map(function($attempts) {
                return [
                    'student' => $attempts->first()->student,
                    'recent_failures' => $attempts->take(3),
                    'failure_count' => $attempts->count(),
                    'average_score' => round($attempts->avg('percentage'), 2),
                ];
            })
            ->values();
        
        return $failingAttempts;
    }
    
    private function findMultipleFailures(Instructor $instructor): Collection
    {
        $quizIds = Quiz::where('instructor_id', $instructor->id)
            ->where('is_published', true)
            ->pluck('id');
        
        $multipleFailures = QuizAttempt::selectRaw('student_id, quiz_id, COUNT(*) as attempt_count, AVG(percentage) as avg_score')
            ->whereIn('quiz_id', $quizIds)
            ->where('status', 'completed')
            ->whereRaw('percentage < (SELECT passing_score FROM quizzes WHERE id = quiz_attempts.quiz_id)')
            ->groupBy('student_id', 'quiz_id')
            ->having('attempt_count', '>=', self::ALERT_THRESHOLD_LOW_ATTEMPTS)
            ->with(['student.user', 'quiz'])
            ->get()
            ->map(function($record) {
                return [
                    'student' => $record->student,
                    'quiz' => $record->quiz,
                    'attempt_count' => $record->attempt_count,
                    'average_score' => round($record->avg_score, 2),
                ];
            });
        
        return $multipleFailures;
    }
    
    private function findNoAttempts(Instructor $instructor): Collection
    {
        $cutoffDate = now()->subDays(self::ALERT_THRESHOLD_NO_ATTEMPTS);
        
        $publishedQuizzes = Quiz::where('instructor_id', $instructor->id)
            ->where('is_published', true)
            ->where('published_at', '<=', $cutoffDate)
            ->whereDoesntHave('available_until', function($q) {
                $q->where('available_until', '<', now());
            })
            ->get();
        
        $noAttempts = collect();
        
        foreach ($publishedQuizzes as $quiz) {
            $enrolledStudents = $this->getEnrolledStudents($instructor, $quiz->subject_id);
            
            foreach ($enrolledStudents as $student) {
                $hasAttempt = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $student->id)
                    ->exists();
                
                if (!$hasAttempt) {
                    $noAttempts->push([
                        'student' => $student,
                        'quiz' => $quiz,
                        'days_since_publish' => $quiz->published_at->diffInDays(now()),
                    ]);
                }
            }
        }
        
        return $noAttempts->groupBy('student.id')
            ->map(function($items, $studentId) {
                return [
                    'student' => $items->first()['student'],
                    'missed_quizzes' => $items->pluck('quiz'),
                    'count' => $items->count(),
                ];
            })
            ->values();
    }
    
    private function findImprovedStudents(Instructor $instructor): Collection
    {
        $quizIds = Quiz::where('instructor_id', $instructor->id)
            ->where('is_published', true)
            ->pluck('id');
        
        $improvements = collect();
        
        $students = $this->getEnrolledStudents($instructor);
        
        foreach ($students as $student) {
            $recentAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
                ->where('student_id', $student->id)
                ->where('status', 'completed')
                ->where('completed_at', '>=', now()->subWeek())
                ->orderBy('completed_at')
                ->get();
            
            if ($recentAttempts->count() >= 2) {
                $firstScore = $recentAttempts->first()->percentage;
                $lastScore = $recentAttempts->last()->percentage;
                $improvement = $lastScore - $firstScore;
                
                if ($improvement >= 20) { // 20% or more improvement
                    $improvements->push([
                        'student' => $student,
                        'improvement' => round($improvement, 2),
                        'from_score' => round($firstScore, 2),
                        'to_score' => round($lastScore, 2),
                        'attempts_count' => $recentAttempts->count(),
                    ]);
                }
            }
        }
        
        return $improvements->sortByDesc('improvement')->values();
    }
    
    private function generateNotifications(Instructor $instructor, array $alerts): void
    {
        $userId = $instructor->user->id;
        $totalAlerts = $alerts['failing_students']->count() + 
                       $alerts['multiple_failures']->count() + 
                       $alerts['no_attempts']->count();
        
        if ($totalAlerts > 0) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'warning',
                'title' => 'Student Performance Alerts',
                'message' => "You have {$totalAlerts} student(s) requiring attention. Check the Student Progress section for details.",
                'action_url' => route('instructor.student-progress.alerts'),
            ]);
        }
        
        // Positive notification for improvements
        if ($alerts['improved_students']->count() > 0) {
            $count = $alerts['improved_students']->count();
            Notification::create([
                'user_id' => $userId,
                'type' => 'success',
                'title' => 'Student Improvements',
                'message' => "{$count} student(s) have shown significant improvement this week!",
                'action_url' => route('instructor.student-progress.alerts'),
            ]);
        }
    }
    
    private function getEnrolledStudents(Instructor $instructor, ?int $subjectId = null)
    {
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();
        
        $query = Student::whereHas('enrollments', function($q) use ($instructor, $currentAcademicYear, $currentSemester, $subjectId) {
            $q->where('academic_year', $currentAcademicYear)
              ->where('semester', $currentSemester)
              ->where('status', 'enrolled')
              ->whereHas('section.assignments', function($a) use ($instructor, $subjectId) {
                  $a->where('instructor_id', $instructor->id);
                  if ($subjectId) {
                      $a->where('subject_id', $subjectId);
                  }
              });
        })->with('user');
        
        return $query->get();
    }
    
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }
}