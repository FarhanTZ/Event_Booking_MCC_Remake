<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\InterventionFilterData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class GetInterventionBookingListAction
{
    /**
     * Get list of active / upcoming bookings for emergency intervention (FR-ADM-05).
     *
     * @param InterventionFilterData $dto
     * @return array{bookings: LengthAwarePaginator, metrics: array, filter: InterventionFilterData}
     */
    public function execute(InterventionFilterData $dto): array
    {
        $todayStr = Carbon::today()->toDateString();

        // Calculate Real-time Metrics
        $totalActive = Booking::whereIn('status', [
            BookingStatus::CONFIRMED,
            BookingStatus::CHECKED_IN,
            BookingStatus::AWAITING_PAYMENT,
            BookingStatus::AWAITING_MARKETING,
        ])->count();

        $todayEvents = Booking::whereIn('status', [
            BookingStatus::CONFIRMED,
            BookingStatus::CHECKED_IN,
        ])->whereHas('slots', function ($q) use ($todayStr) {
            $q->whereDate('event_start_at', $todayStr);
        })->count();

        $emergencyCancelled = BookingApproval::where('decision', 'emergency_cancelled')->count();
        $totalBookings = Booking::count();

        $metrics = [
            'total_active' => $totalActive,
            'today_events' => $todayEvents,
            'emergency_cancelled' => $emergencyCancelled,
            'total_bookings' => $totalBookings,
        ];

        // Query Bookings
        $query = Booking::with([
            'user',
            'eventDetail',
            'slots.room',
            'approvals.actionBy',
            'incidents',
            'latestKtpVaultLog',
        ]);

        // Filter by Status Tab
        if ($dto->status === 'active') {
            $query->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::CHECKED_IN]);
        } elseif ($dto->status === 'confirmed') {
            $query->where('status', BookingStatus::CONFIRMED);
        } elseif ($dto->status === 'checked_in') {
            $query->where('status', BookingStatus::CHECKED_IN);
        } elseif ($dto->status === 'pending') {
            $query->whereIn('status', [BookingStatus::AWAITING_MARKETING, BookingStatus::AWAITING_PAYMENT, BookingStatus::REVISED]);
        } elseif ($dto->status === 'cancelled') {
            $query->where('status', BookingStatus::CANCELLED);
        }

        // Filter by Floor Level
        if ($dto->floor) {
            $query->whereHas('slots.room', function ($q) use ($dto) {
                $q->where('floor_level', $dto->floor);
            });
        }

        // Filter by Date
        if ($dto->date) {
            $query->whereHas('slots', function ($q) use ($dto) {
                $q->whereDate('event_start_at', $dto->date);
            });
        }

        // Search Filter (booking_code, event_name, user name, institution, or pic phone)
        if ($dto->search) {
            $search = '%' . $dto->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('full_name', 'like', $search)
                            ->orWhere('email', 'like', $search)
                            ->orWhere('phone_number', 'like', $search)
                            ->orWhere('institution_name', 'like', $search);
                    })
                    ->orWhereHas('eventDetail', function ($eq) use ($search) {
                        $eq->where('event_name', 'like', $search)
                            ->orWhere('pic_name_snapshot', 'like', $search)
                            ->orWhere('pic_phone_snapshot', 'like', $search);
                    });
            });
        }

        // Order by latest created or slot date
        $query->orderBy('created_at', 'desc');

        $bookings = $query->paginate($dto->perPage)->withQueryString();

        return [
            'bookings' => $bookings,
            'metrics' => $metrics,
            'filter' => $dto,
        ];
    }
}
