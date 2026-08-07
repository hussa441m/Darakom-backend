<?php

namespace App\Policies;

use App\Models\Step;
use App\Models\User;

class StepPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->type === 'admin';
    }

    public function view(User $user, Step $step): bool
    {
        return $user->type === 'admin';
    }

    public function delete(User $user, Step $step): bool
    {
        return $user->type === 'admin';
    }
}
