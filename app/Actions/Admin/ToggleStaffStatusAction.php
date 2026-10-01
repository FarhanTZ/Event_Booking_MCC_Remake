<?php

namespace App\Actions\Admin;

use App\Models\User;

class ToggleStaffStatusAction
{
    /**
     * Toggle staff suspended / active status (FR-ADM-03).
     *
     * @param User $staff
     * @param string|null $reason
     * @return User
     */
    public function execute(User $staff, ?string $reason = null): User
    {
        $newStatus = ! $staff->is_suspended;

        $staff->update([
            'is_suspended' => $newStatus,
            'suspended_at' => $newStatus ? now() : null,
            'suspension_reason' => $newStatus ? ($reason ?? 'Dinonaktifkan oleh Super Admin (Mutasi / Selesai Bertugas).') : null,
        ]);

        return $staff->fresh();
    }
}
