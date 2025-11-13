<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    /**
     * List all feedback submitted by the student
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user->student) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student',
            ], 403);
        }

        $feedbacks = Feedback::where('user_id', $user->id)
            ->with(['user', 'feedbackable'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $feedbacks,
        ]);
    }

    /**
     * Store a new feedback
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->student) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a student',
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:quiz,lesson,instructor,general',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'feedbackable_type' => 'nullable|string',
            'feedbackable_id' => 'nullable|integer',
            'is_anonymous' => 'boolean',
        ]);

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? null,
            'feedbackable_type' => $validated['feedbackable_type'] ?? null,
            'feedbackable_id' => $validated['feedbackable_id'] ?? null,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'data' => $feedback->load(['user', 'feedbackable']),
        ]);
    }

    /**
     * Show feedback details
     */
    public function show(Request $request, Feedback $feedback)
    {
        $user = $request->user();

        if ($feedback->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this feedback',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $feedback->load(['user', 'feedbackable']),
        ]);
    }
}
