<?php

namespace App\Providers;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuestionBank;
use App\Policies\LessonPolicy;
use App\Policies\QuizPolicy;
use App\Policies\QuestionBankPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Lesson::class => LessonPolicy::class,
        Quiz::class => QuizPolicy::class,
        QuestionBank::class => QuestionBankPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define custom gates for system-wide permissions
        
        // Admin-only gates
        Gate::define('manage-users', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-courses', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-subjects', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-sections', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-enrollments', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-assignments', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('view-audit-logs', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-feedback', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('send-notifications', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('manage-specializations', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('view-system-settings', function ($user) {
            return $user->isAdmin();
        });

        // Instructor gates
        Gate::define('create-lessons', function ($user) {
            return $user->isInstructor();
        });

        Gate::define('create-quizzes', function ($user) {
            return $user->isInstructor();
        });

        Gate::define('create-questions', function ($user) {
            return $user->isInstructor();
        });

        Gate::define('view-student-progress', function ($user) {
            return $user->isInstructor();
        });

        Gate::define('grade-submissions', function ($user) {
            return $user->isInstructor();
        });

        Gate::define('use-ai-features', function ($user) {
            return $user->isInstructor();
        });

        // Student gates
        Gate::define('take-quizzes', function ($user) {
            return $user->isStudent();
        });

        Gate::define('view-lessons', function ($user) {
            return $user->isStudent();
        });

        Gate::define('submit-feedback', function ($user) {
            return true; // All authenticated users can submit feedback
        });

        // Super admin gate (for critical operations)
        Gate::define('super-admin', function ($user) {
            // You can check if user is a specific admin (like ID = 1)
            // or check a special flag in admin table
            return $user->isAdmin() && $user->id === 1;
        });

        // Gate to check if user can impersonate other users (for testing/support)
        Gate::define('impersonate-users', function ($user) {
            return $user->isAdmin() && config('app.allow_impersonation', false);
        });
    }
}