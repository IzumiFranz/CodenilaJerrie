<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $feedback = Feedback::where('user_id', $user->id)
            ->with('feedbackable')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('student.feedback.index', compact('feedback'));
    }

    public function create(Request $request)
    {
        $feedbackableType = $request->get('type');
        $feedbackableId = $request->get('id');

        $feedbackable = null;
        if ($feedbackableType && $feedbackableId) {
            $feedbackable = match($feedbackableType) {
                'quiz' => Quiz::findOrFail($feedbackableId),
                'lesson' => Lesson::findOrFail($feedbackableId),
                default => null
            };
        }

        return view('student.feedback.create', compact('feedbackable', 'feedbackableType'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'feedbackable_type' => ['nullable', 'in:App\Models\Quiz,App\Models\Lesson'],
            'feedbackable_id' => ['nullable', 'integer'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        try {
            $feedback = Feedback::create([
                'user_id' => auth()->id(),
                'feedbackable_type' => $validated['feedbackable_type'] ?? null,
                'feedbackable_id' => $validated['feedbackable_id'] ?? null,
                'rating' => $validated['rating'] ?? null,
                'comment' => $validated['comment'],
                'status' => 'pending',
            ]);

            AuditLog::log('feedback_submitted', $feedback);

            return redirect()
                ->route('student.feedback.index')
                ->with('success', 'Thank you for your feedback!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to submit feedback: ' . $e->getMessage());
        }
    }

    public function show(Feedback $feedback)
    {
        // Verify ownership
        if ($feedback->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to feedback.');
        }

        $feedback->load('feedbackable');

        return view('student.feedback.show', compact('feedback'));
    }
}