<?php

namespace App\Actions\Admin;

use App\Models\User;

class LiftUserSanctionAction
{
    /**
     * Lift suspension and restore applicant account access (FR-ADM-04).
     *
     * @param User $user
     * @param string|null $settlementNotes
     * @return User
     */
    public function execute(User $user, ?string $settlementNotes = null): User
    {
        $user->update([
            'is_suspended' => false,
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);

        return $user->fresh();
    }
}
