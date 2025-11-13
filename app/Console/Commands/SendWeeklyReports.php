<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\WeeklyReportMail;
use Illuminate\Support\Facades\Mail;

class SendWeeklyReports extends Command
{
    protected $signature = 'reports:weekly';
    protected $description = 'Send weekly reports to admins';

    public function handle()
    {
        $admins = User::where('role', 'admin')->where('status', 'active')->get();
        
        foreach ($admins as $admin) {
            $settings = $admin->settings;
            
            if ($settings && $settings->email_weekly_report) {
                Mail::to($admin->email)->queue(new WeeklyReportMail());
            }
        }
        
        $this->info('Weekly reports sent!');
    }
}