<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;

class LessonController extends Controller
{
    /**
     * List all lessons for student
     */
    public function index(Request $request)
    {
        $student = $request->user()->student;
        
        // Get enrolled section IDs
        $sectionIds = $student->enrollments()
            ->where('status', 'enrolled')
            ->pluck('section_id');
        
        // Get subject IDs
        $subjectIds = \DB::table('instructor_subject_section')
            ->whereIn('section_id', $sectionIds)
            ->pluck('subject_id')
            ->unique();
        
        $query = Lesson::whereIn('subject_id', $subjectIds)
            ->where('is_published', true)
            ->with(['subject', 'instructor']);
        
        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        
        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        $lessons = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $lessons
        ]);
    }
    
    /**
     * Show lesson details
     */
    public function show(Request $request, Lesson $lesson)
    {
        // Verify student has access
        $student = $request->user()->student;
        
        if (!$lesson->isAvailableToStudent($student)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lesson'
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'data' => $lesson->load(['subject.course', 'instructor'])
        ]);
    }
    
    /**
     * Download lesson file
     */
    public function download(Request $request, Lesson $lesson)
    {
        $student = $request->user()->student;
        
        if (!$lesson->isAvailableToStudent($student)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this lesson'
            ], 403);
        }
        
        if (!$lesson->file_path || !\Storage::disk('public')->exists($lesson->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => \Storage::disk('public')->url($lesson->file_path),
                'filename' => $lesson->file_name ?? basename($lesson->file_path)
            ]
        ]);
    }
}