<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Quiz;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Mail\FeedbackSubmittedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Feedback::where('user_id', $user->id)->with('feedbackable');

        // Optional filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $feedbacks = $query->orderByDesc('created_at')->paginate(12);

        // Stats
        $totalFeedback = Feedback::where('user_id', $user->id)->count();
        $pendingFeedback = Feedback::where('user_id', $user->id)->where('status', 'pending')->count();
        $respondedFeedback = Feedback::where('user_id', $user->id)->where('status', 'responded')->count();
        $averageRating = Feedback::where('user_id', $user->id)->whereNotNull('rating')->avg('rating') ?? 0;

        // Dropdown data for the submit form (inline tab)
        $quizzes = Quiz::where('is_published', true)->get();
        $lessons = Lesson::where('is_published', true)->get();
        $instructors = User::where('role', 'instructor')->get();

        // Open "Submit Feedback" tab if validation failed previously
        $openSubmitTab = session()->getOldInput() ? true : false;

        return view('student.feedback.index', compact(
            'feedbacks',
            'totalFeedback',
            'pendingFeedback',
            'respondedFeedback',
            'averageRating',
            'quizzes',
            'lessons',
            'instructors',
            'openSubmitTab'
        ));
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
            'type' => ['required', 'in:quiz,lesson,instructor,general'],
            'quiz_id' => ['nullable', 'integer'],
            'lesson_id' => ['nullable', 'integer'],
            'instructor_id' => ['nullable', 'integer'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:20', 'max:2000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);
    
        try{
                $feedback = Feedback::create([
                'user_id' => auth()->id(),
                'feedbackable_type' => match($validated['type'] ?? 'general') {
                    'quiz' => Quiz::class,
                    'lesson' => Lesson::class,
                    'instructor' => null, // adjust if needed
                    default => null,
                },
                'feedbackable_id' => $validated['quiz_id'] ?? $validated['lesson_id'] ?? $validated['instructor_id'] ?? null,
                'rating' => $validated['rating'] ?? null,
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'status' => 'pending',
            ]);
    
            AuditLog::log('feedback_submitted', $feedback);
            // Notify instructor if feedback is for a lesson or quiz
            $instructor = null;
            if ($feedback->feedbackable_type === Lesson::class) {
                $instructor = $feedback->feedbackable->instructor;
            } elseif ($feedback->feedbackable_type === Quiz::class) {
                $instructor = $feedback->feedbackable->instructor;
            }

            if ($instructor) {
                $settings = $instructor->user->settings ?? (object)[
                    'email_feedback_submitted' => true,
                    'notification_feedback_submitted' => true
                ];

                if ($settings->email_feedback_submitted) {
                    Mail::to($instructor->user->email)->queue(new FeedbackSubmittedMail($feedback));
                }

                if ($settings->notification_feedback_submitted) {
                    Notification::create([
                        'user_id' => $instructor->user_id,
                        'type' => 'info',
                        'title' => 'New Feedback',
                        'message' => 'A student submitted feedback',
                        'action_url' => route('admin.feedback.show', $feedback),
                    ]);
                }
            }
                return redirect()->route('student.feedback.index')
                    ->with('success', 'Thank you for your feedback!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to submit feedback: ' . $e->getMessage());
        }
    }
    

    public function show(Feedback $feedback)
    {
        // Ownership check
        abort_if($feedback->user_id !== auth()->id(), 403, 'Unauthorized access to feedback.');

        $feedback->load(['feedbackable', 'response_by', 'user']);

        $relatedFeedbacks = Feedback::where('user_id', auth()->id())
            ->where('type', $feedback->type)
            ->where('id', '!=', $feedback->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('student.feedback.show', compact('feedback', 'relatedFeedbacks'));
    }

    public function destroy(Feedback $feedback)
    {
        abort_if($feedback->user_id !== auth()->id(), 403, 'Unauthorized');
        abort_if($feedback->status !== 'pending', 403, 'Only pending feedback can be deleted.');

        $feedback->delete();

        return redirect()->route('student.feedback.index')
            ->with('success', 'Feedback deleted successfully.');
    }
}