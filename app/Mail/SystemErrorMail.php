<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class SystemErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $exception;

    /**
     * Create a new message instance.
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('System Error Occurred')
                    ->view('emails.system_error')
                    ->with([
                        'exceptionClass' => get_class($this->exception),
                        'message' => $this->exception->getMessage(),
                        'file' => $this->exception->getFile(),
                        'line' => $this->exception->getLine(),
                    ]);
    }
}
