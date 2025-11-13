<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\Enrollment;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display student progress and grades
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'User is not a student.');
        }
        
        // Get current academic year and semester
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
        
        // Get subject IDs
        $subjectIds = DB::table('instructor_subject_section')
            ->whereIn('section_id', $sectionIds)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->pluck('subject_id')
            ->unique();
        
        $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->get();
        
        // Overall statistics
        $totalQuizzes = Quiz::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->count();
        
        $completedQuizzes = QuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->where('status', 'completed')
            ->distinct('quiz_id')
            ->count();
        
        $overallAverage = QuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;
        
        // Performance by subject
        $subjectPerformance = [];
        foreach ($subjects as $subject) {
            $quizzes = Quiz::where('subject_id', $subject->id)
                ->where('is_published', true)
                ->pluck('id');
            
            $attempts = QuizAttempt::where('student_id', $student->id)
                ->whereIn('quiz_id', $quizzes)
                ->where('status', 'completed')
                ->get();
            
            if ($attempts->count() > 0) {
                $average = $attempts->avg('percentage');
                $passed = $attempts->filter(function($attempt) {
                    return $attempt->percentage >= $attempt->quiz->passing_score;
                })->count();
                
                $subjectPerformance[] = [
                    'subject' => $subject,
                    'total_quizzes' => $quizzes->count(),
                    'completed' => $attempts->unique('quiz_id')->count(),
                    'average' => round($average, 2),
                    'passed' => $passed,
                    'grade' => $this->calculateGrade($average),
                    'color' => $this->getGradeColor($average),
                ];
            }
        }
        
        // Quiz history (last 10 attempts)
        $recentAttempts = QuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->where('status', 'completed')
            ->with(['quiz.subject'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();
        
        // Performance trend (last 6 months)
        $performanceTrend = QuizAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(completed_at, "%Y-%m") as month'),
                DB::raw('AVG(percentage) as average')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Strengths and weaknesses (by Bloom's taxonomy)
        $bloomsPerformance = DB::table('quiz_answers')
            ->join('quiz_attempts', 'quiz_answers.attempt_id', '=', 'quiz_attempts.id')
            ->join('question_bank', 'quiz_answers.question_id', '=', 'question_bank.id')
            ->where('quiz_attempts.student_id', $student->id)
            ->where('quiz_attempts.status', 'completed')
            ->whereNotNull('question_bank.bloom_level')
            ->select(
                'question_bank.bloom_level',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN quiz_answers.is_correct THEN 1 ELSE 0 END) as correct'),
                DB::raw('ROUND(AVG(CASE WHEN quiz_answers.is_correct THEN 100 ELSE 0 END), 2) as percentage')
            )
            ->groupBy('question_bank.bloom_level')
            ->get();
        
        return view('student.progress.index', compact(
            'student',
            'enrollments',
            'totalQuizzes',
            'completedQuizzes',
            'overallAverage',
            'subjectPerformance',
            'recentAttempts',
            'performanceTrend',
            'bloomsPerformance'
        ));
    }
    
    /**
     * Calculate letter grade from percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 97) return 'A+';
        if ($percentage >= 93) return 'A';
        if ($percentage >= 90) return 'A-';
        if ($percentage >= 87) return 'B+';
        if ($percentage >= 83) return 'B';
        if ($percentage >= 80) return 'B-';
        if ($percentage >= 77) return 'C+';
        if ($percentage >= 73) return 'C';
        if ($percentage >= 70) return 'C-';
        if ($percentage >= 67) return 'D+';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
    
    /**
     * Get color for grade
     */
    private function getGradeColor($percentage)
    {
        if ($percentage >= 90) return 'success';
        if ($percentage >= 80) return 'info';
        if ($percentage >= 70) return 'warning';
        return 'danger';
    }
    
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }
}