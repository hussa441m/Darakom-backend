<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    
    public function update(User $user, Project $project): bool
    {
        return $this->isAdmin($user)
            || $this->isProjectOwner($user, $project);
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


    private function isAdmin(User $user): bool
    {
        return $user->type === 'admin';
    }


    private function isProjectOwner(User $user, Project $project): bool
    {
        return $user->type === 'client'
            && $project->client_id == $user->id;
    }
}