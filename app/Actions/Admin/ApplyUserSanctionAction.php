<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\ApplySanctionData;
use App\Models\User;

class ApplyUserSanctionAction
{
    /**
     * Suspend / Blacklist applicant account (FR-ADM-04).
     *
     * @param User $user
     * @param ApplySanctionData $dto
     * @return User
     */
    public function execute(User $user, ApplySanctionData $dto): User
    {
        $user->update([
            'is_suspended' => true,
            'suspension_reason' => $dto->suspensionReason,
            'suspended_at' => now(),
        ]);

        return $user->fresh();
    }
}
