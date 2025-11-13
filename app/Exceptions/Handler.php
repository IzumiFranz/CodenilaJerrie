<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemErrorMail;

class Handler extends ExceptionHandler
{
    // ... existing code like $dontReport, $dontFlash

    public function report(Throwable $exception)
    {
        parent::report($exception);

        // Notify admins of critical errors
        if ($this->shouldReport($exception)) {
            $admins = User::where('role', 'admin')->where('status', 'active')->get();

            foreach ($admins as $admin) {
                $settings = $admin->settings ?? (object)[
                    'email_system_error' => true,
                    'notification_system_error' => true,
                ];

                if ($settings->email_system_error) {
                    Mail::to($admin->email)->queue(new SystemErrorMail($exception));
                }

                if ($settings->notification_system_error) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'danger',
                        'title' => 'System Error',
                        'message' => get_class($exception) . ': ' . $exception->getMessage(),
                        'action_url' => route('admin.audit-logs.index'),
                    ]);
                }
            }
        }
    }
}
