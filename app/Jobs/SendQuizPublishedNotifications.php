<?php

namespace App\Jobs;

use App\Mail\QuizPublishedMail;
use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendQuizPublishedNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $quiz;
    public $students;

    public function __construct(Quiz $quiz, $students)
    {
        $this->quiz = $quiz;
        $this->students = $students;
    }

    public function handle()
    {
        foreach ($this->students as $student) {
            try {
                Mail::to($student->user->email)
                    ->send(new QuizPublishedMail($this->quiz));
                    
                // Create in-app notification
                \App\Models\Notification::create([
                    'user_id' => $student->user_id,
                    'type' => 'info',
                    'title' => 'New Quiz Available',
                    'message' => "A new quiz '{$this->quiz->title}' has been published in {$this->quiz->subject->subject_name}.",
                    'action_url' => route('student.quizzes.show', $this->quiz),
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Quiz notification failed', [
                    'quiz_id' => $this->quiz->id,
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}