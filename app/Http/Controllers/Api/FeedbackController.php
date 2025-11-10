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
        $student = $request->user()->student;

        $feedbacks = Feedback::where('student_id', $student->id)
            ->with('instructor')
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
        $student = $request->user()->student;

        $validated = $request->validate([
            'instructor_id' => 'required|exists:instructors,id',
            'message' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $feedback = Feedback::create([
            'student_id' => $student->id,
            'instructor_id' => $validated['instructor_id'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'data' => $feedback->load('instructor'),
        ]);
    }

    /**
     * Show feedback details
     */
    public function show(Request $request, Feedback $feedback)
    {
        $student = $request->user()->student;

        if ($feedback->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this feedback',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $feedback->load('instructor'),
        ]);
    }
}
