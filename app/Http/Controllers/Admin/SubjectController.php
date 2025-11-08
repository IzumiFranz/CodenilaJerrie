<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Specialization;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['course', 'specialization']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject_code', 'like', "%{$search}%")
                  ->orWhere('subject_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subjects = $query->orderBy('course_id')->orderBy('year_level')->paginate(20);
        $courses = Course::where('is_active', true)->get();

        return view('admin.subjects.index', compact('subjects', 'courses'));
    }

    public function create(Request $request)
    {
        $courses = Course::where('is_active', true)->get();
        $specializations = Specialization::where('is_active', true)->get();
        $selectedCourse = $request->get('course');

        return view('admin.subjects.create', compact('courses', 'specializations', 'selectedCourse'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'specialization_id' => ['nullable', 'exists:specializations,id'],
            'subject_code' => ['required', 'string', 'max:20', 'unique:subjects,subject_code'],
            'subject_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'units' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['boolean'],
        ]);

        try {
            $subject = Subject::create($validated);

            AuditLog::log('subject_created', $subject);

            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Subject created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create subject: ' . $e->getMessage());
        }
    }

    public function show(Subject $subject)
    {
        $subject->load(['course', 'specialization', 'lessons', 'quizzes', 'questionBank']);
        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $courses = Course::where('is_active', true)->get();
        $specializations = Specialization::where('is_active', true)->get();

        return view('admin.subjects.edit', compact('subject', 'courses', 'specializations'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'specialization_id' => ['nullable', 'exists:specializations,id'],
            'subject_code' => ['required', 'string', 'max:20', 'unique:subjects,subject_code,' . $subject->id],
            'subject_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'units' => ['required', 'integer', 'min:1', 'max:10'],
            'is_active' => ['boolean'],
        ]);

        try {
            $oldValues = $subject->toArray();
            $subject->update($validated);

            AuditLog::log('subject_updated', $subject, $oldValues, $subject->toArray());

            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Subject updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update subject: ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        try {
            if ($subject->assignments()->count() > 0) {
                return back()->with('error', 'Cannot delete subject with active assignments.');
            }

            AuditLog::log('subject_deleted', $subject);
            $subject->delete();

            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }

    public function trashed()
    {
        $subjects = Subject::onlyTrashed()
            ->with(['course', 'specialization'])
            ->orderBy('deleted_at', 'desc')
            ->paginate(20);

        return view('admin.subjects.trashed', compact('subjects'));
    }

    public function restore($id)
    {
        try {
            $subject = Subject::onlyTrashed()->findOrFail($id);
            $subject->restore();

            AuditLog::log('subject_restored', $subject);

            return redirect()
                ->route('admin.subjects.index')
                ->with('success', 'Subject restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore subject: ' . $e->getMessage());
        }
    }

    public function getQualifiedInstructors(Subject $subject)
    {
        $instructors = $subject->getQualifiedInstructors();
        return response()->json($instructors);
    }
}