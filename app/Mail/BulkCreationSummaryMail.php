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
        return $this->subject('Bulk User Creation Summary - ' . count($this->users) . ' Users Created')
            ->markdown('emails.bulk-creation-summary')
            ->attach($this->filePath, [
                'as' => 'user_credentials_summary.csv',
                'mime' => 'text/csv',
            ]);
    }
}