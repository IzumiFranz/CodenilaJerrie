<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Course;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Section::with('course');

        if ($request->filled('search')) {
            $query->where('section_name', 'like', "%{$request->search}%");
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }

        $sections = $query->orderBy('course_id')->orderBy('year_level')->orderBy('section_name')->paginate(20);
        $courses = Course::where('is_active', true)->get();

        return view('admin.sections.index', compact('sections', 'courses'));
    }

    public function create()
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.sections.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'section_name' => ['required', 'string', 'max:50'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'max_students' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        try {
            // Check for duplicate section
            $exists = Section::where('course_id', $validated['course_id'])
                ->where('year_level', $validated['year_level'])
                ->where('section_name', $validated['section_name'])
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Section already exists for this course and year level.');
            }

            $section = Section::create($validated);

            AuditLog::log('section_created', $section);

            return redirect()
                ->route('admin.sections.index')
                ->with('success', 'Section created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create section: ' . $e->getMessage());
        }
    }

    public function show(Section $section)
    {
        $section->load(['course', 'enrollments.student', 'assignments.instructor']);
        
        // Get current academic year and semester
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();
        
        $enrolledCount = $section->getEnrolledStudentsCount($currentAcademicYear, $currentSemester);
        
        return view('admin.sections.show', compact('section', 'enrolledCount', 'currentAcademicYear', 'currentSemester'));
    }

    public function edit(Section $section)
    {
        $courses = Course::where('is_active', true)->get();
        return view('admin.sections.edit', compact('section', 'courses'));
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'section_name' => ['required', 'string', 'max:50'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'max_students' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        try {
            $oldValues = $section->toArray();
            $section->update($validated);

            AuditLog::log('section_updated', $section, $oldValues, $section->toArray());

            return redirect()
                ->route('admin.sections.index')
                ->with('success', 'Section updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update section: ' . $e->getMessage());
        }
    }

    public function destroy(Section $section)
    {
        try {
            if ($section->enrollments()->where('status', 'enrolled')->count() > 0) {
                return back()->with('error', 'Cannot delete section with active enrollments.');
            }

            AuditLog::log('section_deleted', $section);
            $section->delete();

            return redirect()
                ->route('admin.sections.index')
                ->with('success', 'Section deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete section: ' . $e->getMessage());
        }
    }

    public function trashed()
    {
        $sections = Section::onlyTrashed()
            ->with('course')
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.sections.trashed', compact('sections'));
    }

    public function restore($id)
    {
        try {
            $section = Section::onlyTrashed()->findOrFail($id);
            $section->restore();

            AuditLog::log('section_restored', $section);

            return redirect()
                ->route('admin.sections.index')
                ->with('success', 'Section restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore section: ' . $e->getMessage());
        }
    }

    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }
}