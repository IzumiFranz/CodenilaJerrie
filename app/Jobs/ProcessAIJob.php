<?php

namespace App\Jobs;

use App\Models\AIJob;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 180;
    public $aiJob;

    public function __construct(AIJob $aiJob)
    {
        $this->aiJob = $aiJob;
    }

    public function handle(AIService $aiService)
    {
        $this->aiJob->start();

        try {
            $result = match($this->aiJob->job_type) {
                'generate_questions' => $this->generateQuestions($aiService),
                'validate_question' => $this->validateQuestion($aiService),
                'analyze_quiz' => $this->analyzeQuiz($aiService),
                'grade_essay' => $this->gradeEssay($aiService),
                default => throw new \Exception('Unknown job type')
            };

            $this->aiJob->complete($result);
            $this->notifyUser('completed');

        } catch (\Exception $e) {
            Log::error('AI Job Failed', [
                'job_id' => $this->aiJob->id,
                'error' => $e->getMessage()
            ]);

            $this->aiJob->fail($e->getMessage());
            $this->notifyUser('failed');
            throw $e;
        }
    }

    private function generateQuestions(AIService $aiService): array
    {
        $params = $this->aiJob->parameters;
        
        // Add user_id to parameters for AIService to use
        if (!isset($params['user_id'])) {
            $params['user_id'] = $this->aiJob->user_id;
        }
        
        $result = $aiService->generateQuestions($params);

        return $result;
    }

    private function validateQuestion(AIService $aiService): array
    {
        $params = $this->aiJob->parameters;
        $question = \App\Models\QuestionBank::find($params['question_id']);

        if (!$question) {
            throw new \Exception('Question not found');
        }

        return $aiService->validateQuestion($question);
    }

    private function analyzeQuiz(AIService $aiService): array
    {
        $params = $this->aiJob->parameters;
        $quiz = \App\Models\Quiz::find($params['quiz_id']);

        if (!$quiz) {
            throw new \Exception('Quiz not found');
        }

        return $aiService->analyzeQuiz($quiz);
    }

    private function gradeEssay(AIService $aiService): array
    {
        // Essay grading not yet implemented in AIService
        throw new \Exception('Essay grading is not yet implemented');
    }


    private function notifyUser(string $status): void
    {
        $title = match($status) {
            'completed' => 'AI Task Completed',
            'failed' => 'AI Task Failed',
            default => 'AI Task Update'
        };

        $message = match($this->aiJob->job_type) {
            'generate_questions' => "Question generation {$status}",
            'validate_question' => "Question validation {$status}",
            'analyze_quiz' => "Quiz analysis {$status}",
            'grade_essay' => "Essay grading {$status}",
            default => "AI task {$status}"
        };

        \App\Models\Notification::create([
            'user_id' => $this->aiJob->user_id,
            'type' => $status === 'completed' ? 'success' : 'danger',
            'title' => $title,
            'message' => $message,
            'action_url' => $this->getActionUrl(),
        ]);
    }

    private function getActionUrl(): ?string
    {
        return match($this->aiJob->job_type) {
            'generate_questions' => route('instructor.question-bank.index'),
            'validate_question' => route('instructor.question-bank.index'),
            'analyze_quiz' => route('instructor.quizzes.index'),
            default => route('instructor.dashboard')
        };
    }

    public function failed(\Throwable $exception)
    {
        Log::error('AI Job Failed Permanently', [
            'job_id' => $this->aiJob->id,
            'exception' => $exception->getMessage()
        ]);

        $this->aiJob->fail($exception->getMessage());
        $this->notifyUser('failed');
    }
}