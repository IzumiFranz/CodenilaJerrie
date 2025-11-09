<?php

namespace App\Mail;

use App\Models\QuizAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuizResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public $attempt;

    public function __construct(QuizAttempt $attempt)
    {
        $this->attempt = $attempt;
    }

    public function build()
    {
        $subject = $this->attempt->isPassed() 
            ? 'Congratulations! You passed: ' . $this->attempt->quiz->title
            : 'Quiz Result: ' . $this->attempt->quiz->title;

        return $this->subject($subject)
                    ->markdown('emails.quiz-result');
    }
}