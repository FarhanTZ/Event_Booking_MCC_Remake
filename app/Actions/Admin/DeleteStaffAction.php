<?php

namespace App\Actions\Admin;

use App\Models\User;

class DeleteStaffAction
{
    /**
     * Delete an internal staff user (FR-ADM-03).
     *
     * @param User $staff
     * @return bool
     */
    public function execute(User $staff): bool
    {
        return (bool) $staff->delete();
    }
}
