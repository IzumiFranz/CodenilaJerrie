<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\AuditLog;
use App\Models\LessonView;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\LessonAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'User is not a student.');
        }

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
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'User is not a student.');
        }
        
        // Check authorization using policy
        $this->authorize('view', $lesson);

        // Increment view count
        $lesson->incrementViewCount();

        // Log lesson view
        AuditLog::log('lesson_viewed', $lesson, [], [
            'student_id' => $student->id
        ]);

        $lesson->load(['subject.course', 'instructor.user', 
        'instructor', 'attachments', 'quizzes.attempts']);

        // Get other lessons in same subject
        $relatedLessons = Lesson::where('subject_id', $lesson->subject_id)
            ->where('id', '!=', $lesson->id)
            ->where('is_published', true)
            ->orderBy('order')
            ->limit(5)
            ->get();

        return view('student.lessons.show', compact('lesson', 'relatedLessons'));
    }

    public function download(Lesson $lesson, $attachmentId = null)
    {
        // Check authorization
        $this->authorize('view', $lesson);

        try {
            // If downloading an attachment
            if ($attachmentId) {
                $attachment = \App\Models\LessonAttachment::where('lesson_id', $lesson->id)
                    ->findOrFail($attachmentId);

                // Optional: verify student access via a helper or policy
                if (!auth()->user()->hasAccessToLesson($lesson->id)) {
                    abort(403, 'Unauthorized access to this lesson attachment.');
                }

                // Increment download counter
                $attachment->increment('download_count');

                // Log audit
                \App\Models\AuditLog::log('lesson_attachment_downloaded', $lesson, [
                    'attachment_id' => $attachment->id,
                    'filename' => $attachment->file_name,
                ], [
                    'student_id' => auth()->user()->student->id,
                ]);

                $filePath = storage_path('app/' . $attachment->file_path);

                if (!file_exists($filePath)) {
                    return back()->with('error', 'Attachment file not found.');
                }

                return response()->download($filePath, $attachment->file_name);
            }

            // Otherwise, download the lesson’s main file
            if (!$lesson->hasFile()) {
                return back()->with('error', 'No lesson file available for download.');
            }

            // Log main file download
            \App\Models\AuditLog::log('lesson_file_downloaded', $lesson, [], [
                'student_id' => auth()->user()->student->id,
            ]);

            $filePath = storage_path('app/' . $lesson->file_path);

            if (!file_exists($filePath)) {
                return back()->with('error', 'Lesson file not found.');
            }

            $fileName = basename($lesson->file_path);

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }


    private function getCurrentSemester(): string
    {
        $month = now()->month;
        if ($month >= 6 && $month <= 10) return '1st';
        if ($month >= 11 || $month <= 3) return '2nd';
        return 'summer';
    }
    public function trackView(Request $request, Lesson $lesson)
    {
        $student = auth()->user()->student;
        
        // Verify student has access to this lesson
        $hasAccess = $this->studentHasAccessToLesson($student, $lesson);
        
        if (!$hasAccess) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        try {
            $view = LessonView::recordView($lesson, $student);
            
            // Increment lesson view count
            $lesson->incrementViewCount();
            
            return response()->json([
                'success' => true,
                'view_id' => $view->id,
                'message' => 'View tracked successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to track view'], 500);
        }
    }
    public function updateDuration(Request $request, Lesson $lesson)
    {
        $validated = $request->validate([
            'duration' => ['required', 'integer', 'min:0'],
            'view_id' => ['nullable', 'exists:lesson_views,id'],
        ]);
        
        $student = auth()->user()->student;
        
        try {
            // Find or create the view record
            if (isset($validated['view_id'])) {
                $view = LessonView::findOrFail($validated['view_id']);
            } else {
                $view = LessonView::where('lesson_id', $lesson->id)
                    ->where('student_id', $student->id)
                    ->orderBy('viewed_at', 'desc')
                    ->first();
                    
                if (!$view) {
                    $view = LessonView::recordView($lesson, $student);
                }
            }
            
            // Update duration
            $view->duration_seconds = $validated['duration'];
            $view->save();
            
            return response()->json([
                'success' => true,
                'duration' => $view->getDurationFormatted()
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update duration'], 500);
        }
    }
    public function markCompleted(Request $request, Lesson $lesson)
    {
        $student = auth()->user()->student;
        
        try {
            $view = LessonView::where('lesson_id', $lesson->id)
                ->where('student_id', $student->id)
                ->orderBy('viewed_at', 'desc')
                ->first();
                
            if (!$view) {
                $view = LessonView::recordView($lesson, $student);
            }
            
            $view->markCompleted();
            
            AuditLog::log('lesson_completed', $lesson);
            
            return response()->json([
                'success' => true,
                'message' => 'Lesson marked as completed',
                'completed_at' => $view->completed_at->format('M d, Y h:i A')
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to mark as completed'], 500);
        }
    }

    // Helper method
    private function studentHasAccessToLesson(Student $student, Lesson $lesson): bool
    {
        if (!$lesson->is_published) {
            return false;
        }
        
        // Check if student is enrolled in a section where this subject is taught
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();
        
        return $student->enrollments()
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->where('status', 'enrolled')
            ->whereHas('section.assignments', function($q) use ($lesson) {
                $q->where('subject_id', $lesson->subject_id);
            })
            ->exists();
    }
    /**
     * Download an attachment.
     */
    public function downloadAttachment(Lesson $lesson, LessonAttachment $attachment)
    {
        // Check if student has access to this lesson
        $student = auth()->user()->student;
        $hasAccess = $this->studentHasAccessToLesson($student, $lesson);

        if (!$hasAccess) {
            abort(403, 'You are not enrolled in a section for this subject.');
        }

        // Check if attachment belongs to this lesson
        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        // Check if attachment is visible
        if (!$attachment->is_visible) {
            abort(403, 'This attachment is not available.');
        }

        try {
            // Record download (student_id column stores user_id)
            $attachment->recordDownload(auth()->id());

            // Audit log
            AuditLog::log('lesson_attachment_downloaded', $lesson, [
                'attachment_id' => $attachment->id,
                'filename' => $attachment->original_filename,
            ], [
                'student_id' => auth()->user()->student->id,
            ]);

            // Return file download
            return Storage::download($attachment->file_path, $attachment->original_filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * View attachment (for images/PDFs in browser).
     */
    public function viewAttachment(Lesson $lesson, LessonAttachment $attachment)
    {
        // Check if student has access to this lesson
        $student = auth()->user()->student;
        $hasAccess = $this->studentHasAccessToLesson($student, $lesson);

        if (!$hasAccess) {
            abort(403, 'You are not enrolled in a section for this subject.');
        }

        // Check if attachment belongs to this lesson
        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        // Check if attachment is visible
        if (!$attachment->is_visible) {
            abort(403, 'This attachment is not available.');
        }

        // Only allow viewing of certain file types
        if (!in_array($attachment->file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return $this->downloadAttachment($lesson, $attachment);
        }

        try {
            // Record as download
            $attachment->recordDownload(auth()->id());

            // Return file for viewing
            return Storage::response($attachment->file_path);

        } catch (\Exception $e) {
            return back()->with('error', 'View failed: ' . $e->getMessage());
        }
    }
    public function downloadAllAttachments(Lesson $lesson)
    {
        // Check if student has access to this lesson
        $student = auth()->user()->student;
        $hasAccess = $this->studentHasAccessToLesson($student, $lesson);

        if (!$hasAccess) {
            abort(403, 'You are not enrolled in a section for this subject.');
        }

        $attachments = $lesson->visibleAttachments()->get();

        if ($attachments->isEmpty()) {
            return back()->with('error', 'No attachments available.');
        }

        try {
            // Create temporary ZIP file
            $zipFileName = 'lesson-' . $lesson->id . '-attachments-' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }

            $zip = new ZipArchive();
            
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Could not create ZIP file.');
            }

            // Add each attachment to ZIP
            foreach ($attachments as $attachment) {
                $filePath = storage_path('app/public/' . $attachment->file_path);
                
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $attachment->original_filename);
                    
                    // Record download
                    $attachment->recordDownload(auth()->id());
                }
            }

            $zip->close();

            // Audit log
            AuditLog::log('lesson_attachments_downloaded_all', $lesson, [
                'count' => $attachments->count(),
            ], [
                'student_id' => auth()->user()->student->id,
            ]);

            // Return ZIP file and delete after send
            return response()->download($zipPath, 'lesson-attachments.zip')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }
}