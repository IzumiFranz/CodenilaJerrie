<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorSubjectSection;
use App\Models\Instructor;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Course;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorSubjectSection::with(['instructor.user', 'subject', 'section.course']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('instructor', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
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

        $assignments = $query->orderBy('created_at', 'desc')->paginate(20);
        $courses = Course::where('is_active', true)->get();
        $academicYears = $this->getAcademicYears();

        return view('admin.assignments.index', compact('assignments', 'courses', 'academicYears'));
    }

    public function create()
    {
        $instructors = Instructor::with('user', 'specialization')
            ->whereHas('user', fn($q) => $q->where('status', 'active'))
            ->get();
        $courses = Course::where('is_active', true)->get();
        $academicYears = $this->getAcademicYears();

        return view('admin.assignments.create', compact('instructors', 'courses', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'instructor_id' => ['required', 'exists:instructors,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'academic_year' => ['required', 'string'],
            'semester' => ['required', 'in:1st,2nd,summer'],
        ]);

        try {
            // Check for duplicate assignment
            $exists = InstructorSubjectSection::where('instructor_id', $validated['instructor_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('section_id', $validated['section_id'])
                ->where('academic_year', $validated['academic_year'])
                ->where('semester', $validated['semester'])
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Assignment already exists.');
            }

            // Check if instructor is qualified for the subject
            $instructor = Instructor::findOrFail($validated['instructor_id']);
            $subject = Subject::findOrFail($validated['subject_id']);

            if ($subject->specialization_id && !$instructor->canTeachSubject($subject)) {
                return back()->withInput()->with('warning', 'Warning: Instructor specialization does not match subject requirement.');
            }

            $assignment = InstructorSubjectSection::create($validated);

            AuditLog::log('assignment_created', $assignment);

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', 'Assignment created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create assignment: ' . $e->getMessage());
        }
    }

    public function destroy(InstructorSubjectSection $assignment)
    {
        try {
            AuditLog::log('assignment_deleted', $assignment);
            $assignment->delete();

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', 'Assignment deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete assignment: ' . $e->getMessage());
        }
    }

    public function trashed()
    {
        $assignments = InstructorSubjectSection::onlyTrashed()
            ->with(['instructor.user', 'subject', 'section.course'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.assignments.trashed', compact('assignments'));
    }

    public function restore($id)
    {
        try {
            $assignment = InstructorSubjectSection::onlyTrashed()->findOrFail($id);
            $assignment->restore();

            AuditLog::log('assignment_restored', $assignment);

            return redirect()
                ->route('admin.assignments.index')
                ->with('success', 'Assignment restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore assignment: ' . $e->getMessage());
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
}