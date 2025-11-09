<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\QuizTemplate;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class QuizTemplateController extends Controller
{
    public function index()
    {
        $instructor = auth()->user()->instructor;
        
        $templates = QuizTemplate::where('instructor_id', $instructor->id)
            ->orWhere('is_shared', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('instructor.quiz-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('instructor.quiz-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit' => ['required', 'integer', 'min:1', 'max:300'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'randomize_questions' => ['boolean'],
            'randomize_choices' => ['boolean'],
            'show_results' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'allow_review_mode' => ['boolean'],
            'allow_practice_mode' => ['boolean'],
            'is_shared' => ['boolean'],
        ]);

        $instructor = auth()->user()->instructor;
        $validated['instructor_id'] = $instructor->id;

        $template = QuizTemplate::create($validated);

        AuditLog::log('quiz_template_created', $template);

        return redirect()
            ->route('instructor.quiz-templates.index')
            ->with('success', 'Quiz template created successfully.');
    }

    public function destroy(QuizTemplate $quizTemplate)
    {
        $this->authorize('delete', $quizTemplate);

        AuditLog::log('quiz_template_deleted', $quizTemplate);
        $quizTemplate->delete();

        return redirect()
            ->route('instructor.quiz-templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}