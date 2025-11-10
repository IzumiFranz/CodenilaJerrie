<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Run the content:process-scheduled command every 5 minutes
        $schedule->command('content:process-scheduled')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();

        $schedule->command('alerts:generate-performance')
        ->dailyAt('08:00')
        ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
