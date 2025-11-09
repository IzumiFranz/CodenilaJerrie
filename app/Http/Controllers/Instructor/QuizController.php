<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuestionBank;
use App\Models\Subject;
use App\Models\InstructorSubjectSection;
use App\Models\QuizAttempt;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $query = Quiz::where('instructor_id', $instructor->id)
            ->with('subject')
            ->withCount(['questions', 'attempts']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $quizzes = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('instructor.quizzes.index', compact('quizzes', 'subjects'));
    }

    public function create(Request $request)
    {
        $instructor = auth()->user()->instructor;
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        $selectedSubject = $request->get('subject');
        
        // FEATURE: Load template if provided
        $template = null;
        if ($request->has('template')) {
            $template = QuizTemplate::find($request->get('template'));
        }

        return view('instructor.quizzes.create', compact('subjects', 'selectedSubject'));
    }

    public function store(Request $request)
    {
        $instructor = auth()->user()->instructor;

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['required', 'integer', 'min:1', 'max:300'], // minutes
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'randomize_questions' => ['boolean'],
            'randomize_choices' => ['boolean'],
            'show_results' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after:available_from'],
            'is_published' => ['boolean'],
        ]);

        try {
            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            $quiz = Quiz::create([
                'instructor_id' => $instructor->id,
                'subject_id' => $validated['subject_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'time_limit' => $validated['time_limit'],
                'passing_score' => $validated['passing_score'],
                'max_attempts' => $validated['max_attempts'],
                'randomize_questions' => $validated['randomize_questions'] ?? false,
                'randomize_choices' => $validated['randomize_choices'] ?? false,
                'show_results' => $validated['show_results'] ?? true,
                'show_correct_answers' => $validated['show_correct_answers'] ?? true,
                'available_from' => $validated['available_from'] ?? null,
                'available_until' => $validated['available_until'] ?? null,
                'is_published' => $validated['is_published'] ?? false,
            ]);

            AuditLog::log('quiz_created', $quiz);

            return redirect()
                ->route('instructor.quizzes.questions', $quiz)
                ->with('success', 'Quiz created successfully. Now add questions.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create quiz: ' . $e->getMessage());
        }
    }

    public function show(Quiz $quiz)
    {
        $this->authorize('view', $quiz);
        $quiz->load(['subject.course', 'questions', 'attempts']);
        
        return view('instructor.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        
        $instructor = auth()->user()->instructor;
        
        $subjectIds = InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return view('instructor.quizzes.edit', compact('quiz', 'subjects'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['required', 'integer', 'min:1', 'max:300'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'randomize_questions' => ['boolean'],
            'randomize_choices' => ['boolean'],
            'show_results' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after:available_from'],
            'is_published' => ['boolean'],
        ]);

        try {
            $instructor = auth()->user()->instructor;

            $canTeach = InstructorSubjectSection::where('instructor_id', $instructor->id)
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$canTeach) {
                return back()->withInput()
                    ->with('error', 'You are not assigned to teach this subject.');
            }

            $oldValues = $quiz->toArray();
            $quiz->update($validated);

            AuditLog::log('quiz_updated', $quiz, $oldValues, $quiz->toArray());

            return redirect()
                ->route('instructor.quizzes.index')
                ->with('success', 'Quiz updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update quiz: ' . $e->getMessage());
        }
    }

    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);

        try {
            if ($quiz->attempts()->where('status', 'completed')->count() > 0) {
                return back()->with('error', 'Cannot delete quiz with completed attempts.');
            }

            AuditLog::log('quiz_deleted', $quiz);
            $quiz->delete();

            return redirect()
                ->route('instructor.quizzes.index')
                ->with('success', 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete quiz: ' . $e->getMessage());
        }
    }

    public function togglePublish(Quiz $quiz)
    {
        $this->authorize('publish', $quiz);

        try {
            if (!$quiz->is_published && $quiz->questions()->count() === 0) {
                return back()->with('error', 'Cannot publish quiz without questions.');
            }

            $quiz->is_published = !$quiz->is_published;
            $quiz->save();

            AuditLog::log('quiz_publish_toggled', $quiz);

            // TODO: Notify enrolled students if published

            $status = $quiz->is_published ? 'published' : 'unpublished';
            return back()->with('success', "Quiz {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to toggle publish status: ' . $e->getMessage());
        }
    }

    public function manageQuestions(Quiz $quiz)
    {
        $this->authorize('manageQuestions', $quiz);
        
        $quiz->load(['questions.choices']);
        
        $instructor = auth()->user()->instructor;
        $availableQuestions = QuestionBank::where('instructor_id', $instructor->id)
            ->where('subject_id', $quiz->subject_id)
            ->whereNotIn('id', $quiz->questions->pluck('id'))
            ->with('choices')
            ->get();

        return view('instructor.quizzes.questions', compact('quiz', 'availableQuestions'));
    }

    public function addQuestion(Request $request, Quiz $quiz)
    {
        $this->authorize('manageQuestions', $quiz);

        $validated = $request->validate([
            'question_id' => ['required', 'exists:question_bank,id'],
        ]);

        try {
            $question = QuestionBank::findOrFail($validated['question_id']);
            
            if ($question->subject_id !== $quiz->subject_id) {
                return back()->with('error', 'Question must be from the same subject.');
            }

            $maxOrder = $quiz->questions()->max('order') ?? 0;
            
            $quiz->questions()->attach($validated['question_id'], [
                'order' => $maxOrder + 1
            ]);

            AuditLog::log('quiz_question_added', $quiz);

            return back()->with('success', 'Question added to quiz.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add question: ' . $e->getMessage());
        }
    }

    public function removeQuestion(Quiz $quiz, QuestionBank $question)
    {
        $this->authorize('manageQuestions', $quiz);

        try {
            $quiz->questions()->detach($question->id);

            AuditLog::log('quiz_question_removed', $quiz);

            return back()->with('success', 'Question removed from quiz.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove question: ' . $e->getMessage());
        }
    }

    public function reorderQuestions(Request $request, Quiz $quiz)
    {
        $this->authorize('manageQuestions', $quiz);

        $validated = $request->validate([
            'questions' => ['required', 'array'],
            'questions.*.id' => ['required', 'exists:question_bank,id'],
            'questions.*.order' => ['required', 'integer', 'min:1'],
        ]);

        try {
            DB::beginTransaction();

            foreach ($validated['questions'] as $questionData) {
                DB::table('quiz_question')
                    ->where('quiz_id', $quiz->id)
                    ->where('question_bank_id', $questionData['id'])
                    ->update(['order' => $questionData['order']]);
            }

            DB::commit();

            AuditLog::log('quiz_questions_reordered', $quiz);

            return back()->with('success', 'Questions reordered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reorder questions: ' . $e->getMessage());
        }
    }

    public function results(Quiz $quiz)
    {
        $this->authorize('viewResults', $quiz);

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with(['student.user'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return view('instructor.quizzes.results', compact('quiz', 'attempts'));
    }

    public function analytics(Quiz $quiz)
    {
        $this->authorize('viewResults', $quiz);

        // TODO: Implement comprehensive analytics
        
        return view('instructor.quizzes.analytics', compact('quiz'));
    }

    public function duplicate(Quiz $quiz)
    {
        $this->authorize('view', $quiz);

        try {
            DB::beginTransaction();

            $newQuiz = $quiz->replicate();
            $newQuiz->title = $quiz->title . ' (Copy)';
            $newQuiz->is_published = false;
            $newQuiz->save();

            // Copy questions with their order
            foreach ($quiz->questions as $question) {
                $newQuiz->questions()->attach($question->id, [
                    'order' => $question->pivot->order
                ]);
            }

            DB::commit();

            AuditLog::log('quiz_duplicated', $newQuiz);

            return redirect()
                ->route('instructor.quizzes.edit', $newQuiz)
                ->with('success', 'Quiz duplicated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to duplicate quiz: ' . $e->getMessage());
        }
    }
}