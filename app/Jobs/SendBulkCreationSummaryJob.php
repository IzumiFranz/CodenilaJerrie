<?php

namespace App\Jobs;

use App\Mail\BulkCreationSummaryMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SendBulkCreationSummaryJob implements ShouldQueue
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
        $csvContent = $this->generateCSV();
        $filename = 'bulk_creation_summary_' . now()->format('YmdHis') . '.csv';
        Storage::disk('local')->put('temp/' . $filename, $csvContent);
        $filePath = storage_path('app/temp/' . $filename);

        try {
            Mail::to($this->adminEmail)
                ->send(new BulkCreationSummaryMail($this->users, $filePath));

            Storage::disk('local')->delete('temp/' . $filename);
            
            Log::info('Bulk creation summary sent', [
                'admin_email' => $this->adminEmail,
                'user_count' => count($this->users)
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk summary email failed', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function generateCSV(): string
    {
        $handle = fopen('php://temp', 'w');
        
        fputcsv($handle, [
            'Name',
            'Username',
            'Email',
            'Temporary Password',
            'Role',
            'Status'
        ]);

        foreach ($this->users as $user) {
            fputcsv($handle, [
                $user['name'],
                $user['username'],
                $user['email'],
                $user['password'],
                ucfirst($user['role'] ?? 'student'),
                'Email Sent'
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
