<?php


namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\InstructorSubjectSection;
use App\Models\AuditLog;
use Illuminate\Http\Request;
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
            $lesson->is_published = !$lesson->is_published;
            $lesson->save();

            AuditLog::log('lesson_publish_toggled', $lesson);

            // TODO: Notify students if published
            // if ($lesson->is_published) {
            //     Mail::to($enrolledStudents)->send(new LessonPublishedMail($lesson));
            // }

            $status = $lesson->is_published ? 'published' : 'unpublished';
            return back()->with('success', "Lesson {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to toggle publish status: ' . $e->getMessage());
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
}