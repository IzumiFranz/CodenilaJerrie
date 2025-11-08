<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::withCount(['subjects', 'sections', 'students']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                  ->orWhere('course_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $courses = $query->orderBy('course_name')->paginate(20);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code'],
            'course_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_years' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['boolean'],
        ]);

        try {
            $course = Course::create($validated);

            AuditLog::log('course_created', $course);

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    public function show(Course $course)
    {
        $course->load(['subjects', 'sections', 'students']);
        return view('admin.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20', 'unique:courses,course_code,' . $course->id],
            'course_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_years' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['boolean'],
        ]);

        try {
            $oldValues = $course->toArray();
            $course->update($validated);

            AuditLog::log('course_updated', $course, $oldValues, $course->toArray());

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }

    public function destroy(Course $course)
    {
        try {
            if ($course->students()->count() > 0) {
                return back()->with('error', 'Cannot delete course with enrolled students.');
            }

            AuditLog::log('course_deleted', $course);
            $course->delete();

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete course: ' . $e->getMessage());
        }
    }

    public function trashed()
    {
        $courses = Course::onlyTrashed()
            ->withCount(['subjects', 'sections', 'students'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.courses.trashed', compact('courses'));
    }

    public function restore($id)
    {
        try {
            $course = Course::onlyTrashed()->findOrFail($id);
            $course->restore();

            AuditLog::log('course_restored', $course);

            return redirect()
                ->route('admin.courses.index')
                ->with('success', 'Course restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore course: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Course $course)
    {
        try {
            $course->update(['is_active' => !$course->is_active]);

            AuditLog::log('course_status_toggled', $course);

            return back()->with('success', 'Course status updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}