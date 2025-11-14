<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $query = Lesson::with(['instructor.user', 'subject']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $lessons = $query->orderBy('created_at', 'desc')->paginate(20);
        $subjects = Subject::where('is_active', true)->get();
        
        // Statistics
        $totalLessons = Lesson::count();
        $publishedLessons = Lesson::where('is_published', true)->count();

        return view('admin.lessons.index', compact('lessons', 'subjects', 'totalLessons', 'publishedLessons'));
    }

    public function show(Lesson $lesson)
    {
        $lesson->load(['instructor.user', 'subject.course', 'attachments']);
        
        // Get assignments (sections with access) - moved from view
        $assignments = collect();
        if ($lesson->subject) {
            $assignments = $lesson->subject->assignments()
                ->with('section.course', 'instructor')
                ->get();
        }
        
        // Get related lessons - moved from view
        $relatedLessons = collect();
        if ($lesson->subject) {
            $relatedLessons = $lesson->subject->lessons()
                ->where('id', '!=', $lesson->id)
                ->where('is_published', true)
                ->orderBy('order')
                ->limit(5)
                ->get();
        }
        
        return view('admin.lessons.show', compact('lesson', 'assignments', 'relatedLessons'));
    }

    public function destroy(Lesson $lesson)
    {
        try {
            AuditLog::log('lesson_deleted_by_admin', $lesson);
            
            // Delete file if exists
            $lesson->deleteFile();
            
            $lesson->delete();

            return redirect()
                ->route('admin.lessons.index')
                ->with('success', 'Lesson deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete lesson: ' . $e->getMessage());
        }
    }
}