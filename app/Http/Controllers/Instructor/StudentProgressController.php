<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\InstructorSubjectSection;
use App\Models\Enrollment;
use App\Services\PerformanceAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentProgressController extends Controller
{
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        // Get current academic year and semester
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        // Get instructor's teaching assignments
        $query = InstructorSubjectSection::with(['subject', 'section.course'])
            ->where('instructor_id', $instructor->id);

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        } else {
            $query->where('academic_year', $currentAcademicYear);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        } else {
            $query->where('semester', $currentSemester);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $assignments = $query->get();

        // Get available academic years
        $academicYears = $this->getAcademicYears();

        // Get subjects taught by instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->get();

        return view('instructor.student-progress.index', compact(
            'assignments', 
            'academicYears', 
            'subjects',
            'currentAcademicYear',
            'currentSemester'
        ));
    }

    public function show(Request $request, Student $student)
    {
        $instructor = auth()->user()->instructor;
        
        // Get current academic year and semester
        $currentAcademicYear = $request->get('academic_year', now()->format('Y') . '-' . (now()->year + 1));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());

        // Verify instructor teaches this student in at least one section
        $commonSections = $this->getCommonSections($instructor->id, $student->id, $currentAcademicYear, $currentSemester);
        
        if ($commonSections->isEmpty()) {
            return back()->with('error', 'You do not teach this student in any section.');
        }

        $student->load(['user', 'course']);

        // Get student's enrollments in instructor's sections
        $enrollments = Enrollment::whereIn('section_id', $commonSections->pluck('id'))
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->with(['section.course'])
            ->get();

        // Get subjects instructor teaches this student
        $teachingAssignments = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->whereIn('section_id', $commonSections->pluck('id'))
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->with('subject')
            ->get();

        $subjects = $teachingAssignments->pluck('subject')->unique('id');

        // Get student's quiz attempts for instructor's quizzes
        $quizIds = Quiz::where('instructor_id', $instructor->id)
            ->whereIn('subject_id', $subjects->pluck('id'))
            ->pluck('id');

        $quizAttempts = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->with(['quiz.subject'])
            ->orderBy('completed_at', 'desc')
            ->get();

        // Calculate statistics
        $totalAttempts = $quizAttempts->count();
        $averageScore = $quizAttempts->avg('percentage') ?? 0;
        $passedAttempts = $quizAttempts->filter(function($attempt) {
            return $attempt->percentage >= $attempt->quiz->passing_score;
        })->count();
        $passRate = $totalAttempts > 0 ? ($passedAttempts / $totalAttempts) * 100 : 0;

        // Performance by subject
        $performanceBySubject = [];
        foreach ($subjects as $subject) {
            $subjectAttempts = $quizAttempts->filter(fn($a) => $a->quiz->subject_id === $subject->id);
            
            if ($subjectAttempts->count() > 0) {
                $performanceBySubject[] = [
                    'subject' => $subject->subject_name,
                    'attempts' => $subjectAttempts->count(),
                    'average' => round($subjectAttempts->avg('percentage'), 2),
                    'passed' => $subjectAttempts->filter(fn($a) => $a->percentage >= $a->quiz->passing_score)->count(),
                ];
            }
        }

        // Recent quiz attempts (detailed)
        $recentAttempts = $quizAttempts->take(10);

        // Strengths and weaknesses (based on Bloom's taxonomy if available)
        $strengths = [];
        $weaknesses = [];
        
        // Get all answers from completed attempts
        $allAnswers = DB::table('quiz_answers')
            ->join('quiz_attempts', 'quiz_answers.attempt_id', '=', 'quiz_attempts.id')
            ->join('question_bank', 'quiz_answers.question_id', '=', 'question_bank.id')
            ->whereIn('quiz_attempts.quiz_id', $quizIds)
            ->where('quiz_attempts.student_id', $student->id)
            ->where('quiz_attempts.status', 'completed')
            ->select(
                'question_bank.blooms_level',
                DB::raw('AVG(CASE WHEN quiz_answers.is_correct THEN 100 ELSE 0 END) as avg_score'),
                DB::raw('COUNT(*) as total_questions')
            )
            ->whereNotNull('question_bank.blooms_level')
            ->groupBy('question_bank.blooms_level')
            ->get();

        foreach ($allAnswers as $level) {
            if ($level->avg_score >= 75) {
                $strengths[] = [
                    'level' => ucfirst($level->blooms_level),
                    'score' => round($level->avg_score, 2),
                    'total' => $level->total_questions
                ];
            } elseif ($level->avg_score < 60) {
                $weaknesses[] = [
                    'level' => ucfirst($level->blooms_level),
                    'score' => round($level->avg_score, 2),
                    'total' => $level->total_questions
                ];
            }
        }

        return view('instructor.student-progress.show', compact(
            'student',
            'enrollments',
            'subjects',
            'quizAttempts',
            'totalAttempts',
            'averageScore',
            'passedAttempts',
            'passRate',
            'performanceBySubject',
            'recentAttempts',
            'strengths',
            'weaknesses',
            'currentAcademicYear',
            'currentSemester'
        ));
    }

    public function export(Request $request, Section $section)
    {
        $instructor = auth()->user()->instructor;
        
        // Verify instructor teaches this section
        $currentAcademicYear = $request->get('academic_year', now()->format('Y') . '-' . (now()->year + 1));
        $currentSemester = $request->get('semester', $this->getCurrentSemester());

        $assignment = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->where('section_id', $section->id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->first();

        if (!$assignment) {
            return back()->with('error', 'You do not teach this section.');
        }

        // Get enrolled students
        $students = Student::whereHas('enrollments', function($q) use ($section, $currentAcademicYear, $currentSemester) {
            $q->where('section_id', $section->id)
              ->where('academic_year', $currentAcademicYear)
              ->where('semester', $currentSemester)
              ->where('status', 'enrolled');
        })->with('user')->get();

        // Get instructor's quizzes for this subject
        $quizzes = Quiz::where('instructor_id', $instructor->id)
            ->where('subject_id', $assignment->subject_id)
            ->where('is_published', true)
            ->get();

        // Prepare CSV data
        $filename = 'student_progress_' . $section->section_name . '_' . now()->format('Y-m-d_His') . '.csv';
        
        $handle = fopen('php://temp', 'w');
        
        // Header row
        $headers = ['Student Number', 'Student Name', 'Email'];
        foreach ($quizzes as $quiz) {
            $headers[] = $quiz->title . ' (Score)';
            $headers[] = $quiz->title . ' (Attempts)';
        }
        $headers[] = 'Average Score';
        $headers[] = 'Total Attempts';
        
        fputcsv($handle, $headers);

        // Data rows
        foreach ($students as $student) {
            $row = [
                $student->student_number,
                $student->full_name,
                $student->user->email,
            ];

            $totalScore = 0;
            $totalAttempts = 0;
            $quizCount = 0;

            foreach ($quizzes as $quiz) {
                $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('student_id', $student->id)
                    ->where('status', 'completed')
                    ->get();

                if ($attempts->count() > 0) {
                    $bestScore = $attempts->max('percentage');
                    $row[] = round($bestScore, 2) . '%';
                    $row[] = $attempts->count();
                    $totalScore += $bestScore;
                    $quizCount++;
                } else {
                    $row[] = 'No attempts';
                    $row[] = '0';
                }

                $totalAttempts += $attempts->count();
            }

            $averageScore = $quizCount > 0 ? round($totalScore / $quizCount, 2) : 0;
            $row[] = $averageScore . '%';
            $row[] = $totalAttempts;

            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    private function getCommonSections($instructorId, $studentId, $academicYear, $semester)
    {
        // Get sections where instructor teaches
        $instructorSections = InstructorSubjectSection::where('instructor_id', $instructorId)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->pluck('section_id')
            ->unique();

        // Get sections where student is enrolled
        $studentSections = Enrollment::where('student_id', $studentId)
            ->where('academic_year', $academicYear)
            ->where('semester', $semester)
            ->where('status', 'enrolled')
            ->pluck('section_id');

        // Find common sections
        $commonSectionIds = $instructorSections->intersect($studentSections);

        return Section::whereIn('id', $commonSectionIds)->get();
    }

    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }

    private function getAcademicYears(): array
    {
        $years = [];
        $currentYear = now()->year;
        for ($i = -1; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = $year . '-' . ($year + 1);
        }
        return $years;
    }


    public function exportStudentPdf(Student $student)
    {
        $this->authorize('viewProgress', $student);
        
        $quizAttempts = QuizAttempt::where('student_id', $student->id)
            ->with(['quiz.subject'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();
        
        $pdf = Pdf::loadView('instructor.student-progress.pdf', compact('student', 'quizAttempts'));
        
        $filename = 'Student_Progress_' . $student->student_number . '_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function alerts(PerformanceAlertService $alertService)
    {
        $instructor = auth()->user()->instructor;
        
        $alerts = $alertService->checkAndGenerateAlerts($instructor);
        
        return view('instructor.student-progress.alerts', compact('alerts'));
    }
    
    /**
     * Dismiss alert notification
     */
    public function dismissAlert(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'alert_type' => ['required', 'in:failing,multiple_failures,no_attempts'],
        ]);
        
        // Mark as acknowledged in session
        $key = "alert_dismissed_{$validated['alert_type']}_{$validated['student_id']}";
        session([$key => now()->addDays(7)]); // Dismiss for 7 days
        
        return response()->json(['success' => true]);
    }
    
}