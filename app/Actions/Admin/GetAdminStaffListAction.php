<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\StaffFilterData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class GetAdminStaffListAction
{
    /**
     * Get filtered staff accounts and division distribution metrics (FR-ADM-03).
     *
     * @param StaffFilterData $dto
     * @return array{
     *   staffs: \Illuminate\Contracts\Pagination\LengthAwarePaginator,
     *   metrics: array{total: int, active: int, inactive: int, marketing: int, front_office: int, duty_officer: int, super_admin: int},
     *   filter: StaffFilterData
     * }
     */
    public function execute(StaffFilterData $dto): array
    {
        // Only internal roles (excluding regular public user)
        $staffRoles = [UserRole::MARKETING, UserRole::FRONT_OFFICE, UserRole::DUTY_OFFICER, UserRole::SUPER_ADMIN];

        $baseQuery = User::query()->whereIn('role', $staffRoles);

        // Overall Staff Metrics
        $metrics = [
            'total' => User::whereIn('role', $staffRoles)->count(),
            'active' => User::whereIn('role', $staffRoles)->where('is_suspended', false)->count(),
            'inactive' => User::whereIn('role', $staffRoles)->where('is_suspended', true)->count(),
            'marketing' => User::where('role', UserRole::MARKETING)->count(),
            'front_office' => User::where('role', UserRole::FRONT_OFFICE)->count(),
            'duty_officer' => User::where('role', UserRole::DUTY_OFFICER)->count(),
            'super_admin' => User::where('role', UserRole::SUPER_ADMIN)->count(),
        ];

        // 1. Search Filter (Name, Email, Phone, Institution)
        if (! empty($dto->search)) {
            $search = '%' . strtolower($dto->search) . '%';
            $baseQuery->where(function (Builder $query) use ($search) {
                $query->whereRaw('LOWER(full_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(phone_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(institution_name) LIKE ?', [$search]);
            });
        }

        // 2. Role Filter
        if (! empty($dto->role)) {
            $baseQuery->where('role', $dto->role);
        }

        // 3. Status Filter (active / inactive)
        if ($dto->status === 'active') {
            $baseQuery->where('is_suspended', false);
        } elseif ($dto->status === 'inactive') {
            $baseQuery->where('is_suspended', true);
        }

        // 4. Sorting
        $allowedSorts = ['full_name', 'email', 'role', 'created_at', 'is_suspended'];
        $sortField = in_array($dto->sortField, $allowedSorts) ? $dto->sortField : 'created_at';
        $sortDirection = strtolower($dto->sortDirection) === 'asc' ? 'asc' : 'desc';

        $staffs = $baseQuery->orderBy($sortField, $sortDirection)
            ->paginate(15)
            ->withQueryString();

        return [
            'staffs' => $staffs,
            'metrics' => $metrics,
            'filter' => $dto,
        ];
    }
}
