<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        // Get enrolled subjects
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        $enrolledSubjects = $student->sections()
            ->wherePivot('academic_year', $currentAcademicYear)
            ->wherePivot('semester', $currentSemester)
            ->wherePivot('status', 'enrolled')
            ->with('subjects')
            ->get()
            ->pluck('subjects')
            ->flatten()
            ->unique('id');

        $query = Lesson::whereIn('subject_id', $enrolledSubjects->pluck('id'))
            ->where('is_published', true)
            ->with(['subject', 'instructor.user']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $lessons = $query->paginate(12);

        return view('student.lessons.index', compact(
            'lessons',
            'enrolledSubjects'
        ));
    }

    public function show(Lesson $lesson)
    {
        $user = auth()->user();
        
        // Check authorization using policy
        $this->authorize('view', $lesson);

        // Increment view count
        $lesson->incrementViewCount();

        // Log lesson view
        AuditLog::log('lesson_viewed', $lesson, [], [
            'student_id' => $user->student->id
        ]);

        $lesson->load(['subject.course', 'instructor.user']);

        // Get other lessons in same subject
        $relatedLessons = Lesson::where('subject_id', $lesson->subject_id)
            ->where('id', '!=', $lesson->id)
            ->where('is_published', true)
            ->orderBy('order')
            ->limit(5)
            ->get();

        return view('student.lessons.show', compact('lesson', 'relatedLessons'));
    }

    public function download(Lesson $lesson)
    {
        // Check authorization
        $this->authorize('view', $lesson);

        if (!$lesson->hasFile()) {
            return back()->with('error', 'No file available for download.');
        }

        try {
            // Log file download
            AuditLog::log('lesson_file_downloaded', $lesson, [], [
                'student_id' => auth()->user()->student->id
            ]);

            $filePath = storage_path('app/' . $lesson->file_path);
            
            if (!file_exists($filePath)) {
                return back()->with('error', 'File not found.');
            }

            $fileName = basename($lesson->file_path);
            
            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download file: ' . $e->getMessage());
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