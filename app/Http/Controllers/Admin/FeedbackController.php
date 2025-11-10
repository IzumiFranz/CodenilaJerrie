<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\AuditLog;
use App\Mail\FeedbackResponseMail;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comment', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $feedback = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.feedback.index', compact('feedback'));
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('user', 'feedbackable');
        return view('admin.feedback.show', compact('feedback'));
    }

    public function respond(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'admin_response' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $feedback->respond($validated['admin_response']);

            AuditLog::log('feedback_responded', $feedback);

            // 🔔 SEND RESPONSE EMAIL TO STUDENT
            $settings = $feedback->user->settings ?? (object)[
                'email_feedback_response' => true,
                'notification_feedback_response' => true
            ];
            
            if ($settings->email_feedback_response) {
                Mail::to($feedback->user->email)->queue(new FeedbackResponseMail($feedback));
            }
            
            if ($settings->notification_feedback_response) {
                Notification::create([
                    'user_id' => $feedback->user_id,
                    'type' => 'info',
                    'title' => 'Feedback Response Received',
                    'message' => 'An administrator has responded to your feedback.',
                    'action_url' => route('student.feedback.show', $feedback),
                ]);
            }

            return back()->with('success', 'Response sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send response: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,resolved'],
        ]);

        try {
            $feedback->update(['status' => $validated['status']]);

            AuditLog::log('feedback_status_updated', $feedback);

            return back()->with('success', 'Feedback status updated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
