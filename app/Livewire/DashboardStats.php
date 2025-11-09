<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Livewire\Component;

class DashboardStats extends Component
{
    public $stats = [];
    public $refreshInterval = 30000; // 30 seconds

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'totalCourses' => Course::count(),
            'activeCourses' => Course::where('is_active', true)->count(),
            'totalSubjects' => Subject::count(),
            'activeEnrollments' => Enrollment::where('status', 'enrolled')->count(),
            'totalQuizzes' => Quiz::count(),
            'completedAttempts' => QuizAttempt::where('status', 'completed')->count(),
        ];
    }

    public function refresh()
    {
        $this->loadStats();
        $this->dispatch('stats-refreshed');
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}