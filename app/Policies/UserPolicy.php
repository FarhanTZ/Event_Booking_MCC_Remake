<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the target user profile.
     */
    public function view(User $currentUser, User $targetUser): bool
    {
        return $currentUser->id === $targetUser->id || $currentUser->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the target user profile.
     */
    public function update(User $currentUser, User $targetUser): bool
    {
        return $currentUser->id === $targetUser->id || $currentUser->isSuperAdmin();
    }
}
