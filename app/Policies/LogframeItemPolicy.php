<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LogframeItem;
use App\Models\User;

class LogframeItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project_officer', 'it_admin']);
    }

    public function view(User $user, LogframeItem $logframeItem): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('it_admin')) {
            return true;
        }

        return $user->hasRole('project_officer')
            && $logframeItem->project?->assigned_officer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'project_officer']);
    }

    public function update(User $user, LogframeItem $logframeItem): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasRole('project_officer')
            && $logframeItem->project?->assigned_officer_id === $user->id;
    }

    public function delete(User $user, LogframeItem $logframeItem): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, LogframeItem $logframeItem): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, LogframeItem $logframeItem): bool
    {
        return $user->hasRole('admin');
    }
}
