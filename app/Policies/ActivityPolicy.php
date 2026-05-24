<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Activity $activity): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'supervisor', 'support_staff']);
    }

    public function update(User $user, Activity $activity): bool
    {
        // Admins and supervisors can update any activity
        if ($user->hasAnyRole(['admin', 'supervisor'])) {
            return true;
        }

        // Support staff can update activities assigned to them or created by them
        return $activity->assigned_to === $user->id || $activity->created_by === $user->id;
    }

    public function updateStatus(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->hasRole('admin');
    }

    public function restore(User $user, Activity $activity): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, Activity $activity): bool
    {
        return $user->hasRole('admin');
    }
}
