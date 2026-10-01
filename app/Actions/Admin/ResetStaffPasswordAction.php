<?php

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetStaffPasswordAction
{
    /**
     * Reset internal staff password (FR-ADM-03).
     *
     * @param User $staff
     * @param string $newPassword
     * @return User
     */
    public function execute(User $staff, string $newPassword): User
    {
        $staff->update([
            'password_hash' => Hash::make($newPassword),
        ]);

        return $staff->fresh();
    }
}
