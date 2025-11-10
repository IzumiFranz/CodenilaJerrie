<?php

namespace App\Console\Commands;

use App\Models\Instructor;
use App\Services\PerformanceAlertService;
use Illuminate\Console\Command;

class GeneratePerformanceAlerts extends Command
{
    protected $signature = 'alerts:generate-performance';
    protected $description = 'Generate performance alerts for instructors';

    public function handle(PerformanceAlertService $alertService)
    {
        $this->info('Generating performance alerts...');
        
        $instructors = Instructor::whereHas('user', function($q) {
            $q->where('status', 'active');
        })->get();
        
        $totalAlerts = 0;
        
        foreach ($instructors as $instructor) {
            $alerts = $alertService->checkAndGenerateAlerts($instructor);
            $count = collect($alerts)->sum(function($alert) {
                return is_countable($alert) ? count($alert) : $alert->count();
            });
            
            if ($count > 0) {
                $this->line("Generated {$count} alerts for {$instructor->full_name}");
                $totalAlerts += $count;
            }
        }
        
        $this->info("Total alerts generated: {$totalAlerts}");
        
        return 0;
    }
}