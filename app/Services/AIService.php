<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\Choice;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $model = 'gpt-4o'; // Use gpt-4o or gpt-3.5-turbo
    protected $maxTokens = 4000;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file.');
        }
    }

    /**
     * Generate questions from lessons using AI
     */
    public function generateQuestions(array $parameters): array
    {
        try {
            $lessons = Lesson::whereIn('id', $parameters['lesson_ids'])
                ->where('subject_id', $parameters['subject_id'])
                ->get();

            if ($lessons->isEmpty()) {
                throw new \Exception('No lessons found');
            }

            // Prepare lesson content
            $lessonContent = $this->prepareLessonContent($lessons);

            // Generate questions
            $prompt = $this->buildGenerationPrompt($lessonContent, $parameters);
            
            $response = $this->callOpenAI($prompt);
            
            $questions = $this->parseGeneratedQuestions($response);

            // Get instructor ID from user who created the job
            $instructorId = \App\Models\User::find($parameters['user_id'] ?? null)?->instructor?->id;
            
            if (!$instructorId) {
                throw new \Exception('Unable to determine instructor for question generation');
            }

            // Save questions to database
            $savedQuestions = $this->saveGeneratedQuestions(
                $questions, 
                $parameters['subject_id'],
                $instructorId
            );

            return [
                'success' => true,
                'questions' => $savedQuestions,
                'question_ids' => array_map(fn($q) => $q->id, $savedQuestions),
                'count' => count($savedQuestions)
            ];

        } catch (\Exception $e) {
            Log::error('AI Question Generation Failed', [
                'error' => $e->getMessage(),
                'parameters' => $parameters
            ]);

            throw $e;
        }
    }

    /**
     * Validate question quality using AI
     */
    public function validateQuestion(QuestionBank $question): array
    {
        try {
            $prompt = $this->buildValidationPrompt($question);
            
            $response = $this->callOpenAI($prompt);
            
            $validation = $this->parseValidationResponse($response);

            // Update question with validation results
            $question->update([
                'is_validated' => true,
                'quality_score' => $validation['quality_score'] ?? 0
            ]);

            return [
                'success' => true,
                'validation' => $validation
            ];

        } catch (\Exception $e) {
            Log::error('AI Question Validation Failed', [
                'question_id' => $question->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Analyze quiz performance using AI
     */
    public function analyzeQuiz(Quiz $quiz): array
    {
        try {
            // Get quiz statistics
            $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('status', 'completed')
                ->with(['answers.question'])
                ->get();

            if ($attempts->count() < 5) {
                throw new \Exception('Need at least 5 completed attempts for analysis');
            }

            $statistics = $this->calculateQuizStatistics($quiz, $attempts);
            
            $prompt = $this->buildAnalysisPrompt($quiz, $statistics);
            
            $response = $this->callOpenAI($prompt);
            
            $analysis = $this->parseAnalysisResponse($response);

            return [
                'success' => true,
                'analysis' => $analysis,
                'statistics' => $statistics
            ];

        } catch (\Exception $e) {
            Log::error('AI Quiz Analysis Failed', [
                'quiz_id' => $quiz->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Prepare lesson content for AI processing
     */
    protected function prepareLessonContent($lessons)
    {
        $content = '';
        foreach ($lessons as $lesson) {
            $content .= "Lesson: {$lesson->title}\n\n";
            $content .= strip_tags($lesson->content) . "\n\n";
            $content .= "---\n\n";
        }
        return $content;
    }

    /**
     * Build prompt for question generation
     */
    protected function buildGenerationPrompt($lessonContent, $parameters)
    {
        $types = implode(', ', $parameters['types']);
        $count = $parameters['count'];
        $difficulty = $parameters['difficulty'];

        return <<<PROMPT
You are an expert educational content creator. Generate {$count} high-quality quiz questions based on the following lesson content.

LESSON CONTENT:
{$lessonContent}

REQUIREMENTS:
- Question Types: {$types}
- Difficulty Level: {$difficulty}
- Total Questions: {$count}
- Distribute question types evenly
- Ensure questions are clear, accurate, and pedagogically sound
- For multiple choice questions, provide 4 options with only ONE correct answer
- Include distractors that are plausible but clearly wrong
- For true/false questions, ensure they test understanding, not memorization
- For identification questions, provide clear hints
- For essay questions, provide clear rubrics

FORMAT YOUR RESPONSE AS JSON:
{
  "questions": [
    {
      "type": "multiple_choice|true_false|identification|essay",
      "question_text": "The question text",
      "difficulty": "easy|medium|hard",
      "points": 1-10,
      "bloom_level": "remember|understand|apply|analyze|evaluate|create",
      "correct_answer": "For identification/true_false/essay",
      "choices": [
        {"text": "Option A", "is_correct": true},
        {"text": "Option B", "is_correct": false},
        {"text": "Option C", "is_correct": false},
        {"text": "Option D", "is_correct": false}
      ],
      "explanation": "Why this is the correct answer"
    }
  ]
}

Generate ONLY valid JSON. No additional text.
PROMPT;
    }

    /**
     * Build prompt for question validation
     */
    protected function buildValidationPrompt(QuestionBank $question)
    {
        $questionText = $question->question_text;
        $type = $question->type;
        $choices = '';

        if (in_array($type, ['multiple_choice', 'true_false'])) {
            $choices = "\nCHOICES:\n";
            foreach ($question->choices as $choice) {
                $mark = $choice->is_correct ? '[CORRECT]' : '';
                $choices .= "- {$choice->choice_text} {$mark}\n";
            }
        }

        return <<<PROMPT
You are an expert in educational assessment. Analyze the following question for quality, clarity, and pedagogical value.

QUESTION TYPE: {$type}
QUESTION TEXT: {$questionText}
{$choices}

ANALYZE THE FOLLOWING:
1. Grammar and Language Quality (0-100)
2. Clarity and Precision (0-100)
3. Question Quality (0-100)
4. Difficulty Appropriateness
5. Bloom's Taxonomy Level
6. Potential Issues
7. Improvement Suggestions

For Multiple Choice Questions, also check:
- Are distractors plausible?
- Is there only ONE clearly correct answer?
- Are options similar in length and structure?

FORMAT YOUR RESPONSE AS JSON:
{
  "quality_score": 85,
  "clarity_score": 90,
  "grammar_score": 95,
  "bloom_level": "apply",
  "difficulty_assessment": "appropriate|too_easy|too_hard",
  "issues": [
    "List of issues found"
  ],
  "suggestions": [
    "Specific improvement suggestions"
  ],
  "distractor_quality": "good|fair|poor",
  "overall_assessment": "Brief overall assessment"
}

Generate ONLY valid JSON. No additional text.
PROMPT;
    }

    /**
     * Build prompt for quiz analysis
     */
    protected function buildAnalysisPrompt(Quiz $quiz, $statistics)
    {
        $stats = json_encode($statistics, JSON_PRETTY_PRINT);

        return <<<PROMPT
You are an expert in educational data analysis. Analyze the following quiz performance data and provide insights.

QUIZ: {$quiz->title}
TOTAL QUESTIONS: {$quiz->questions()->count()}
PASSING SCORE: {$quiz->passing_score}%
TIME LIMIT: {$quiz->time_limit} minutes

PERFORMANCE STATISTICS:
{$stats}

ANALYZE THE FOLLOWING:
1. Overall quiz difficulty assessment
2. Question performance analysis
3. Student performance patterns
4. Identify problematic questions
5. Recommendations for improvement
6. Insights on learning outcomes

FORMAT YOUR RESPONSE AS JSON:
{
  "overall_difficulty": "easy|medium|hard|mixed",
  "difficulty_assessment": "Detailed assessment",
  "question_analysis": [
    {
      "question_id": 1,
      "issue": "Description of issue",
      "recommendation": "How to fix"
    }
  ],
  "performance_insights": [
    "List of insights about student performance"
  ],
  "recommendations": [
    "Specific actionable recommendations"
  ],
  "learning_outcomes": "Assessment of whether learning outcomes are met",
  "suggested_adjustments": {
    "time_limit": "Increase/Decrease/Keep",
    "passing_score": "Increase/Decrease/Keep",
    "question_difficulty": "Adjust specific questions"
  }
}

Generate ONLY valid JSON. No additional text.
PROMPT;
    }

    /**
     * Call OpenAI API
     */
    protected function callOpenAI($prompt)
    {
        // Validate API key before making request
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenAI API key is not configured.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert educational content creator and analyst. You always respond with valid JSON only, no additional text.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => $this->maxTokens,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                $errorData = $response->json();
                
                $errorMessage = $errorData['error']['message'] ?? $errorBody ?? 'Unknown API error';
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'error' => $errorMessage,
                    'response' => $errorBody
                ]);
                
                throw new \Exception('OpenAI API Error: ' . $errorMessage);
            }

            $data = $response->json();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                Log::error('OpenAI Invalid Response Format', ['response' => $data]);
                throw new \Exception('Invalid API response format: missing content');
            }

            return $data['choices'][0]['message']['content'];
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenAI Connection Error', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to connect to OpenAI API. Please check your internet connection.');
        } catch (\Exception $e) {
            Log::error('OpenAI Request Failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Parse generated questions from AI response
     */
    protected function parseGeneratedQuestions($response)
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI response: ' . json_last_error_msg());
        }

        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new \Exception('Invalid response format: missing questions array');
        }

        return $data['questions'];
    }

    /**
     * Parse validation response
     */
    protected function parseValidationResponse($response)
    {
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse validation response: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Parse analysis response
     */
    protected function parseAnalysisResponse($response)
    {
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse analysis response: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Save generated questions to database
     */
    protected function saveGeneratedQuestions($questions, $subjectId, $instructorId = null)
    {
        $saved = [];

        // Get instructor ID - use provided one or get from auth
        if (!$instructorId) {
            $instructorId = auth()->check() ? auth()->user()->instructor->id : null;
        }

        if (!$instructorId) {
            throw new \Exception('Instructor ID is required to save questions');
        }

        foreach ($questions as $questionData) {
            $question = QuestionBank::create([
                'instructor_id' => $instructorId,
                'subject_id' => $subjectId,
                'question_text' => $questionData['question_text'],
                'type' => $questionData['type'],
                'points' => $questionData['points'] ?? 1,
                'difficulty' => $questionData['difficulty'] ?? 'medium',
                'bloom_level' => $questionData['bloom_level'] ?? 'understand',
                'explanation' => $questionData['explanation'] ?? null,
                'is_validated' => true,
                'quality_score' => 85, // Default score for AI-generated
            ]);

            // Handle different question types
            if ($questionData['type'] === 'multiple_choice' && isset($questionData['choices'])) {
                foreach ($questionData['choices'] as $index => $choice) {
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'] ?? false,
                        'order' => $index + 1,
                    ]);
                }
            } elseif ($questionData['type'] === 'true_false') {
                Choice::create([
                    'question_id' => $question->id,
                    'choice_text' => 'True',
                    'is_correct' => $questionData['correct_answer'] === 'true' || $questionData['correct_answer'] === 'True',
                    'order' => 1,
                ]);
                Choice::create([
                    'question_id' => $question->id,
                    'choice_text' => 'False',
                    'is_correct' => $questionData['correct_answer'] === 'false' || $questionData['correct_answer'] === 'False',
                    'order' => 2,
                ]);
            }

            $saved[] = $question;
        }

        return $saved;
    }

    /**
     * Calculate quiz statistics
     */
    protected function calculateQuizStatistics(Quiz $quiz, $attempts)
    {
        $totalAttempts = $attempts->count();
        $totalQuestions = $quiz->questions()->count();
        
        $scores = $attempts->pluck('score')->toArray();
        $averageScore = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
        
        $passedCount = $attempts->filter(function($attempt) use ($quiz) {
            return $attempt->score >= $quiz->passing_score;
        })->count();

        // Question-level statistics
        $questionStats = [];
        foreach ($quiz->questions as $question) {
            $correctCount = 0;
            $totalAnswers = 0;

            foreach ($attempts as $attempt) {
                $answer = $attempt->answers()
                    ->where('question_id', $question->id)
                    ->first();
                
                if ($answer) {
                    $totalAnswers++;
                    if ($answer->is_correct) {
                        $correctCount++;
                    }
                }
            }

            $difficultyIndex = $totalAnswers > 0 ? ($correctCount / $totalAnswers) * 100 : 0;

            $questionStats[] = [
                'question_id' => $question->id,
                'question_text' => substr($question->question_text, 0, 100),
                'correct_count' => $correctCount,
                'total_answers' => $totalAnswers,
                'difficulty_index' => round($difficultyIndex, 2),
                'performance' => $difficultyIndex > 70 ? 'easy' : ($difficultyIndex < 40 ? 'hard' : 'moderate')
            ];
        }

        return [
            'total_attempts' => $totalAttempts,
            'average_score' => round($averageScore, 2),
            'pass_rate' => $totalAttempts > 0 ? round(($passedCount / $totalAttempts) * 100, 2) : 0,
            'highest_score' => count($scores) > 0 ? max($scores) : 0,
            'lowest_score' => count($scores) > 0 ? min($scores) : 0,
            'question_statistics' => $questionStats,
        ];
    }
}