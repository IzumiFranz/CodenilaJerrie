<?php

namespace App\Policies;

use App\Models\AIJob;
use App\Models\User;

class AIJobPolicy
{
    public function view(User $user, AIJob $job): bool
    {
        return $user->id === $job->user_id || $user->isAdmin();
    }

    public function delete(User $user, AIJob $job): bool
    {
        return $user->id === $job->user_id || $user->isAdmin();
    }
}