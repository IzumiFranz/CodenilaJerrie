<?php


namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\InstructorSubjectSection;
use App\Models\AuditLog;
use App\Models\LessonAttachment;
use App\Mail\LessonPublishedMail;
use App\Services\LessonAttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonController extends Controller
{
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $query = Lesson::where('instructor_id', $instructor->id)
            ->with('subject');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $lessons = $query->orderBy('order')->orderBy('created_at', 'desc')->paginate(20);
        
        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('instructor.lessons.index', compact('lessons', 'subjects'));
    }

    public function create(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $selectedSubject = $request->get('subject');

        return view('instructor.lessons.create', compact('subjects', 'selectedSubject'));
    }

    public function store(Request $request)
    {
        $instructor = auth()->user()->instructor;

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,txt,zip', 'max:10240'], // 10MB
            'order' => ['nullable', 'integer', 'min:1'],
            'is_published' => ['boolean'],
        ]);

        try {
            // Verify instructor teaches this subject
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            // Handle file upload
            $filePath = null;
            $fileName = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
                    . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('lessons', $fileName, 'public');
            }

            // Get next order number if not provided
            if (!isset($validated['order'])) {
                $maxOrder = Lesson::where('subject_id', $validated['subject_id'])->max('order') ?? 0;
                $validated['order'] = $maxOrder + 1;
            }

            $lesson = Lesson::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'file_path' => $filePath,
                'file_name' => $fileName,
                'order' => $validated['order'],
                'is_published' => $validated['is_published'] ?? false,
            ]);

            AuditLog::log('lesson_created', $lesson);

            // TODO: Notify enrolled students if published
            // if ($lesson->is_published) {
            //     Mail::to($enrolledStudents)->send(new LessonPublishedMail($lesson));
            // }

            return redirect()
                ->route('instructor.lessons.index')
                ->with('success', 'Lesson created successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create lesson: ' . $e->getMessage());
        }
    }

    public function show(Lesson $lesson)
    {
        $this->authorize('view', $lesson);
        $lesson->load('subject.course');
        
        return view('instructor.lessons.show', compact('lesson'));
    }

    public function edit(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        $instructor = auth()->user()->instructor;
        
        // Get subjects taught by this instructor
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('instructor.lessons.edit', compact('lesson', 'subjects'));
    }

    public function update(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,txt,zip', 'max:10240'],
            'remove_file' => ['boolean'],
            'order' => ['required', 'integer', 'min:1'],
            'is_published' => ['boolean'],
        ]);

        try {
            $instructor = auth()->user()->instructor;

            // Verify instructor teaches this subject
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            $oldValues = $lesson->toArray();

            // Handle file removal
            if ($request->has('remove_file') && $request->remove_file) {
                $lesson->deleteFile();
                $lesson->file_path = null;
                $lesson->file_name = null;
            }

            // Handle new file upload
            if ($request->hasFile('file')) {
                // Delete old file
                $lesson->deleteFile();
                
                $file = $request->file('file');
                $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) 
                    . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('lessons', $fileName, 'public');
                
                $lesson->file_path = $filePath;
                $lesson->file_name = $fileName;
            }

            $lesson->update([
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'content' => $validated['content'],
                'order' => $validated['order'],
                'is_published' => $validated['is_published'] ?? $lesson->is_published,
            ]);

            AuditLog::log('lesson_updated', $lesson, $oldValues, $lesson->toArray());

            return redirect()
                ->route('instructor.lessons.index')
                ->with('success', 'Lesson updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update lesson: ' . $e->getMessage());
        }
    }

    public function destroy(Lesson $lesson)
    {
        $this->authorize('delete', $lesson);

        try {
            AuditLog::log('lesson_deleted', $lesson);
            
            $lesson->deleteFile();
            $lesson->delete();

            return redirect()
                ->route('instructor.lessons.index')
                ->with('success', 'Lesson deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete lesson: ' . $e->getMessage());
        }
    }

    public function togglePublish(Lesson $lesson)
    {
        $this->authorize('publish', $lesson);
        
        try {
            $wasPublished = $lesson->is_published;
            $lesson->is_published = !$lesson->is_published;
            $lesson->save();
            
            AuditLog::log('lesson_publish_toggled', $lesson);
            
            // 🔔 SEND EMAIL TO ENROLLED STUDENTS
            $settings = $student->user->settings ?? (object)[
                'email_lesson_published' => true,
                'notification_lesson_published' => true
            ];
            
            if ($settings->email_lesson_published) {
                Mail::to($student->user->email)->queue(new LessonPublishedMail($lesson));
            }
            
            if ($settings->notification_lesson_published) {
                Notification::create([...]);
            }
            
            $status = $lesson->is_published ? 'published' : 'unpublished';
            return back()->with('success', "Lesson {$status} successfully.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to toggle publish status: ' . $e->getMessage());
        }
    }

    private function notifyStudentsAboutLesson(Lesson $lesson)
    {
        // Get all students enrolled in sections where this subject is taught
        $students = \App\Models\Student::whereHas('enrollments', function($q) use ($lesson) {
            $q->whereHas('section.assignments', function($query) use ($lesson) {
                $query->where('subject_id', $lesson->subject_id)
                      ->where('instructor_id', $lesson->instructor_id);
            })
            ->where('status', 'enrolled');
        })
        ->with('user')
        ->get();
        
        // Send email to each student
        foreach ($students as $student) {
            Mail::to($student->user->email)
                ->queue(new LessonPublishedMail($lesson));
                
            // Create in-app notification
            \App\Models\Notification::create([
                'user_id' => $student->user_id,
                'type' => 'info',
                'title' => 'New Lesson Available',
                'message' => "A new lesson '{$lesson->title}' has been published in {$lesson->subject->subject_name}.",
                'action_url' => route('student.lessons.show', $lesson),
            ]);
        }
    }

    public function duplicate(Lesson $lesson)
    {
        $this->authorize('view', $lesson);

        try {
            $newLesson = $lesson->replicate();
            $newLesson->title = $lesson->title . ' (Copy)';
            $newLesson->is_published = false;
            $newLesson->order = Lesson::where('subject_id', $lesson->subject_id)->max('order') + 1;
            $newLesson->save();

            // Copy file if exists
            if ($lesson->file_path) {
                $oldPath = $lesson->file_path;
                $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newFileName = time() . '_' . Str::random(10) . '.' . $extension;
                $newPath = 'lessons/' . $newFileName;
                
                Storage::disk('public')->copy($oldPath, $newPath);
                
                $newLesson->file_path = $newPath;
                $newLesson->file_name = $newFileName;
                $newLesson->save();
            }

            AuditLog::log('lesson_duplicated', $newLesson);

            return redirect()
                ->route('instructor.lessons.edit', $newLesson)
                ->with('success', 'Lesson duplicated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to duplicate lesson: ' . $e->getMessage());
        }
    }

    public function download(Lesson $lesson)
    {
        $this->authorize('download', $lesson);

        if (!$lesson->file_path || !Storage::disk('public')->exists($lesson->file_path)) {
            return back()->with('error', 'File not found.');
        }

        AuditLog::log('lesson_file_downloaded', $lesson);

        return Storage::disk('public')->download(
            $lesson->file_path,
            $lesson->file_name ?? basename($lesson->file_path)
        );
    }

    public function viewStatistics(Lesson $lesson)
    {
        $this->authorize('view', $lesson);
        
        $stats = $lesson->getViewStats();
        
        // Get view history
        $viewHistory = LessonView::where('lesson_id', $lesson->id)
            ->with('student.user')
            ->orderBy('viewed_at', 'desc')
            ->paginate(50);
        
        // Get completion timeline
        $completionTimeline = LessonView::where('lesson_id', $lesson->id)
            ->where('completed', true)
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(DISTINCT student_id) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Get students who haven't viewed yet
        $instructor = auth()->user()->instructor;
        $enrolledStudents = $this->getEnrolledStudents($lesson->subject_id);
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

    // Helper method
    private function getEnrolledStudents(int $subjectId)
    {
        $instructor = auth()->user()->instructor;
        $currentAcademicYear = now()->format('Y') . '-' . (now()->year + 1);
        $currentSemester = $this->getCurrentSemester();
        
        $sectionIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->where('subject_id', $subjectId)
            ->where('academic_year', $currentAcademicYear)
            ->where('semester', $currentSemester)
            ->pluck('section_id');
        
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
     * Schedule lesson publish/unpublish
     */
    public function schedule(Request $request, Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        $validated = $request->validate([
            'scheduled_publish_at' => ['nullable', 'date', 'after:now'],
            'scheduled_unpublish_at' => ['nullable', 'date', 'after:scheduled_publish_at'],
            'auto_publish' => ['boolean'],
        ]);
        
        try {
            $lesson->update([
                'scheduled_publish_at' => $validated['scheduled_publish_at'] ?? null,
                'scheduled_unpublish_at' => $validated['scheduled_unpublish_at'] ?? null,
                'auto_publish' => $validated['auto_publish'] ?? false,
            ]);
            
            AuditLog::log('lesson_scheduled', $lesson);
            
            $message = 'Lesson scheduling updated successfully.';
            if ($lesson->auto_publish && $lesson->scheduled_publish_at) {
                $message .= ' Will auto-publish on ' . $lesson->scheduled_publish_at->format('M d, Y h:i A');
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    /**
     * Cancel scheduled publish
     */
    public function cancelSchedule(Lesson $lesson)
    {
        $this->authorize('update', $lesson);
        
        try {
            $lesson->update([
                'scheduled_publish_at' => null,
                'scheduled_unpublish_at' => null,
                'auto_publish' => false,
            ]);
            
            AuditLog::log('lesson_schedule_cancelled', $lesson);
            
            return back()->with('success', 'Schedule cancelled successfully.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel schedule: ' . $e->getMessage());
        }
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
            'files.*' => 'required|file|max:51200', // 50MB
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
            activity()
                ->performedOn($lesson)
                ->causedBy(auth()->user())
                ->withProperties([
                    'count' => count($attachments),
                    'filenames' => collect($attachments)->pluck('original_filename')->toArray(),
                ])
                ->log('Uploaded ' . count($attachments) . ' attachment(s) to lesson');

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
            activity()
                ->performedOn($lesson)
                ->causedBy(auth()->user())
                ->withProperties(['filename' => $filename])
                ->log('Deleted attachment from lesson');

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
            activity()
                ->performedOn($lesson)
                ->causedBy(auth()->user())
                ->withProperties([
                    'filename' => $attachment->original_filename,
                    'visible' => $attachment->is_visible,
                ])
                ->log('Toggled attachment visibility');

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
            activity()
                ->performedOn($lesson)
                ->causedBy(auth()->user())
                ->log('Reordered lesson attachments');

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Download attachment (instructor).
     */
    public function downloadAttachment(Lesson $lesson, LessonAttachment $attachment)
    {
        $this->authorize('view', $lesson);

        if ($attachment->lesson_id !== $lesson->id) {
            abort(404);
        }

        return Storage::download($attachment->file_path, $attachment->original_filename);
    }
}