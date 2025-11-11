<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $createdBy;

    /**
     * Create a new message instance.
     *
     * @param User $user The newly created user
     * @param string $password The temporary password
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->createdBy = auth()->user();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Account Created - ' . config('app.name'))
                    ->markdown('emails.user-created');
    }
}