<?php

namespace App\Mail;

use App\Models\Lesson;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LessonPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lesson;

    public function __construct(Lesson $lesson)
    {
        $this->lesson = $lesson;
    }

    public function build()
    {
        return $this->subject('New Lesson Available: ' . $this->lesson->title)
                    ->markdown('emails.lesson-published');
    }
}
