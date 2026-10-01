<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\SanctionFilterData;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\RoomIncident;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSanctionReviewQueueAction
{
    /**
     * Get review queue of applicants for sanctions / blacklist enforcement (FR-ADM-04).
     *
     * @param SanctionFilterData $dto
     * @return array{applicants: LengthAwarePaginator, metrics: array, filter: SanctionFilterData}
     */
    public function execute(SanctionFilterData $dto): array
    {
        // Calculate Metrics
        $totalSuspended = User::where('role', UserRole::USER)->where('is_suspended', true)->count();
        $pendingAbandoned = Booking::where('status', BookingStatus::ABANDONED_INCIDENT)->count();
        $activeIncidents = RoomIncident::where('is_resolved', false)->count();
        $totalApplicants = User::where('role', UserRole::USER)->count();

        $metrics = [
            'total_suspended' => $totalSuspended,
            'pending_abandoned' => $pendingAbandoned,
            'active_incidents' => $activeIncidents,
            'total_applicants' => $totalApplicants,
        ];

        // Base Query: Public users with their bookings, incidents, and violation history
        $query = User::where('role', UserRole::USER)
            ->with([
                'bookings' => function ($q) {
                    $q->with(['slots.room', 'incidents.photos', 'latestKtpVaultLog'])->latest();
                },
            ]);

        // Filter by Status Tab
        if ($dto->status === 'suspended') {
            $query->where('is_suspended', true);
        } elseif ($dto->status === 'abandoned') {
            $query->whereHas('bookings', function ($q) {
                $q->where('status', BookingStatus::ABANDONED_INCIDENT);
            });
        } elseif ($dto->status === 'incident') {
            $query->whereHas('bookings.incidents', function ($q) {
                $q->where('is_resolved', false);
            });
        }

        // Search Filter (name, email, phone, institution, or suspension reason)
        if ($dto->search) {
            $search = '%' . $dto->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone_number', 'like', $search)
                    ->orWhere('institution_name', 'like', $search)
                    ->orWhere('suspension_reason', 'like', $search);
            });
        }

        // Sorting
        $allowedSorts = ['full_name', 'email', 'created_at', 'suspended_at', 'is_suspended'];
        $sortField = in_array($dto->sortField, $allowedSorts) ? $dto->sortField : 'created_at';
        $query->orderBy($sortField, $dto->sortDirection);

        $applicants = $query->paginate($dto->perPage)->withQueryString();

        return [
            'applicants' => $applicants,
            'metrics' => $metrics,
            'filter' => $dto,
        ];
    }
}
