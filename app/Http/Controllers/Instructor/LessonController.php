<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Student;
use App\Models\LessonView;
use App\Models\LessonAttachment;
use App\Models\InstructorSubjectSection;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Services\LessonAttachmentService;
use App\Mail\LessonPublishedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    /**
     * Display a listing of lessons.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $instructor = $user->instructor;
        
        if (!$instructor) {
            abort(403, 'User is not an instructor.');
        }
        
        // Base query with relationships
        $query = Lesson::where('instructor_id', $instructor->id)
            ->with(['subject', 'attachments']);

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Subject filter
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'published':
                    $query->where('status', 'published');
                    break;
                case 'draft':
                    $query->where('status', 'draft');
                    break;
                case 'scheduled':
                    $query->where('status', 'scheduled')
                          ->whereNotNull('scheduled_publish_at');
                    break;
            }
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title');
                break;
            case 'views':
                $query->withCount('views')->orderBy('views_count', 'desc');
                break;
            default: // newest
                $query->latest();
                break;
        }

        $lessons = $query->paginate(20)->withQueryString();

        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        // Statistics for dashboard cards
        $totalLessons = Lesson::where('instructor_id', $instructor->id)->count();
        $publishedLessons = Lesson::where('instructor_id', $instructor->id)
            ->where('status', 'published')
            ->count();
        $scheduledLessons = Lesson::where('instructor_id', $instructor->id)
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_publish_at')
            ->count();
        $withAttachments = Lesson::where('instructor_id', $instructor->id)
            ->has('attachments')
            ->count();

        return view('instructor.lessons.index', compact(
            'lessons',
            'subjects',
            'totalLessons',
            'publishedLessons',
            'scheduledLessons',
            'withAttachments'
        ));
    }

    /**
     * Show the form for creating a new lesson.
     */
    public function create(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        if ($subjects->isEmpty()) {
            return redirect()
                ->route('instructor.dashboard')
                ->with('error', 'You are not assigned to teach any subjects yet.');
        }

        $selectedSubject = $request->get('subject');

        return view('instructor.lessons.create', compact('subjects', 'selectedSubject'));
    }

    /**
     * Store a newly created lesson.
     */
    public function store(Request $request)
    {
        $instructor = auth()->user()->instructor;

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published',
            'scheduled_publish_at' => 'nullable|date|after:now',
            'scheduled_unpublish_at' => 'nullable|date|after:scheduled_publish_at',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,webp,zip,rar,mp4,mp3',
        ]);

        try {
            // Verify instructor teaches this subject
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()
                    ->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            DB::beginTransaction();

            // Calculate word count and read time
            $plainText = strip_tags($validated['content']);
            $wordCount = str_word_count($plainText);
            $readTimeMinutes = max(1, ceil($wordCount / 200)); // 200 words per minute

            // Get next order number if not provided
            if (!isset($validated['order']) || $validated['order'] === 0) {
                $maxOrder = Lesson::where('subject_id', $validated['subject_id'])->max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            // Determine final status
            $finalStatus = $validated['status'];
            if ($request->filled('scheduled_publish_at')) {
                $finalStatus = 'scheduled';
            }

            // Create lesson
            $lesson = Lesson::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'content' => $validated['content'],
                'order' => $validated['order'],
                'status' => $finalStatus,
                'word_count' => $wordCount,
                'read_time_minutes' => $readTimeMinutes,
                'scheduled_publish_at' => $validated['scheduled_publish_at'] ?? null,
                'scheduled_unpublish_at' => $validated['scheduled_unpublish_at'] ?? null,
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                $attachmentService = app(LessonAttachmentService::class);
                $attachmentService->uploadMultiple(
                    $lesson,
                    $request->file('attachments'),
                    auth()->id()
                );
            }

            // Audit log
            AuditLog::log('lesson_created', $lesson, [], [
                'subject' => $lesson->subject->subject_name ?? $lesson->subject->name ?? null,
                'status' => $lesson->is_published ? 'published' : 'draft',
            ]);

            // Send notifications if published immediately
            if ($finalStatus === 'published') {
                $this->notifyStudentsAboutLesson($lesson);
            }

            DB::commit();

            return redirect()
                ->route('instructor.lessons.show', $lesson)
                ->with('success', 'Lesson created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create lesson: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified lesson.
     */
    public function show(Lesson $lesson)
    {
        $this->authorize('view', $lesson);
        
        $lesson->load([
            'subject',
            'attachments' => function($query) {
                $query->where('is_visible', true)->orderBy('display_order');
            }
        ]);

        // Get view statistics
        $viewStats = [
            'total_views' => LessonView::where('lesson_id', $lesson->id)->count(),
            'unique_viewers' => LessonView::where('lesson_id', $lesson->id)
                ->distinct('student_id')
                ->count(),
            'completed_count' => LessonView::where('lesson_id', $lesson->id)
                ->where('completed', true)
                ->count(),
        ];

        return view('instructor.lessons.show', compact('lesson', 'viewStats'));
    }

    /**
     * Show the form for editing the lesson.
     */
    public function edit(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        $instructor = auth()->user()->instructor;
        
        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $lesson->load('attachments');

        return view('instructor.lessons.edit', compact('lesson', 'subjects'));
    }

    /**
     * Update the specified lesson.
     */
    public function update(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'content' => 'required|string',
            'order' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
            'scheduled_publish_at' => 'nullable|date|after:now',
            'scheduled_unpublish_at' => 'nullable|date|after:scheduled_publish_at',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,webp,zip,rar,mp4,mp3',
        ]);

        try {
            $instructor = auth()->user()->instructor;

            // Verify instructor teaches this subject
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()
                    ->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            DB::beginTransaction();

            $oldValues = $lesson->toArray();
            $wasPublished = $lesson->status === 'published';

            // Calculate word count and read time
            $plainText = strip_tags($validated['content']);
            $wordCount = str_word_count($plainText);
            $readTimeMinutes = max(1, ceil($wordCount / 200));

            // Determine final status
            $finalStatus = $validated['status'];
            if ($request->filled('scheduled_publish_at')) {
                $finalStatus = 'scheduled';
            }

            // Update lesson
            $lesson->update([
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'content' => $validated['content'],
                'order' => $validated['order'],
                'status' => $finalStatus,
                'word_count' => $wordCount,
                'read_time_minutes' => $readTimeMinutes,
                'scheduled_publish_at' => $validated['scheduled_publish_at'] ?? null,
                'scheduled_unpublish_at' => $validated['scheduled_unpublish_at'] ?? null,
            ]);

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                $attachmentService = app(LessonAttachmentService::class);
                $attachmentService->uploadMultiple(
                    $lesson,
                    $request->file('attachments'),
                    auth()->id()
                );
            }

            // Audit log
            AuditLog::log('lesson_updated', $lesson, $oldValues, $lesson->toArray());

            // Send notifications if newly published
            if (!$wasPublished && $finalStatus === 'published') {
                $this->notifyStudentsAboutLesson($lesson);
            }

            DB::commit();

            return redirect()
                ->route('instructor.lessons.show', $lesson)
                ->with('success', 'Lesson updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update lesson: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy(Lesson $lesson)
    {
        $this->authorize('delete', $lesson);

        try {
            DB::beginTransaction();

            // Audit log before deletion
            AuditLog::log('lesson_deleted', $lesson, ['title' => $lesson->title], []);
            
            // Delete will cascade to attachments (handled by model event)
            $lesson->delete();

            DB::commit();

            return redirect()
                ->route('instructor.lessons.index')
                ->with('success', 'Lesson deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete lesson: ' . $e->getMessage());
        }
    }

    /**
     * Toggle lesson publish status.
     */
    public function togglePublish(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        try {
            DB::beginTransaction();

            $wasPublished = $lesson->status === 'published';
            $newStatus = $wasPublished ? 'draft' : 'published';
            
            $lesson->update(['status' => $newStatus]);

            // Audit log
            AuditLog::log('lesson_publish_toggled', $lesson, ['old_status' => $wasPublished ? 'published' : 'draft'], ['new_status' => $newStatus]);

            // Send notifications if newly published
            if (!$wasPublished && $newStatus === 'published') {
                $this->notifyStudentsAboutLesson($lesson);
            }

            DB::commit();
            
            $message = $newStatus === 'published' 
                ? 'Lesson published successfully!' 
                : 'Lesson unpublished successfully.';
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to toggle publish status: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a lesson.
     */
    public function duplicate(Lesson $lesson)
    {
        $this->authorize('view', $lesson);

        try {
            DB::beginTransaction();

            // Replicate lesson
            $newLesson = $lesson->replicate();
            $newLesson->title = $lesson->title . ' (Copy)';
            $newLesson->status = 'draft';
            $newLesson->scheduled_publish_at = null;
            $newLesson->scheduled_unpublish_at = null;
            $newLesson->order = Lesson::where('subject_id', $lesson->subject_id)->max('order') + 1;
            $newLesson->save();

            // Copy attachments
            foreach ($lesson->attachments as $attachment) {
                $oldPath = $attachment->file_path;
                
                if (Storage::disk('public')->exists($oldPath)) {
                    $extension = $attachment->file_extension;
                    $newStoredFilename = Str::uuid() . '.' . $extension;
                    $newPath = 'lesson-attachments/' . $newLesson->id . '/' . $newStoredFilename;
                    
                    Storage::disk('public')->copy($oldPath, $newPath);
                    
                    LessonAttachment::create([
                        'lesson_id' => $newLesson->id,
                        'original_filename' => $attachment->original_filename,
                        'stored_filename' => $newStoredFilename,
                        'file_path' => $newPath,
                        'mime_type' => $attachment->mime_type,
                        'file_size' => $attachment->file_size,
                        'file_extension' => $attachment->file_extension,
                        'description' => $attachment->description,
                        'display_order' => $attachment->display_order,
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            // Audit log
            AuditLog::log('lesson_duplicated', $newLesson, [], ['original_lesson_id' => $lesson->id]);

            DB::commit();

            return redirect()
                ->route('instructor.lessons.edit', $newLesson)
                ->with('success', 'Lesson duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate lesson: ' . $e->getMessage());
        }
    }

    /**
     * Schedule lesson publish/unpublish.
     */
    public function schedule(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        $validated = $request->validate([
            'scheduled_publish_at' => 'nullable|date|after:now',
            'scheduled_unpublish_at' => 'nullable|date|after:scheduled_publish_at',
        ]);
        
        try {
            $lesson->update([
                'scheduled_publish_at' => $validated['scheduled_publish_at'] ?? null,
                'scheduled_unpublish_at' => $validated['scheduled_unpublish_at'] ?? null,
                'status' => $validated['scheduled_publish_at'] ? 'scheduled' : $lesson->status,
            ]);
            
            // Audit log
            AuditLog::log('lesson_schedule_updated', $lesson, [], $validated);
            
            $message = 'Lesson scheduling updated successfully.';
            if ($lesson->scheduled_publish_at) {
                $message .= ' Will auto-publish on ' . $lesson->scheduled_publish_at->format('M d, Y h:i A');
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    /**
     * Cancel scheduled publish.
     */
    public function cancelSchedule(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        try {
            $lesson->update([
                'scheduled_publish_at' => null,
                'scheduled_unpublish_at' => null,
                'status' => 'draft',
            ]);
            
            // Audit log
            AuditLog::log('lesson_schedule_cancelled', $lesson, [], []);
            
            return back()->with('success', 'Schedule cancelled successfully.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel schedule: ' . $e->getMessage());
        }
    }

    /**
     * Show view statistics.
     */
    public function viewStatistics(Lesson $lesson)
    {
        $this->authorize('view', $lesson);
        
        // Statistics
        $stats = [
            'total_views' => LessonView::where('lesson_id', $lesson->id)->count(),
            'unique_viewers' => LessonView::where('lesson_id', $lesson->id)
                ->distinct('student_id')
                ->count(),
            'completed_count' => LessonView::where('lesson_id', $lesson->id)
                ->where('completed', true)
                ->count(),
            'average_duration' => LessonView::where('lesson_id', $lesson->id)
                ->avg('duration_seconds'),
            'completion_rate' => 0,
        ];

        // Calculate completion rate
        if ($stats['unique_viewers'] > 0) {
            $stats['completion_rate'] = round(($stats['completed_count'] / $stats['unique_viewers']) * 100, 2);
        }
        
        // View history
        $viewHistory = LessonView::where('lesson_id', $lesson->id)
            ->with('student.user')
            ->orderBy('viewed_at', 'desc')
            ->paginate(50);
        
        // Completion timeline for chart
        $completionTimeline = LessonView::where('lesson_id', $lesson->id)
            ->where('completed', true)
            ->whereNotNull('completed_at')
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(DISTINCT student_id) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Students who haven't viewed yet
        $enrolledStudents = $this->getEnrolledStudents($lesson);
        $viewedStudentIds = LessonView::where('lesson_id', $lesson->id)
            ->pluck('student_id')
            ->unique();
        
        $notViewedStudents = $enrolledStudents->whereNotIn('id', $viewedStudentIds);
        
        return view('instructor.lessons.view-statistics', compact(
            'lesson',
            'stats',
            'viewHistory',
            'completionTimeline',
            'notViewedStudents'
        ));
    }

    /**
     * Show attachments management page.
     */
    public function attachments(Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $lesson->load(['attachments.uploader', 'attachments.downloads']);
        
        $statistics = app(LessonAttachmentService::class)->getStatistics($lesson);

        return view('instructor.lessons.attachments', compact('lesson', 'statistics'));
    }

    /**
     * Upload new attachments.
     */
    public function uploadAttachments(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'required|file|max:51200',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $service = app(LessonAttachmentService::class);
            $descriptions = $request->input('descriptions', []);
            
            $attachments = $service->uploadMultiple(
                $lesson,
                $request->file('files'),
                auth()->id(),
                $descriptions
            );

            // Audit log
            AuditLog::log('lesson_attachments_uploaded', $lesson, [], [
                'count' => count($attachments),
                'filenames' => collect($attachments)->pluck('original_filename')->toArray(),
            ]);

            DB::commit();

            return redirect()
                ->route('instructor.lessons.attachments', $lesson)
                ->with('success', count($attachments) . ' file(s) uploaded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete an attachment.
     */
    public function deleteAttachment(Lesson $lesson, LessonAttachment $attachment)
    {
        $this->authorize('update', $lesson);

        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        try {
            $filename = $attachment->original_filename;
            
            app(LessonAttachmentService::class)->delete($attachment);

            // Audit log
            AuditLog::log('lesson_attachment_deleted', $lesson, [], ['filename' => $filename]);

            return redirect()
                ->route('instructor.lessons.attachments', $lesson)
                ->with('success', 'Attachment deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Toggle attachment visibility.
     */
    public function toggleAttachmentVisibility(Lesson $lesson, LessonAttachment $attachment)
    {
        $this->authorize('update', $lesson);

        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        try {
            $service = app(LessonAttachmentService::class);
            $attachment = $service->toggleVisibility($attachment);

            // Audit log
            AuditLog::log('lesson_attachment_visibility_toggled', $lesson, [], [
                'filename' => $attachment->original_filename,
                'visible' => $attachment->is_visible,
            ]);

            return response()->json([
                'success' => true,
                'visible' => $attachment->is_visible,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update attachment description.
     */
    public function updateAttachmentDescription(Request $request, Lesson $lesson, LessonAttachment $attachment)
    {
        $this->authorize('update', $lesson);

        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        $request->validate([
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $service = app(LessonAttachmentService::class);
            $attachment = $service->updateDescription($attachment, $request->input('description'));

            return response()->json([
                'success' => true,
                'description' => $attachment->description,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Reorder attachments.
     */
    public function reorderAttachments(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:lesson_attachments,id',
        ]);

        try {
            $service = app(LessonAttachmentService::class);
            $service->reorder($lesson, $request->input('order'));

            // Audit log
            AuditLog::log('lesson_attachments_reordered', $lesson, [], []);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download lesson file.
     */
    public function download(Lesson $lesson)
    {
        $this->authorize('view', $lesson);

        if (!$lesson->hasFile()) {
            return back()->with('error', 'No lesson file available for download.');
        }

        try {
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

    /**
     * Download attachment.
     */
    public function downloadAttachment(Lesson $lesson, LessonAttachment $attachment)
    {
        $this->authorize('view', $lesson);

        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->original_filename);
    }

    /**
     * Notify enrolled students about new/published lesson.
     */
    private function notifyStudentsAboutLesson(Lesson $lesson)
    {
        try {
            $students = $this->getEnrolledStudents($lesson);
            
            foreach ($students as $student) {
                // Check notification preferences
                $settings = $student->user->settings ?? null;
                $emailEnabled = $settings->email_lesson_published ?? true;
                $notificationEnabled = $settings->notification_lesson_published ?? true;

                // Send email
                if ($emailEnabled) {
                    Mail::to($student->user->email)->queue(new LessonPublishedMail($lesson));
                }

                // Create in-app notification
                if ($notificationEnabled) {
                    Notification::create([
                        'user_id' => $student->user_id,
                        'type' => 'info',
                        'title' => 'New Lesson Published',
                        'message' => "A new lesson '{$lesson->title}' has been published in {$lesson->subject->subject_name}.",
                        'action_url' => route('student.lessons.show', $lesson),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            \Log::error('Failed to notify students about lesson: ' . $e->getMessage());
        }
    }

    /**
     * Get students enrolled in this lesson's subject.
     */
    private function getEnrolledStudents(Lesson $lesson)
    {
        $instructor = auth()->user()->instructor;
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();

        // Find sections where instructor teaches this subject
        $sectionIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->where('subject_id', $lesson->subject_id)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->pluck('section_id');

        // Get enrolled students
        return Student::whereHas('enrollments', function($q) use ($sectionIds, $currentAcademicYear, $currentSemester) {
            $q->whereIn('section_id', $sectionIds)
              ->where('academic_year', $currentAcademicYear)
              ->where('semester', $currentSemester)
              ->where('status', 'enrolled');
        })
        ->with('user')
        ->get();
    }

    /**
     * Get current semester based on month.
     */
    private function getCurrentSemester(): string
    {
        $month = now()->month;
        
        if ($month >= 8 && $month <= 12) {
            return '1st'; // First semester: August to December
        } elseif ($month >= 1 && $month <= 5) {
            return '2nd'; // Second semester: January to May
        } else {
            return 'Summer'; // Summer: June to July
        }
    }
}