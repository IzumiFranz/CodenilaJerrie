<?php

namespace App\Jobs;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $password;
    public $tries = 3;
    public $timeout = 60;
    public $backoff = [30, 60, 120];

    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function handle()
    {
        try {
            Mail::to($this->user->email)
                ->send(new WelcomeMail($this->user, $this->password));
            
            Log::info('Welcome email sent successfully', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'username' => $this->user->username
            ]);
            
        } catch (\Exception $e) {
            Log::error('Welcome email failed', [
                'user_id' => $this->user->id,
                'email' => $this->user->email,
                'error' => $e->getMessage()
            ]);
            throw $e; // Will retry up to 3 times
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('Welcome email failed permanently', [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'exception' => $exception->getMessage()
        ]);
        
        // Notify admin about failed email
        \App\Models\Notification::create([
            'user_id' => 1, // System admin
            'type' => 'danger',
            'title' => 'Failed to Send Welcome Email',
            'message' => "Could not send welcome email to {$this->user->email} ({$this->user->username}). Please contact the user manually.",
        ]);
    }
}
