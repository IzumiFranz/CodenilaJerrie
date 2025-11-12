<?php 

namespace App\Jobs;

use App\Mail\BulkUsersCreatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendBulkUserEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $adminEmail;
    public $tries = 3;

    public function __construct(array $users, string $adminEmail)
    {
        $this->users = $users;
        $this->adminEmail = $adminEmail;
    }

    public function handle()
    {
        // Generate CSV with credentials
        $csvContent = $this->generateCSV();
        
        // Save temporarily
        $filename = 'user_credentials_' . now()->format('YmdHis') . '.csv';
        Storage::disk('local')->put('temp/' . $filename, $csvContent);
        $filePath = storage_path('app/temp/' . $filename);

        try {
            // Send email with attachment
            Mail::to($this->adminEmail)
                ->send(new BulkUsersCreatedMail($this->users, $filePath));

            // Clean up temp file
            Storage::disk('local')->delete('temp/' . $filename);

        } catch (\Exception $e) {
            \Log::error('Bulk user emails failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function generateCSV(): string
    {
        $handle = fopen('php://temp', 'w');
        
        // Headers
        fputcsv($handle, [
            'Name',
            'Username',
            'Email',
            'Password',
            'Role'
        ]);

        // Data
        foreach ($this->users as $user) {
            fputcsv($handle, [
                $user['name'],
                $user['username'],
                $user['email'],
                $user['password'],
                ucfirst($user['role'] ?? 'student')
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}