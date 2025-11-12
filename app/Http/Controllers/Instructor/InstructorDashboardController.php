<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuestionBank;
use App\Models\InstructorSubjectSection;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InstructorDashboardController extends Controller
{
    public function index()
    {
        $instructor = auth()->user()->instructor;

        // Get current academic year and semester
        $year = now()->year;
        $month = now()->month;
        $currentAcademicYear = ($month >= 6) ? "{$year}-" . ($year + 1) : ($year - 1) . "-{$year}";

        $currentSemester = $this->getCurrentSemester();

        // Content Statistics

        $totalLessons = Lesson::where('instructor_id', $instructor->id)->count();
        $publishedLessons = Lesson::where('instructor_id', $instructor->id)
            ->where('is_published', true)
            ->count();
        $draftLessons = $totalLessons - $publishedLessons;

        $totalQuizzes = Quiz::where('instructor_id', $instructor->id)->count();
        $publishedQuizzes = Quiz::where('instructor_id', $instructor->id)
            ->where('is_published', true)
            ->count();
        $draftQuizzes = $totalQuizzes - $publishedQuizzes;

        $totalQuestions = QuestionBank::where('instructor_id', $instructor->id)->count();

        // Teaching Assignments
        $assignments = InstructorSubjectSection::with(['subject', 'section.course'])
            ->where('instructor_id', $instructor->id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->get();

        $totalSections = $assignments->count();
        $totalSubjects = $assignments->pluck('subject_id')->unique()->count();

        // Student Statistics
        $totalStudents = 0;
        foreach ($assignments as $assignment) {
            $totalStudents += Enrollment::where('section_id', $assignment->section_id)
                ->where('academic_year', $currentAcademicYear)
                ->where('semester', $currentSemester)
                ->where('status', 'enrolled')
                ->count();
        }

        // Quiz Attempts Statistics
        $quizIds = Quiz::where('instructor_id', $instructor->id)->pluck('id');
        $totalAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)->count();
        $completedAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->where('status', 'completed')
            ->count();
        $inProgressAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->where('status', 'in_progress')
            ->count();
        $averageScore = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;

            // FEATURE 1: Quick Stats
        $classAverage = $averageScore;

        $hardestQuiz = Quiz::where('instructor_id', $instructor->id)
            ->withAvg(['attempts as avg_score' => fn($q) => $q->where('status', 'completed')], 'percentage')
            ->orderBy('avg_score')
            ->first();

        $popularLesson = Lesson::where('instructor_id', $instructor->id)
            ->orderBy('view_count', 'desc')
            ->first();

        $strugglingStudents = Student::whereHas('enrollments', function ($q) use ($assignments) {
             $q->whereIn('section_id', $assignments->pluck('section_id'));
        })
            ->withAvg(['quizAttempts as avg_score' => function ($q) use ($quizIds) {
                $q->whereIn('quiz_id', $quizIds)->where('status', 'completed');
            }], 'percentage')
            ->having('avg_score', '<', 50)
            ->orderBy('avg_score', 'asc')
            ->limit(5)
            ->get();


        // Recent Lessons
        $recentLessons = Lesson::where('instructor_id', $instructor->id)
            ->with('subject')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Quizzes
        $recentQuizzes = Quiz::where('instructor_id', $instructor->id)
            ->with('subject')
            ->withCount('attempts')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Quiz Attempts
        $recentAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->with(['student.user', 'quiz'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Subjects taught
        $subjectIds = $assignments->pluck('subject_id')->unique();
        $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->get();

        // Performance by Subject (Quiz average scores)
        $performanceBySubject = [];
        foreach ($subjects as $subject) {
            $subjectQuizIds = Quiz::where('instructor_id', $instructor->id)
                ->where('subject_id', $subject->id)
                ->pluck('id');
            
            $avgScore = QuizAttempt::whereIn('quiz_id', $subjectQuizIds)
                ->where('status', 'completed')
                ->avg('percentage') ?? 0;
            
            $performanceBySubject[] = [
                'subject' => $subject->subject_name,
                'average' => round($avgScore, 2)
            ];
        'quiz_templates' => QuizTemplate::where('instructor_id', $instructor->id)->count(),
        'recent_quiz_attempts' => $this->getRecentAttempts($instructor),
        'chart_data' => $this->getChartData($instructor),
        }
        // ADD THESE AI STATS:
    $stats['ai_stats'] = [
        'questions_generated' => AIJob::where('user_id', auth()->id())
            ->where('job_type', 'generate_questions')
            ->where('status', 'completed')
            ->count(),
        
        'questions_validated' => AIJob::where('user_id', auth()->id())
            ->where('job_type', 'validate_question')
            ->where('status', 'completed')
            ->count(),
        
        'quizzes_analyzed' => AIJob::where('user_id', auth()->id())
            ->where('job_type', 'analyze_quiz')
            ->where('status', 'completed')
            ->count(),
        
        'pending_jobs' => AIJob::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->count(),
    ];

        return view('instructor.dashboard', compact(
            'totalLessons', 'publishedLessons', 'draftLessons',
            'totalQuizzes', 'publishedQuizzes', 'draftQuizzes',
            'totalQuestions', 'totalSections', 'totalSubjects', 'totalStudents',
            'totalAttempts', 'completedAttempts', 'inProgressAttempts', 'averageScore',
            'recentLessons', 'recentQuizzes', 'recentAttempts', 'assignments',
            'subjects', 'performanceBySubject', 'currentAcademicYear', 'currentSemester', 
            'classAverage', 'hardestQuiz', 'popularLesson', 'strugglingStudents', 'ai_stats'
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