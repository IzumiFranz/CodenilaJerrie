<?php

namespace App\Mail;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuizPublishedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function build()
    {
        return $this->subject('New Quiz Available: ' . $this->quiz->title)
                    ->markdown('emails.quiz-published');
    }
}