<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export users to CSV
     */
    public function exportUsers(Request $request)
    {
        $query = User::with(['admin', 'instructor', 'student']);

        // Apply filters from request
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $filename = 'users_export_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Headers
        fputcsv($handle, [
            'ID', 'Username', 'Email', 'Role', 'Status', 
            'First Name', 'Last Name', 'Phone', 'Created At'
        ]);

        // Data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->username,
                $user->email,
                ucfirst($user->role),
                ucfirst($user->status),
                $user->profile->first_name ?? '',
                $user->profile->last_name ?? '',
                $user->profile->phone ?? '',
                $user->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Export courses to CSV
     */
    public function exportCourses(Request $request)
    {
        $query = Course::withCount(['subjects', 'students']);

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $courses = $query->orderBy('course_name')->get();

        $filename = 'courses_export_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [
            'ID', 'Course Code', 'Course Name', 'Description', 
            'Max Years', 'Subjects Count', 'Students Count', 'Status', 'Created At'
        ]);

        foreach ($courses as $course) {
            fputcsv($handle, [
                $course->id,
                $course->course_code,
                $course->course_name,
                $course->description,
                $course->max_years,
                $course->subjects_count,
                $course->students_count,
                $course->is_active ? 'Active' : 'Inactive',
                $course->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Export enrollments to CSV
     */
    public function exportEnrollments(Request $request)
    {
        $query = Enrollment::with(['student.user', 'student.course', 'section.course']);

        // Apply filters
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->get();

        $filename = 'enrollments_export_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [
            'ID', 'Student Number', 'Student Name', 'Email', 'Course', 
            'Section', 'Year Level', 'Academic Year', 'Semester', 'Status', 
            'Enrollment Date'
        ]);

        foreach ($enrollments as $enrollment) {
            fputcsv($handle, [
                $enrollment->id,
                $enrollment->student->student_number,
                $enrollment->student->first_name . ' ' . $enrollment->student->last_name,
                $enrollment->student->user->email,
                $enrollment->student->course->course_code,
                $enrollment->section->section_name,
                $enrollment->section->year_level,
                $enrollment->academic_year,
                $enrollment->semester,
                ucfirst($enrollment->status),
                $enrollment->enrollment_date,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Export quiz results to CSV
     */
    public function exportQuizResults($quizId)
    {
        $quiz = \App\Models\Quiz::with('questions')->findOrFail($quizId);
        $attempts = QuizAttempt::with('student.user')
            ->where('quiz_id', $quizId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();

        $filename = 'quiz_results_' . str_replace(' ', '_', $quiz->title) . '_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        fputcsv($handle, [
            'Student Number', 'Student Name', 'Email', 'Score', 
            'Total Points', 'Percentage', 'Status', 'Time Taken (minutes)', 
            'Started At', 'Completed At'
        ]);

        foreach ($attempts as $attempt) {
            $timeTaken = $attempt->started_at && $attempt->completed_at 
                ? \Carbon\Carbon::parse($attempt->started_at)->diffInMinutes($attempt->completed_at)
                : 'N/A';

            fputcsv($handle, [
                $attempt->student->student_number,
                $attempt->student->first_name . ' ' . $attempt->student->last_name,
                $attempt->student->user->email,
                $attempt->score,
                $quiz->questions->sum('points'),
                number_format($attempt->percentage, 2) . '%',
                $attempt->percentage >= $quiz->passing_score ? 'Passed' : 'Failed',
                $timeTaken,
                $attempt->started_at ? \Carbon\Carbon::parse($attempt->started_at)->format('Y-m-d H:i:s') : '',
                $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at)->format('Y-m-d H:i:s') : '',
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    /**
     * Generate PDF Report (Example - Users Report)
     */
    public function generateUsersPDF(Request $request)
    {
        $query = User::with(['admin', 'instructor', 'student']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        // Generate HTML for PDF
        $html = view('admin.exports.users-pdf', compact('users'))->render();

        // You can use libraries like dompdf, mpdf, or snappy
        // For this example, we'll return the HTML that can be printed as PDF
        return response($html)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Generate Comprehensive Analytics Report
     */
    public function generateAnalyticsReport(Request $request)
    {
        $academicYear = $request->get('academic_year', now()->format('Y') . '-' . (now()->year + 1));
        $semester = $request->get('semester', $this->getCurrentSemester());

        $data = [
            'academic_year' => $academicYear,
            'semester' => $semester,
            'total_users' => User::count(),
            'active_students' => User::where('role', 'student')->where('status', 'active')->count(),
            'active_instructors' => User::where('role', 'instructor')->where('status', 'active')->count(),
            'total_courses' => Course::count(),
            'total_subjects' => Subject::count(),
            'total_enrollments' => Enrollment::where('academic_year', $academicYear)
                ->where('semester', $semester)
                ->where('status', 'enrolled')
                ->count(),
            'quiz_completion_rate' => $this->getQuizCompletionRate($academicYear, $semester),
            'average_quiz_score' => QuizAttempt::where('status', 'completed')->avg('percentage') ?? 0,
            'top_performing_students' => $this->getTopPerformingStudents($academicYear, $semester),
            'enrollment_by_course' => $this->getEnrollmentByCourse($academicYear, $semester),
        ];

        $filename = 'analytics_report_' . $academicYear . '_' . $semester . '_' . now()->format('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');

        // Summary Section
        fputcsv($handle, ['ANALYTICS REPORT']);
        fputcsv($handle, ['Academic Year', $academicYear]);
        fputcsv($handle, ['Semester', $semester]);
        fputcsv($handle, ['Generated', now()->format('Y-m-d H:i:s')]);
        fputcsv($handle, []);

        // Statistics
        fputcsv($handle, ['OVERALL STATISTICS']);
        fputcsv($handle, ['Metric', 'Value']);
        fputcsv($handle, ['Total Users', $data['total_users']]);
        fputcsv($handle, ['Active Students', $data['active_students']]);
        fputcsv($handle, ['Active Instructors', $data['active_instructors']]);
        fputcsv($handle, ['Total Courses', $data['total_courses']]);
        fputcsv($handle, ['Total Subjects', $data['total_subjects']]);
        fputcsv($handle, ['Total Enrollments', $data['total_enrollments']]);
        fputcsv($handle, ['Quiz Completion Rate', number_format($data['quiz_completion_rate'], 2) . '%']);
        fputcsv($handle, ['Average Quiz Score', number_format($data['average_quiz_score'], 2) . '%']);
        fputcsv($handle, []);

        // Top Performing Students
        fputcsv($handle, ['TOP PERFORMING STUDENTS']);
        fputcsv($handle, ['Rank', 'Student Number', 'Name', 'Average Score']);
        foreach ($data['top_performing_students'] as $index => $student) {
            fputcsv($handle, [
                $index + 1,
                $student->student_number,
                $student->first_name . ' ' . $student->last_name,
                number_format($student->average_score, 2) . '%'
            ]);
        }
        fputcsv($handle, []);

        // Enrollment by Course
        fputcsv($handle, ['ENROLLMENT BY COURSE']);
        fputcsv($handle, ['Course Code', 'Course Name', 'Enrollment Count']);
        foreach ($data['enrollment_by_course'] as $enrollment) {
            fputcsv($handle, [
                $enrollment->course_code,
                $enrollment->course_name,
                $enrollment->enrollment_count
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    // Helper methods
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }

    private function getQuizCompletionRate($academicYear, $semester): float
    {
        $totalAttempts = QuizAttempt::count();
        $completedAttempts = QuizAttempt::where('status', 'completed')->count();
        
        return $totalAttempts > 0 ? ($completedAttempts / $totalAttempts) * 100 : 0;
    }

    private function getTopPerformingStudents($academicYear, $semester, $limit = 10)
    {
        return \App\Models\Student::select('students.*')
            ->selectRaw('AVG(quiz_attempts.percentage) as average_score')
            ->join('quiz_attempts', 'students.id', '=', 'quiz_attempts.student_id')
            ->where('quiz_attempts.status', 'completed')
            ->groupBy('students.id')
            ->orderBy('average_score', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getEnrollmentByCourse($academicYear, $semester)
    {
        return Course::select('courses.course_code', 'courses.course_name')
            ->selectRaw('COUNT(enrollments.id) as enrollment_count')
            ->join('students', 'courses.id', '=', 'students.course_id')
            ->join('enrollments', 'students.id', '=', 'enrollments.student_id')
            ->where('enrollments.academic_year', $academicYear)
            ->where('enrollments.semester', $semester)
            ->where('enrollments.status', 'enrolled')
            ->groupBy('courses.id', 'courses.course_code', 'courses.course_name')
            ->orderBy('enrollment_count', 'desc')
            ->get();
    }
}