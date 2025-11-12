<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class BulkUsersCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $users;
    public $role;
    public $totalCreated;
    public $csvPath;
    public $filePath;
    /**
     * Create a new message instance.
     *
     * @param array $users Array of created users with credentials
     */
    public function __construct(array $users, string $role)
    {
        $this->users = $users;
        $this->role = $role;
        $this->totalCreated = count($users);
        $this->csvPath = $this->generateCsvFile($users);
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Bulk User Creation Complete - Credentials Attached' . $this->totalCreated . ' ' . ucfirst($this->role) . '(s)')
                    ->markdown('emails.bulk-users-created')
                    ->attach($this->csvPath, [
                        'as' => 'user_credentials_' . date('Y-m-d_His') . '.csv',
                        'mime' => 'text/csv',
                    ]);
    }

    /**
     * Generate CSV file with user credentials
     *
     * @param array $users
     * @return string Path to generated CSV file
     */
    private function generateCsvFile(array $users): string
    {
        $filename = 'bulk_users_' . time() . '.csv';
        $path = storage_path('app/temp/' . $filename);

        // Create temp directory if it doesn't exist
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $handle = fopen($path, 'w');

        // Add UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // CSV Headers
        fputcsv($handle, [
            'Name',
            'Username',
            'Email',
            'Password',
            'Role',
            'Login URL',
            'Status'
        ]);

        // Add user data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user['name'] ?? '',
                $user['username'] ?? '',
                $user['email'] ?? '',
                $user['password'] ?? '',
                ucfirst($this->role),
                url('/login'),
                'Active'
            ]);
        }

        fclose($handle);

        return $path;
    }

    /**
     * Clean up temporary CSV file after sending
     */
    public function __destruct()
    {
        if ($this->csvPath && file_exists($this->csvPath)) {
            @unlink($this->csvPath);
        }
    }
}