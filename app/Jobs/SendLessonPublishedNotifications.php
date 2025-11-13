<?php

namespace App\Jobs;

use App\Mail\LessonPublishedMail;
use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLessonPublishedNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $lesson;
    public $students;

    public function __construct(Lesson $lesson, $students)
    {
        $this->lesson = $lesson;
        $this->students = $students;
    }

    public function handle()
    {
        foreach ($this->students as $student) {
            try {
                Mail::to($student->user->email)
                    ->send(new LessonPublishedMail($this->lesson));
                    
                \App\Models\Notification::create([
                    'user_id' => $student->user_id,
                    'type' => 'info',
                    'title' => 'New Lesson Available',
                    'message' => "A new lesson '{$this->lesson->title}' has been published in {$this->lesson->subject->subject_name}.",
                    'action_url' => route('student.lessons.show', $this->lesson),
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Lesson notification failed', [
                    'lesson_id' => $this->lesson->id,
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}