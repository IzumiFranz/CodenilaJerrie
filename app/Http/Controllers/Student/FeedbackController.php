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

        // Gather counts for stats cards
        $totalFeedback = $feedback->total();
        $pendingFeedback = $feedback->where('status', 'pending')->count();
        $respondedFeedback = $feedback->where('status', 'responded')->count();
        $averageRating = $feedback->avg('rating') ?? 0;

        // Include quizzes, lessons, instructors for the form
        $quizzes = Quiz::all();
        $lessons = Lesson::all();
        $instructors = User::where('role', 'instructor')->get();

        // Determine if we need to open the submit tab (if validation fails)
        $openSubmitTab = session()->getOldInput() ? true : false;
        return view('student.feedback.index', compact(
            'feedback', 
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
    
        try {
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
    
            return redirect()->route('student.feedback.index')
                ->with('success', 'Thank you for your feedback!');
    
        } catch (\Exception $e) {
            return back()->withInput()
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