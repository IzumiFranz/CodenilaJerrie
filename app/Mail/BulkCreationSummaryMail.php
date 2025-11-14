<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulkCreationSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $users;
    public $filePath;

    public function __construct(array $users, string $filePath)
    {
        $this->users = $users;
        $this->filePath = $filePath;
    }

    public function build()
    {
        // Pre-calculate stats to pass to view instead of querying in view
        $stats = [
            'queue_jobs' => \Illuminate\Support\Facades\DB::table('jobs')->count(),
            'active_users' => \App\Models\User::where('status', 'active')->count(),
            'total_students' => \App\Models\User::where('role', 'student')->count(),
            'total_instructors' => \App\Models\User::where('role', 'instructor')->count(),
        ];
        
        return $this->subject('Bulk User Creation Summary - ' . count($this->users) . ' Users Created')
            ->markdown('emails.bulk-creation-summary', ['stats' => $stats])
            ->attach($this->filePath, [
                'as' => 'user_credentials_summary.csv',
                'mime' => 'text/csv',
            ]);
    }
}