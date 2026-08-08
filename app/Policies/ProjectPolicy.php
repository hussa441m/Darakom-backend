<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function update(User $user, Project $project): bool
    {
        return $this->isAdmin($user)
            || $this->isProjectOwner($user, $project)
            || $this->isAssignedProvider($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->isAdmin($user)
            || $this->isProjectOwner($user, $project);
    }

    public function create(User $user): bool
    {
        return $user->type === 'client';
    }
     
    public function view(User $user, Project $project): bool
    {
        return $this->isAdmin($user)
            || $this->isProjectOwner($user, $project)
            || (
                $user->type === 'provider'
                && $user->profile
                && $project->performed_by == $user->profile->id
            );
    }

    private function isAdmin(User $user): bool
    {
        return $user->type === 'admin';
    }

    private function isProjectOwner(User $user, Project $project): bool
    {
        return $user->type === 'client'
            && $project->client_id == $user->id;
    }

    private function isAssignedProvider(User $user, Project $project): bool
    {
        if ($user->type !== 'provider') {
            return false;
        }

        return $project->performed_by == $user->id
            || ($user->profile && $project->performed_by == $user->profile->id);
    }
}