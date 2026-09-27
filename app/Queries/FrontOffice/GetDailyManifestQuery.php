<?php

namespace App\Queries\FrontOffice;

use App\Enums\BookingStatus;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetDailyManifestQuery
{
    /**
     * Get aggregate KPI metrics for Front Office daily manifest (CURRENT_DATE).
     *
     * @param Carbon|null $targetDate
     * @return array<string, int>
     */
    public function getMetrics(?Carbon $targetDate = null): array
    {
        $date = $targetDate ?? Carbon::today();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Mengambil seluruh slot aktif hari ini yang booking induknya valid (confirmed, checked_in, completed)
        $todaySlots = BookingSlot::query()
            ->where('is_active', true)
            ->whereBetween('event_start_at', [$startOfDay, $endOfDay])
            ->whereHas('booking', function ($bq) {
                $bq->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::CHECKED_IN,
                    BookingStatus::COMPLETED,
                ]);
            })
            ->with(['booking', 'room'])
            ->get();

        $totalSlotsToday = $todaySlots->count();
        $confirmedCount = $todaySlots->where('booking.status', BookingStatus::CONFIRMED)->count();
        $checkedInCount = $todaySlots->where('booking.status', BookingStatus::CHECKED_IN)->count();
        $completedCount = $todaySlots->where('booking.status', BookingStatus::COMPLETED)->count();

        // Total ruangan unik yang aktif digunakan hari ini
        $activeRoomsCount = $todaySlots->pluck('room_id')->unique()->count();

        return [
            'total_slots_today' => $totalSlotsToday,
            'confirmed_slots' => $confirmedCount,
            'checked_in_slots' => $checkedInCount,
            'completed_slots' => $completedCount,
            'active_rooms_count' => $activeRoomsCount,
        ];
    }

    /**
     * Get paginated daily manifest sessions for CURRENT_DATE (FR-FO-01).
     * Filtered strictly to sessions occurring on the target date.
     *
     * @param Request $request
     * @param Carbon|null $targetDate
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getDailyManifestSlots(Request $request, ?Carbon $targetDate = null, int $perPage = 15): LengthAwarePaginator
    {
        $date = $targetDate ?? Carbon::today();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $query = BookingSlot::query()
            ->where('is_active', true)
            ->whereBetween('event_start_at', [$startOfDay, $endOfDay])
            ->whereHas('booking', function ($bq) {
                $bq->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::CHECKED_IN,
                    BookingStatus::COMPLETED,
                ]);
            })
            ->with([
                'room',
                'dutyOfficer',
                'booking.user',
                'booking.eventDetail',
                'booking.confirmedBy',
                'booking.dutyOfficer',
                'booking.slots' => function ($sq) {
                    $sq->orderBy('event_start_at', 'asc')->with(['room', 'dutyOfficer']);
                },
            ]);

        // 1. Status Filter (Confirmed, Checked-In, Completed)
        $status = $request->query('status');
        if ($status && in_array($status, ['confirmed', 'checked_in', 'completed'], true)) {
            $query->whereHas('booking', function ($bq) use ($status) {
                $bq->where('status', $status);
            });
        }

        // 2. Instant Search Filter (Kode Booking, Nama Acara, Ruangan/Lantai, PIC, Lembaga)
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $likeOperator = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($search, $likeOperator) {
                $q->whereHas('booking', function ($bq) use ($search, $likeOperator) {
                    $bq->where('booking_code', $likeOperator, "%{$search}%")
                        ->orWhereHas('eventDetail', function ($eq) use ($search, $likeOperator) {
                            $eq->where('event_name', $likeOperator, "%{$search}%")
                                ->orWhere('pic_name_snapshot', $likeOperator, "%{$search}%")
                                ->orWhere('pic_phone_snapshot', $likeOperator, "%{$search}%")
                                ->orWhere('institution_name_snapshot', $likeOperator, "%{$search}%");
                        })
                        ->orWhereHas('user', function ($uq) use ($search, $likeOperator) {
                            $uq->where('full_name', $likeOperator, "%{$search}%")
                                ->orWhere('phone_number', $likeOperator, "%{$search}%")
                                ->orWhere('institution_name', $likeOperator, "%{$search}%");
                        });
                })
                ->orWhereHas('room', function ($rq) use ($search, $likeOperator) {
                    $rq->where('name', $likeOperator, "%{$search}%")
                        ->orWhere('floor_level', '=', is_numeric($search) ? (int) $search : -1);
                });
            });
        }

        // 3. Floor Level Filter (Lantai 1 - 8)
        $floor = $request->query('floor_level');
        if ($floor && $floor !== 'all') {
            $query->whereHas('room', function ($rq) use ($floor) {
                $rq->where('floor_level', (int) $floor);
            });
        }

        // 4. Room Filter
        $roomId = $request->query('room_id');
        if ($roomId && $roomId !== 'all') {
            $query->where('room_id', (int) $roomId);
        }

        // 5. Urutan Operasional: Jam pelaksanaan sesi mulai paling awal (event_start_at ASC)
        return $query->orderBy('event_start_at', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get active Duty Officers list for room assignment disposition (FR-FO-04).
     *
     * @return Collection<\App\Models\User>
     */
    public function getActiveDutyOfficers(): Collection
    {
        return \App\Models\User::query()
            ->where('role', \App\Enums\UserRole::DUTY_OFFICER)
            ->orderBy('full_name', 'asc')
            ->get();
    }
}
