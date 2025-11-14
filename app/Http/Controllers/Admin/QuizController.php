<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with(['instructor.user', 'subject'])->withCount(['questions', 'attempts']);

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

        $quizzes = $query->orderBy('created_at', 'desc')->paginate(20);
        $subjects = Subject::where('is_active', true)->get();

        return view('admin.quizzes.index', compact('quizzes', 'subjects'));
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['instructor.user', 'subject.course', 'questions', 'attempts']);
        return view('admin.quizzes.show', compact('quiz'));
    }

    public function results(Quiz $quiz)
    {
        $attempts = $quiz->attempts()
            ->with(['student.user'])
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return view('admin.quizzes.results', compact('quiz', 'attempts'));
    }

    public function destroy(Quiz $quiz)
    {
        try {
            if ($quiz->attempts()->where('status', 'completed')->count() > 0) {
                return back()->with('error', 'Cannot delete quiz with completed attempts.');
            }

            AuditLog::log('quiz_deleted_by_admin', $quiz);
            $quiz->delete();

            return redirect()
                ->route('admin.quizzes.index')
                ->with('success', 'Quiz deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete quiz: ' . $e->getMessage());
        }
    }

    public function trashed(Request $request)
    {
        $query = Quiz::onlyTrashed()->with(['instructor.user', 'subject'])->withCount(['questions', 'attempts']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $quizzes = $query->orderBy('deleted_at', 'desc')->paginate(20);
        $subjects = Subject::where('is_active', true)->get();

        return view('admin.quizzes.trashed', compact('quizzes', 'subjects'));
    }

    public function restore($id)
    {
        try {
            $quiz = Quiz::onlyTrashed()->findOrFail($id);
            $quiz->restore();

            AuditLog::log('quiz_restored', $quiz);

            return redirect()
                ->route('admin.quizzes.trashed')
                ->with('success', 'Quiz restored successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore quiz: ' . $e->getMessage());
        }
    }

    public function forceDelete($id)
    {
        try {
            $quiz = Quiz::onlyTrashed()->findOrFail($id);
            
            if ($quiz->attempts()->where('status', 'completed')->count() > 0) {
                return back()->with('error', 'Cannot permanently delete quiz with completed attempts.');
            }

            AuditLog::log('quiz_force_deleted', $quiz);
            $quiz->forceDelete();

            return redirect()
                ->route('admin.quizzes.trashed')
                ->with('success', 'Quiz permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete quiz: ' . $e->getMessage());
        }
    }
}