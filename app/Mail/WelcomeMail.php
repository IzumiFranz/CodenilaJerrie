<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $loginUrl;

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->loginUrl = route('login');
    }

    public function build()
    {
        return $this->subject('Welcome to ' . config('app.name') . ' - Your Account Details')
            ->markdown('emails.welcome')
            ->with([
                'user' => $this->user,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
                'roleLabel' => $this->getRoleLabel(),
            ]);
    }

    private function getRoleLabel(): string
    {
        return match($this->user->role) {
            'admin' => 'Administrator',
            'instructor' => 'Instructor',
            'student' => 'Student',
            default => ucfirst($this->user->role)
        };
    }
}