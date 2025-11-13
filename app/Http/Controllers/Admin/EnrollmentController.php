<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Section;
use App\Models\Course;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;
use App\Mail\EnrollmentConfirmationMail;
use App\Mail\FeedbackResponseMail;use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with(['student.user', 'student.course', 'section.course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->whereHas('section', function($q) use ($request) {
                $q->where('course_id', $request->course_id);
            });
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->orderBy('created_at', 'desc')->paginate(20);
        $courses = Course::where('is_active', true)->get();

        // Get available academic years (last 3 years)
        $academicYears = $this->getAcademicYears();

        return view('admin.enrollments.index', compact('enrollments', 'courses', 'academicYears'));
    }

    public function create()
    {
        $students = Student::with('user', 'course')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->get();
        $courses = Course::where('is_active', true)->get();
        $academicYears = $this->getAcademicYears();

        return view('admin.enrollments.create', compact('students', 'courses', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'academic_year' => ['required', 'string'],
            'semester' => ['required', 'in:1st,2nd,summer'],
            'enrollment_date' => ['required', 'date'],
        ]);

        try {
            // Check for duplicate enrollment
            $exists = Enrollment::where('student_id', $validated['student_id'])
                ->where('section_id', $validated['section_id'])
                ->where('academic_year', $validated['academic_year'])
                ->where('semester', $validated['semester'])
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Student is already enrolled in this section.');
            }

            // Check section capacity
            $section = Section::findOrFail($validated['section_id']);
            if (!$section->hasAvailableSlots($validated['academic_year'], $validated['semester'])) {
                return back()->withInput()->with('error', 'Section is full.');
            }

            $validated['status'] = 'enrolled';
            $enrollment = Enrollment::create($validated);

            AuditLog::log('enrollment_created', $enrollment);

            $studentSettings = $enrollment->student->user->settings ?? (object)[
                'email_enrollment' => true,
                'notification_enrollment' => true,
            ];

            if ($studentSettings->email_enrollment) {
                Mail::to($enrollment->student->user->email)
                    ->queue(new EnrollmentConfirmationMail($enrollment));
            }

            if ($studentSettings->notification_enrollment) {
                Notification::create([
                    'user_id'    => $enrollment->student->user_id,
                    'type'       => 'success',
                    'title'      => 'Enrollment Confirmed',
                    'message'    => "You have been enrolled in {$enrollment->section->full_name}.",
                    'action_url' => route('student.dashboard'),
                ]);
            }

            // =====================================
            // 👨‍🏫 Notify the INSTRUCTOR
            // =====================================
            $instructor = $enrollment->section->assignments()
                ->where('subject_id', $enrollment->section->subject_id)
                ->first()?->instructor;

            if ($instructor) {
                $instructorSettings = $instructor->user->settings ?? (object)[
                    'email_student_enrolled' => true,
                    'notification_student_enrolled' => true,
                ];

                if ($instructorSettings->email_student_enrolled) {
                    Mail::to($instructor->user->email)
                        ->queue(new StudentEnrolledMail($enrollment));
                }

                if ($instructorSettings->notification_student_enrolled) {
                    Notification::create([
                        'user_id'    => $instructor->user_id,
                        'type'       => 'info',
                        'title'      => 'New Student Enrolled',
                        'message'    => "{$enrollment->student->full_name} enrolled in {$enrollment->section->full_name}",
                        'action_url' => route('instructor.student-progress.show', $enrollment->student),
                    ]);
                }
            }

            // ✅ Final redirect (after everything runs)
            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', 'Student enrolled successfully.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to enroll student: ' . $e->getMessage());
        }
    }


    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student.user', 'student.course', 'section.course', 'section.subjects']);
        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(Enrollment $enrollment)
    {
        $students = Student::with('user', 'course')->get();
        $courses = Course::where('is_active', true)->get();
        $sections = Section::where('course_id', $enrollment->section->course_id)
            ->where('is_active', true)
            ->get();
        $academicYears = $this->getAcademicYears();

        return view('admin.enrollments.edit', compact('enrollment', 'students', 'courses', 'sections', 'academicYears'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validated = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'academic_year' => ['required', 'string'],
            'semester' => ['required', 'in:1st,2nd,summer'],
            'status' => ['required', 'in:enrolled,dropped,completed'],
            'enrollment_date' => ['required', 'date'],
        ]);

        try {
            $oldValues = $enrollment->toArray();
            $enrollment->update($validated);

            AuditLog::log('enrollment_updated', $enrollment, $oldValues, $enrollment->toArray());

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', 'Enrollment updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update enrollment: ' . $e->getMessage());
        }
    }

    public function destroy(Enrollment $enrollment)
    {
        try {
            AuditLog::log('enrollment_deleted', $enrollment);
            $enrollment->delete();

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', 'Enrollment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete enrollment: ' . $e->getMessage());
        }
    }

    public function drop(Enrollment $enrollment)
    {
        try {
            $enrollment->drop();

            AuditLog::log('enrollment_dropped', $enrollment);

            return back()->with('success', 'Student dropped from section successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to drop student: ' . $e->getMessage());
        }
    }

    public function trashed()
    {
        $enrollments = Enrollment::onlyTrashed()
            ->with(['student.user', 'section.course'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.enrollments.trashed', compact('enrollments'));
    }

    public function restore($id)
    {
        try {
            $enrollment = Enrollment::onlyTrashed()->findOrFail($id);
            $enrollment->restore();

            AuditLog::log('enrollment_restored', $enrollment);

            return redirect()
                ->route('admin.enrollments.index')
                ->with('success', 'Enrollment restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore enrollment: ' . $e->getMessage());
        }
    }

    public function bulkEnroll(Request $request)
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'academic_year' => ['required', 'string'],
            'semester' => ['required', 'in:1st,2nd,summer'],
        ]);

        try {
            $file = $request->file('csv_file');
            $csv = array_map('str_getcsv', file($file));
            $headers = array_shift($csv);

            $enrolled = 0;
            $failed = [];

            DB::beginTransaction();

            foreach ($csv as $index => $row) {
                try {
                    $data = array_combine($headers, $row);

                    $student = Student::where('student_number', $data['student_number'])->first();
                    $section = Section::find($data['section_id']);

                    if (!$student || !$section) {
                        $failed[] = "Row " . ($index + 2) . ": Student or section not found";
                        continue;
                    }

                    // Check duplicate
                    $exists = Enrollment::where('student_id', $student->id)
                        ->where('section_id', $section->id)
                        ->where('academic_year', $validated['academic_year'])
                        ->where('semester', $validated['semester'])
                        ->exists();

                    if ($exists) {
                        $failed[] = "Row " . ($index + 2) . ": Already enrolled";
                        continue;
                    }

                    // Check capacity
                    if (!$section->hasAvailableSlots($validated['academic_year'], $validated['semester'])) {
                        $failed[] = "Row " . ($index + 2) . ": Section full";
                        continue;
                    }

                    Enrollment::create([
                        'student_id' => $student->id,
                        'section_id' => $section->id,
                        'academic_year' => $validated['academic_year'],
                        'semester' => $validated['semester'],
                        'enrollment_date' => now(),
                        'status' => 'enrolled',
                    ]);

                    $enrolled++;
                } catch (\Exception $e) {
                    $failed[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            AuditLog::log('bulk_enrollment', null, [], [
                'enrolled' => $enrolled,
                'failed' => count($failed),
            ]);

            $message = "{$enrolled} students enrolled successfully.";
            if (!empty($failed)) {
                $message .= " " . count($failed) . " failed.";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to bulk enroll: ' . $e->getMessage());
        }
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

    public function forceDelete($id)
    {
        Enrollment::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Enrollment permanently deleted.');
    }
}