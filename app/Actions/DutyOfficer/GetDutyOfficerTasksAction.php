<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\DutyOfficerDashboardFilterData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GetDutyOfficerTasksAction
{
    /**
     * Retrieve filtered duty officer field tasks for specified shift, floor, and scope.
     *
     * @param DutyOfficerDashboardFilterData $dto
     * @return array{
     *   tasks: Collection<int, Booking>,
     *   metrics: array{total: int, checked_in: int, upcoming: int, completed: int},
     *   floors: array<int>,
     *   filter: DutyOfficerDashboardFilterData,
     *   target_date: Carbon
     * }
     */
    public function execute(DutyOfficerDashboardFilterData $dto): array
    {
        $targetDate = $dto->date ? Carbon::parse($dto->date, 'Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $dateString = $targetDate->format('Y-m-d');

        // Base query for operational field tasks
        $baseQuery = Booking::query()
            ->with([
                'slots' => function ($query) {
                    $query->with('room.facilities')->orderBy('event_start_at', 'asc');
                },
                'eventDetail',
                'latestKtpVaultLog',
                'latestIncident.photos',
                'user',
                'dutyOfficer',
                'checkoutDutyOfficer',
                'roomOpenedBy',
            ])
            ->whereIn('status', [
                BookingStatus::CONFIRMED->value,
                BookingStatus::CHECKED_IN->value,
                BookingStatus::COMPLETED->value,
                BookingStatus::ABANDONED_CLEAN->value,
                BookingStatus::ABANDONED_INCIDENT->value,
            ])
            ->whereHas('slots', function (Builder $query) use ($dateString) {
                $query->whereDate('event_start_at', $dateString)
                    ->orWhereDate('event_end_at', $dateString);
            });

        // 1. Scope filter: Assigned to current DO vs All
        if ($dto->scope === 'assigned') {
            $baseQuery->where(function (Builder $query) use ($dto) {
                $query->where('duty_officer_id', $dto->userId)
                    ->orWhere('assigned_checkout_do_id', $dto->userId)
                    ->orWhere('checkout_duty_officer_id', $dto->userId)
                    ->orWhereHas('slots', function (Builder $sq) use ($dto) {
                        $sq->where('duty_officer_id', $dto->userId)
                            ->orWhere('assigned_checkout_do_id', $dto->userId)
                            ->orWhere('checkout_duty_officer_id', $dto->userId);
                    });
            });
        }

        // 2. Floor filter
        if ($dto->floor !== null) {
            $baseQuery->whereHas('slots.room', function (Builder $query) use ($dto) {
                $query->where('floor_level', $dto->floor);
            });
        }

        // 3. Shift filter
        $isSqlite = \Illuminate\Support\Facades\DB::getDriverName() === 'sqlite';
        if ($dto->shift === 'morning') {
            $baseQuery->whereHas('slots', function (Builder $query) use ($isSqlite) {
                if ($isSqlite) {
                    $query->whereRaw("strftime('%H:%M:%S', event_start_at) < '16:00:00'");
                } else {
                    $query->whereRaw("to_char(event_start_at, 'HH24:MI:SS') < '16:00:00'");
                }
            });
        } elseif ($dto->shift === 'evening') {
            $baseQuery->whereHas('slots', function (Builder $query) use ($isSqlite) {
                if ($isSqlite) {
                    $query->whereRaw("strftime('%H:%M:%S', event_start_at) >= '16:00:00'");
                } else {
                    $query->whereRaw("to_char(event_start_at, 'HH24:MI:SS') >= '16:00:00'");
                }
            });
        }

        // 4. Search Filter
        if (! empty($dto->search)) {
            $search = '%' . strtolower($dto->search) . '%';
            $baseQuery->where(function (Builder $query) use ($search) {
                $query->whereRaw('LOWER(booking_code) LIKE ?', [$search])
                    ->orWhereHas('eventDetail', function (Builder $sq) use ($search) {
                        $sq->whereRaw('LOWER(event_name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(institution_name_snapshot) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(pic_name_snapshot) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(equipment_needed) LIKE ?', [$search]);
                    })
                    ->orWhereHas('slots.room', function (Builder $sq) use ($search) {
                        $sq->whereRaw('LOWER(name) LIKE ?', [$search]);
                    })
                    ->orWhereHas('latestKtpVaultLog', function (Builder $sq) use ($search) {
                        $sq->whereRaw('LOWER(ktp_holder_name) LIKE ?', [$search])
                            ->orWhereRaw('LOWER(pic_whatsapp_number) LIKE ?', [$search]);
                    });
            });
        }

        // Clone base query for metrics calculation before status filter
        $metricsQuery = clone $baseQuery;
        $allTasksForMetrics = $metricsQuery->get();

        $metrics = [
            'total' => $allTasksForMetrics->count(),
            'needs_opening' => $allTasksForMetrics->filter(fn ($b) => $b->needsRoomOpening())->count(),
            'awaiting_checkout' => $allTasksForMetrics->filter(fn ($b) => $b->isAwaitingCheckoutInspection())->count(),
            'checked_in' => $allTasksForMetrics->where('status', BookingStatus::CHECKED_IN)->count(),
            'upcoming' => $allTasksForMetrics->where('status', BookingStatus::CONFIRMED)->count(),
            'completed' => $allTasksForMetrics->filter(fn ($b) => in_array($b->status, [
                BookingStatus::COMPLETED,
                BookingStatus::ABANDONED_CLEAN,
                BookingStatus::ABANDONED_INCIDENT,
            ]))->count(),
        ];

        // 5. Status tab filter
        if ($dto->status === 'needs_opening') {
            $baseQuery->where('status', BookingStatus::CHECKED_IN->value)
                ->whereNull('room_opened_at');
        } elseif ($dto->status === 'awaiting_checkout') {
            $baseQuery->where(function (Builder $query) {
                $query->whereIn('status', [BookingStatus::CHECKED_IN->value, BookingStatus::COMPLETED->value])
                    ->whereNotNull('checkout_initiated_at')
                    ->where(function ($sq) {
                        $sq->whereNull('clearance_status')->orWhere('clearance_status', 'pending');
                    });
            });
        } elseif ($dto->status === 'checked_in') {
            $baseQuery->where('status', BookingStatus::CHECKED_IN->value);
        } elseif ($dto->status === 'upcoming') {
            $baseQuery->where('status', BookingStatus::CONFIRMED->value);
        } elseif ($dto->status === 'completed') {
            $baseQuery->whereIn('status', [
                BookingStatus::COMPLETED->value,
                BookingStatus::ABANDONED_CLEAN->value,
                BookingStatus::ABANDONED_INCIDENT->value,
            ]);
        }

        // Execute query and order: Awaiting Checkout / Needs opening first, Checked-in second, then earliest start time
        $tasks = $baseQuery->get()->sort(function (Booking $a, Booking $b) {
            // Priority -1: Awaiting checkout inspection
            if ($a->isAwaitingCheckoutInspection() && ! $b->isAwaitingCheckoutInspection()) return -1;
            if (! $a->isAwaitingCheckoutInspection() && $b->isAwaitingCheckoutInspection()) return 1;

            // Priority 0: Needs room opening first
            if ($a->needsRoomOpening() && ! $b->needsRoomOpening()) return -1;
            if (! $a->needsRoomOpening() && $b->needsRoomOpening()) return 1;

            // Priority 1: CHECKED_IN status first
            if ($a->isCheckedIn() && ! $b->isCheckedIn()) return -1;
            if (! $a->isCheckedIn() && $b->isCheckedIn()) return 1;

            // Priority 2: Earliest start time
            $aStart = $a->slots->first()?->event_start_at;
            $bStart = $b->slots->first()?->event_start_at;

            if ($aStart && $bStart) {
                return $aStart <=> $bStart;
            }

            return 0;
        })->values();

        // Get list of active floors in MCC (Lantai 1 s/d 8)
        $floors = Room::where('is_active', true)
            ->distinct()
            ->orderBy('floor_level', 'asc')
            ->pluck('floor_level')
            ->toArray();

        if (empty($floors)) {
            $floors = [1, 2, 3, 4, 5, 6, 7, 8];
        }

        return [
            'tasks' => $tasks,
            'metrics' => $metrics,
            'floors' => $floors,
            'filter' => $dto,
            'target_date' => $targetDate,
        ];
    }
}
