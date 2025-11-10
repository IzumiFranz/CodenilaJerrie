<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\QuestionTag;
use App\Models\Subject;
use App\Models\InstructorSubjectSection;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class QuestionTagController extends Controller
{
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $query = QuestionTag::where('instructor_id', $instructor->id)
            ->with('subject');
        
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        $tags = $query->orderBy('name')->paginate(20);
        
        // Get subjects for filter
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        
        return view('instructor.question-tags.index', compact('tags', 'subjects'));
    }

    public function create()
    {
        $instructor = auth()->user()->instructor;
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        
        return view('instructor.question-tags.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        
        try {
            // Generate unique slug
            $baseSlug = QuestionTag::generateSlug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            
            while (QuestionTag::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            
            // Verify subject if provided
            if (isset($validated['subject_id'])) {
                $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                    ->where('subject_id', $validated['subject_id'])
                    ->exists();
                
                if (!$canTeach) {
                    return back()->withInput()
                        ->with('error', 'You are not assigned to teach this subject.');
                }
            }
            
            $tag = QuestionTag::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $validated['subject_id'] ?? null,
                'name' => $validated['name'],
                'slug' => $slug,
                'color' => $validated['color'],
                'description' => $validated['description'] ?? null,
            ]);
            
            AuditLog::log('question_tag_created', $tag);
            
            return redirect()
                ->route('instructor.question-tags.index')
                ->with('success', 'Tag created successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create tag: ' . $e->getMessage());
        }
    }

    public function edit(QuestionTag $questionTag)
    {
        $instructor = auth()->user()->instructor;
        
        if ($questionTag->instructor_id !== $instructor->id) {
            abort(403);
        }
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();
        
        return view('instructor.question-tags.edit', compact('questionTag', 'subjects'));
    }

    public function update(Request $request, QuestionTag $questionTag)
    {
        $instructor = auth()->user()->instructor;
        
        if ($questionTag->instructor_id !== $instructor->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        
        try {
            // Update slug if name changed
            if ($validated['name'] !== $questionTag->name) {
                $baseSlug = QuestionTag::generateSlug($validated['name']);
                $slug = $baseSlug;
                $counter = 1;
                
                while (QuestionTag::where('slug', $slug)->where('id', '!=', $questionTag->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $validated['slug'] = $slug;
            }
            
            // Verify subject if provided
            if (isset($validated['subject_id'])) {
                $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                    ->where('subject_id', $validated['subject_id'])
                    ->exists();
                
                if (!$canTeach) {
                    return back()->withInput()
                        ->with('error', 'You are not assigned to teach this subject.');
                }
            }
            
            $oldValues = $questionTag->toArray();
            $questionTag->update($validated);
            
            AuditLog::log('question_tag_updated', $questionTag, $oldValues, $questionTag->toArray());
            
            return redirect()
                ->route('instructor.question-tags.index')
                ->with('success', 'Tag updated successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update tag: ' . $e->getMessage());
        }
    }

    public function destroy(QuestionTag $questionTag)
    {
        $instructor = auth()->user()->instructor;
        
        if ($questionTag->instructor_id !== $instructor->id) {
            abort(403);
        }
        
        try {
            AuditLog::log('question_tag_deleted', $questionTag);
            
            $questionTag->delete();
            
            return redirect()
                ->route('instructor.question-tags.index')
                ->with('success', 'Tag deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete tag: ' . $e->getMessage());
        }
    }

    /**
     * Get tags for AJAX
     */
    public function getTags(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $query = QuestionTag::where('instructor_id', $instructor->id);
        
        if ($request->filled('subject_id')) {
            $query->where(function($q) use ($request) {
                $q->where('subject_id', $request->subject_id)
                  ->orWhereNull('subject_id');
            });
        }
        
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        $tags = $query->orderBy('name')->get();
        
        return response()->json($tags);
    }
}