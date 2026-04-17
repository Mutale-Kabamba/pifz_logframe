<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project_officer', 'it_admin']);
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('it_admin')) {
            return true;
        }

        return $user->hasRole('project_officer') && $project->assigned_officer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('project_officer') && $project->assigned_officer_id === $user->id;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('admin');
    }
}
