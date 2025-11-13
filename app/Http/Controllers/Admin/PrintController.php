<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Section;
use App\Models\Quiz;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PrintController extends Controller
{
    public function users()
    {
        $users = User::with(['student', 'instructor', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('admin.print.users', compact('users'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('users-list-' . now()->format('Y-m-d') . '.pdf');
    }

    public function enrollments(Section $section)
    {
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        $enrollments = Enrollment::where('section_id', $section->id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->with(['student.user', 'section.course'])
            ->orderBy('student.user.last_name')
            ->get();

        $pdf = PDF::loadView('admin.print.enrollments', compact('enrollments', 'section', 'currentAcademicYear', 'currentSemester'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('enrollments-' . $section->section_name . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function quizResults(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->with(['student.user'])
            ->orderBy('completed_at', 'desc')
            ->get();

        $pdf = PDF::loadView('admin.print.quiz-results', compact('quiz', 'attempts'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('quiz-results-' . $quiz->id . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function attendanceSheet(Section $section)
    {
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        $enrollments = Enrollment::where('section_id', $section->id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->with(['student.user'])
            ->orderBy('student.user.last_name')
            ->get();

        $pdf = PDF::loadView('admin.print.attendance-sheet', compact('enrollments', 'section', 'currentAcademicYear', 'currentSemester'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('attendance-sheet-' . $section->section_name . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function gradeSheet(Section $section)
    {
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        $enrollments = Enrollment::where('section_id', $section->id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->with(['student.user', 'section.subjects.quizzes.attempts' => function($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('student.user.last_name')
            ->get();

        $pdf = PDF::loadView('admin.print.grade-sheet', compact('enrollments', 'section', 'currentAcademicYear', 'currentSemester'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('grade-sheet-' . $section->section_name . '-' . now()->format('Y-m-d') . '.pdf');
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

