<?php

namespace App\Jobs;

use App\Mail\QuizPublishedMail;
use App\Models\{Quiz, Notification};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendQuizPublishedNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $quiz;
    public $students;

    /**
     * @param \App\Models\Quiz $quiz
     * @param \Illuminate\Support\Collection|\App\Models\Student[] $students
     */
    public function __construct(Quiz $quiz, $students)
    {
        $this->quiz = $quiz;
        $this->students = collect($students); // ensure it's a collection
    }

    public function handle(): void
    {
        foreach ($this->students as $student) {
            try {
                $user = $student->user;
                if (!$user) continue;

                // Respect user notification settings
                $settings = $user->settings ?? (object)[
                    'email_quiz_published' => true,
                    'notification_quiz_published' => true,
                ];

                // Send email if enabled
                if ($settings->email_quiz_published) {
                    Mail::to($user->email)->queue(new QuizPublishedMail($this->quiz));
                }

                // Create in-app notification if enabled
                if ($settings->notification_quiz_published) {
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'info',
                        'title' => 'New Quiz Available',
                        'message' => "A new quiz '{$this->quiz->title}' has been published in {$this->quiz->subject->subject_name}.",
                        'action_url' => route('student.quizzes.show', $this->quiz),
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Quiz notification failed', [
                    'quiz_id' => $this->quiz->id,
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
