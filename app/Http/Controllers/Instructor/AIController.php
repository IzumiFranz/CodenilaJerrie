<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AIJob;
use App\Models\Subject;
use App\Models\Lesson;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Jobs\ProcessAIJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    /**
     * Display AI Assistant Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        if (!$user->isInstructor()) {
            abort(403, 'Only instructors can access AI features.');
        }

        // Get recent jobs
        $recentJobs = AIJob::where('user_id', $user->id)
            ->with(['subject'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $stats = [
            'total_generated' => AIJob::where('user_id', $user->id)
                ->where('job_type', 'generate_questions')
                ->where('status', 'completed')
                ->count(),
            'total_validated' => AIJob::where('user_id', $user->id)
                ->where('job_type', 'validate_question')
                ->where('status', 'completed')
                ->count(),
            'total_analyzed' => AIJob::where('user_id', $user->id)
                ->where('job_type', 'analyze_quiz')
                ->where('status', 'completed')
                ->count(),
            'monthly_jobs' => AIJob::where('user_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'monthly_generated' => AIJob::where('user_id', $user->id)
                ->where('job_type', 'generate_questions')
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'avg_validation_score' => QuestionBank::where('instructor_id', $user->instructor->id)
                ->where('is_validated', true)
                ->whereNotNull('quality_score')
                ->avg('quality_score') ?? 0,
            'estimated_cost' => 0, // Can be calculated based on API usage
            'avg_generation_time' => '45s',
            'success_rate' => '98%',
            'questions_per_hour' => '120',
            'monthly_limit' => '500',
        ];

        // Chart data (last 4 weeks)
        $chartData = [
            'generated' => [],
            'validated' => []
        ];
        
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $chartData['generated'][] = AIJob::where('user_id', $user->id)
                ->where('job_type', 'generate_questions')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();
            
            $chartData['validated'][] = AIJob::where('user_id', $user->id)
                ->where('job_type', 'validate_question')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->count();
        }

        // Check if there are processing jobs
        $hasProcessingJobs = $recentJobs->where('status', 'processing')->isNotEmpty();

        // Get subjects for the modal
        $instructor = $user->instructor;
        $subjectIds = \App\Models\InstructorSubjectSection::where('instructor_id', $instructor->id)
            ->pluck('subject_id')
            ->unique();
        $subjects = \App\Models\Subject::whereIn('id', $subjectIds)->get();

        return view('instructor.ai.dashboard', compact('stats', 'recentJobs', 'chartData', 'hasProcessingJobs', 'subjects'));
    }

    /**
     * Display AI job history
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isInstructor()) {
            abort(403, 'Only instructors can access AI features.');
        }
        
        $query = AIJob::where('user_id', $user->id)
            ->with(['subject']);

        // Filters
        if ($request->filled('type')) {
            $query->where('job_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_generated' => AIJob::where('user_id', auth()->id())
                ->where('job_type', 'generate_questions')
                ->where('status', 'completed')
                ->count(),
            'total_validated' => AIJob::where('user_id', auth()->id())
                ->where('job_type', 'validate_question')
                ->where('status', 'completed')
                ->count(),
            'total_analyzed' => AIJob::where('user_id', auth()->id())
                ->where('job_type', 'analyze_quiz')
                ->where('status', 'completed')
                ->count(),
            'monthly_jobs' => AIJob::where('user_id', auth()->id())
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('instructor.ai.index', compact('jobs', 'stats'));
    }

    /**
     * Show AI job details
     */
    public function show(AIJob $job)
    {
        $user = auth()->user();
        
        if (!$user->isInstructor() && !$user->isAdmin()) {
            abort(403, 'Unauthorized to view this job');
        }
        
        // Check authorization
        if ($job->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Unauthorized to view this job');
        }

        $job->load(['subject', 'user']);

        // Load generated questions if job is completed and has question IDs
        $generatedQuestions = collect();
        if ($job->status === 'completed' && 
            $job->job_type === 'generate_questions' && 
            isset($job->result['question_ids'])) {
            $generatedQuestions = QuestionBank::whereIn('id', $job->result['question_ids'])
                ->with(['choices', 'subject'])
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('instructor.ai.show', compact('job', 'generatedQuestions'));
    }

    /**
     * Generate questions using AI
     */
    public function generateQuestions(Request $request)
    {
        // Authorization check
        if (!auth()->user()->isInstructor()) {
            return response()->json([
                'success' => false,
                'message' => 'Only instructors can generate questions using AI.'
            ], 403);
        }

        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'lesson_ids' => ['required', 'array', 'min:1'],
            'lesson_ids.*' => ['exists:lessons,id'],
            'count' => ['required', 'integer', 'min:1', 'max:50'],
            'difficulty' => ['required', 'in:easy,medium,hard'],
            'types' => ['required', 'array', 'min:1'],
            'types.*' => ['in:multiple_choice,true_false,identification,essay'],
        ]);

        try {
            // Verify lessons belong to subject
            $lessons = Lesson::whereIn('id', $validated['lesson_ids'])
                ->where('subject_id', $validated['subject_id'])
                ->where('is_published', true)
                ->pluck('id');

            if ($lessons->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid published lessons found'
                ], 422);
            }

            // Create AI job
            $job = AIJob::create([
                'user_id' => auth()->id(),
                'subject_id' => $validated['subject_id'],
                'job_type' => 'generate_questions',
                'parameters' => $validated,
                'status' => 'pending',
            ]);

            // Dispatch job to queue
            ProcessAIJob::dispatch($job);

            return response()->json([
                'success' => true,
                'message' => 'Question generation started. You will be notified when complete.',
                'job_id' => $job->id
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Question Generation Failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start question generation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate question using AI
     */
    public function validateQuestion(Request $request, QuestionBank $question)
    {
        // Authorization checks
        if (!auth()->user()->isInstructor()) {
            return response()->json([
                'success' => false,
                'message' => 'Only instructors can validate questions using AI.'
            ], 403);
        }

        // Check if instructor owns the question
        if ($question->instructor_id !== auth()->user()->instructor->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not own this question'
            ], 403);
        }

        try {
            // Create AI job
            $job = AIJob::create([
                'user_id' => auth()->id(),
                'subject_id' => $question->subject_id,
                'job_type' => 'validate_question',
                'parameters' => [
                    'question_id' => $question->id
                ],
                'status' => 'pending',
            ]);

            // Dispatch job to queue
            ProcessAIJob::dispatch($job);

            return response()->json([
                'success' => true,
                'message' => 'Question validation started.',
                'job_id' => $job->id
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Question Validation Failed', [
                'question_id' => $question->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analyze quiz using AI
     */
    public function analyzeQuiz(Request $request, Quiz $quiz)
    {
        // Handle GET request - show confirmation or redirect
        if ($request->isMethod('get')) {
            $attemptsCount = $quiz->attempts()->where('status', 'completed')->count();
            if ($attemptsCount < 5) {
                return redirect()->route('instructor.quizzes.show', $quiz)
                    ->with('error', 'This quiz needs at least 5 completed attempts for meaningful analysis.');
            }
            // For GET, just return JSON for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Ready to analyze',
                'attempts_count' => $attemptsCount
            ]);
        }
        
        // Handle POST request - start analysis
        // Authorization checks
        if (!auth()->user()->isInstructor()) {
            return response()->json([
                'success' => false,
                'message' => 'Only instructors can analyze quizzes using AI.'
            ], 403);
        }

        // Check if instructor owns the quiz
        if ($quiz->instructor_id !== auth()->user()->instructor->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not own this quiz'
            ], 403);
        }

        // Check if quiz has enough attempts
        $attemptsCount = $quiz->attempts()->where('status', 'completed')->count();
        if ($attemptsCount < 5) {
            return response()->json([
                'success' => false,
                'message' => 'This quiz needs at least 5 completed attempts for meaningful analysis.'
            ], 422);
        }

        try {
            // Create AI job
            $job = AIJob::create([
                'user_id' => auth()->id(),
                'subject_id' => $quiz->subject_id,
                'job_type' => 'analyze_quiz',
                'parameters' => [
                    'quiz_id' => $quiz->id
                ],
                'status' => 'pending',
            ]);

            // Dispatch job to queue
            ProcessAIJob::dispatch($job);

            return response()->json([
                'success' => true,
                'message' => 'Quiz analysis started.',
                'job_id' => $job->id
            ]);

        } catch (\Exception $e) {
            \Log::error('AI Quiz Analysis Failed', [
                'quiz_id' => $quiz->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start analysis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check job status (for polling)
     */
    public function checkStatus(AIJob $job)
    {
        // Check authorization
        if ($job->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'status' => $job->status,
            'result' => $job->result,
            'error_message' => $job->error_message,
            'completed_at' => $job->completed_at?->toISOString(),
            'started_at' => $job->started_at?->toISOString(),
        ]);
    }

    /**
     * Get lessons by subject (AJAX endpoint)
     */
    public function getLessonsBySubject(Subject $subject)
    {
        // Verify instructor can access this subject
        $canAccess = $subject->instructors()
            ->where('instructors.id', auth()->user()->instructor->id)
            ->exists();

        if (!$canAccess && !auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this subject'
            ], 403);
        }

        $lessons = Lesson::where('subject_id', $subject->id)
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'order']);

        return response()->json($lessons);
    }

    /**
     * Get AI usage statistics
     */
    public function statistics()
    {
        $stats = [
            'total_jobs' => AIJob::where('user_id', auth()->id())->count(),
            'completed_jobs' => AIJob::where('user_id', auth()->id())
                ->where('status', 'completed')
                ->count(),
            'failed_jobs' => AIJob::where('user_id', auth()->id())
                ->where('status', 'failed')
                ->count(),
            'processing_jobs' => AIJob::where('user_id', auth()->id())
                ->where('status', 'processing')
                ->count(),
            
            // Questions generated
            'questions_generated' => DB::table('question_bank')
                ->where('instructor_id', auth()->user()->instructor->id)
                ->where('is_validated', true)
                ->where('created_at', '>=', now()->subMonth())
                ->count(),
            
            // By job type
            'by_type' => AIJob::where('user_id', auth()->id())
                ->select('job_type', DB::raw('count(*) as count'))
                ->groupBy('job_type')
                ->pluck('count', 'job_type'),
            
            // Recent activity (last 7 days)
            'recent_activity' => AIJob::where('user_id', auth()->id())
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->get(['job_type', 'status', 'created_at']),
        ];

        return response()->json($stats);
    }

    /**
     * Cancel pending job
     */
    public function cancel(AIJob $job)
    {
        if ($job->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($job->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Can only cancel pending jobs'
            ], 422);
        }

        $job->update(['status' => 'failed', 'error_message' => 'Cancelled by user']);

        return response()->json([
            'success' => true,
            'message' => 'Job cancelled successfully'
        ]);
    }

    /**
     * Retry failed job
     */
    public function retry(AIJob $job)
    {
        if ($job->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($job->status !== 'failed') {
            return response()->json([
                'success' => false,
                'message' => 'Can only retry failed jobs'
            ], 422);
        }

        // Reset job status
        $job->update([
            'status' => 'pending',
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ]);

        // Dispatch again
        ProcessAIJob::dispatch($job);

        return response()->json([
            'success' => true,
            'message' => 'Job queued for retry'
        ]);
    }
}