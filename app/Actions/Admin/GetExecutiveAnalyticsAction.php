<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\ExecutiveAnalyticsFilterData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\Room;
use App\Models\RoomIncident;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GetExecutiveAnalyticsAction
{
    /**
     * Standard Ekraf Subsectors (17 Subsektor Ekraf Kemenparekraf)
     */
    public const EKRAF_SUBSECTORS = [
        'Aplikasi',
        'Pengembangan Permainan (Game)',
        'Arsitektur',
        'Desain Interior',
        'Desain Komunikasi Visual (DKV)',
        'Desain Produk',
        'Fashion',
        'Film, Animasi & Video',
        'Fotografi',
        'Kriya (Kerajinan Tangan)',
        'Kuliner',
        'Musik',
        'Penerbitan',
        'Periklanan',
        'Seni Pertunjukan',
        'Seni Rupa',
        'Televisi & Radio',
    ];

    /**
     * Compute comprehensive building performance analytics and KPI metrics (FR-ADM-06).
     *
     * @param ExecutiveAnalyticsFilterData $dto
     * @return array
     */
    public function execute(ExecutiveAnalyticsFilterData $dto): array
    {
        // 1. Determine Date Range (Default: Current Month)
        $dateFrom = $dto->dateFrom ? Carbon::parse($dto->dateFrom)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $dateTo = $dto->dateTo ? Carbon::parse($dto->dateTo)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        // Total Days in range
        $daysCount = max(1, $dateFrom->diffInDays($dateTo) + 1);

        // Operational hours per day: MCC opens 08:00 to 21:00 = 13 hours/day
        $dailyOperationalHours = 13;

        // 2. Base Query for Confirmed/Checked-in/Completed Slots
        $slotsQuery = BookingSlot::with(['room', 'booking.eventDetail', 'booking.user'])
            ->whereHas('booking', function ($bq) {
                $bq->whereIn('status', [
                    BookingStatus::CONFIRMED,
                    BookingStatus::CHECKED_IN,
                    BookingStatus::COMPLETED,
                    BookingStatus::ABANDONED_CLEAN,
                    BookingStatus::ABANDONED_INCIDENT,
                ]);
            })
            ->where('event_start_at', '>=', $dateFrom)
            ->where('event_start_at', '<=', $dateTo);

        if ($dto->floor) {
            $slotsQuery->whereHas('room', fn($rq) => $rq->where('floor_level', $dto->floor));
        }

        if ($dto->roomId) {
            $slotsQuery->where('room_id', $dto->roomId);
        }

        if ($dto->subsector) {
            $slotsQuery->whereHas('booking.eventDetail', fn($eq) => $eq->where('ekraf_subsector', $dto->subsector));
        }

        if ($dto->scheme) {
            $slotsQuery->whereHas('booking', fn($bq) => $bq->where('pricing_scheme', $dto->scheme));
        }

        $allSlots = $slotsQuery->get();

        // 3. KPI Summaries
        $totalBookingsCount = $allSlots->pluck('booking_id')->unique()->count();
        $totalSessionsCount = $allSlots->count();

        // Total utilized hours
        $totalUtilizedMinutes = 0;
        foreach ($allSlots as $slot) {
            if ($slot->event_start_at && $slot->event_end_at) {
                $diff = $slot->event_start_at->diffInMinutes($slot->event_end_at);
                $totalUtilizedMinutes += max(0, $diff);
            }
        }
        $totalUtilizedHours = round($totalUtilizedMinutes / 60, 1);

        // Available Capacity in Hours across target rooms
        $roomsQuery = Room::where('is_active', true);
        if ($dto->floor) {
            $roomsQuery->where('floor_level', $dto->floor);
        }
        if ($dto->roomId) {
            $roomsQuery->where('id', $dto->roomId);
        }
        $activeRooms = $roomsQuery->get();
        $activeRoomsCount = $activeRooms->count();
        $totalAvailableCapacityHours = $activeRoomsCount * $daysCount * $dailyOperationalHours;

        $overallOccupancyRate = $totalAvailableCapacityHours > 0
            ? round(min(100, ($totalUtilizedHours / $totalAvailableCapacityHours) * 100), 1)
            : 0;

        // 4. Occupancy Rate per Floor & per Room
        $floorOccupancy = [];
        for ($f = 1; $f <= 8; $f++) {
            $floorRooms = $activeRooms->where('floor_level', $f);
            $floorRoomsCount = $floorRooms->count();
            $floorAvailableHours = $floorRoomsCount * $daysCount * $dailyOperationalHours;

            $floorSlots = $allSlots->filter(fn($s) => $s->room && $s->room->floor_level === $f);
            $floorUsedMinutes = 0;
            foreach ($floorSlots as $fs) {
                $floorUsedMinutes += max(0, $fs->event_start_at->diffInMinutes($fs->event_end_at));
            }
            $floorUsedHours = round($floorUsedMinutes / 60, 1);
            $rate = $floorAvailableHours > 0 ? round(min(100, ($floorUsedHours / $floorAvailableHours) * 100), 1) : 0;

            $floorOccupancy[$f] = [
                'floor' => $f,
                'rooms_count' => $floorRoomsCount,
                'used_hours' => $floorUsedHours,
                'available_hours' => $floorAvailableHours,
                'occupancy_rate' => $rate,
            ];
        }

        // Room Occupancy Leaderboard (Top 10 Most Utilized)
        $roomOccupancyList = [];
        foreach ($activeRooms as $room) {
            $roomSlots = $allSlots->where('room_id', $room->id);
            $roomUsedMinutes = 0;
            foreach ($roomSlots as $rs) {
                $roomUsedMinutes += max(0, $rs->event_start_at->diffInMinutes($rs->event_end_at));
            }
            $roomUsedHours = round($roomUsedMinutes / 60, 1);
            $roomAvailableHours = $daysCount * $dailyOperationalHours;
            $rRate = $roomAvailableHours > 0 ? round(min(100, ($roomUsedHours / $roomAvailableHours) * 100), 1) : 0;

            $roomOccupancyList[] = [
                'id' => $room->id,
                'name' => $room->name,
                'floor_level' => $room->floor_level,
                'capacity' => $room->capacity,
                'used_hours' => $roomUsedHours,
                'available_hours' => $roomAvailableHours,
                'occupancy_rate' => $rRate,
                'events_count' => $roomSlots->pluck('booking_id')->unique()->count(),
            ];
        }
        usort($roomOccupancyList, fn($a, $b) => $b['occupancy_rate'] <=> $a['occupancy_rate']);

        // 5. 17 Ekraf Subsectors Distribution
        $ekrafDistribution = [];
        $totalEkrafEvents = 0;

        foreach (self::EKRAF_SUBSECTORS as $sub) {
            $subSlots = $allSlots->filter(function ($slot) use ($sub) {
                return $slot->booking?->eventDetail?->ekraf_subsector === $sub;
            });
            $subBookingsCount = $subSlots->pluck('booking_id')->unique()->count();
            $totalEkrafEvents += $subBookingsCount;

            $ekrafDistribution[$sub] = [
                'subsector' => $sub,
                'count' => $subBookingsCount,
                'percentage' => 0,
            ];
        }

        // Calculate percentages
        foreach ($ekrafDistribution as $key => $val) {
            $ekrafDistribution[$key]['percentage'] = $totalEkrafEvents > 0
                ? round(($val['count'] / $totalEkrafEvents) * 100, 1)
                : 0;
        }
        uasort($ekrafDistribution, fn($a, $b) => $b['count'] <=> $a['count']);

        // 6. Pricing Scheme Ratio (Subsidy Pemkot Malang vs Commercial)
        $subsidyBookings = $allSlots->filter(fn($s) => in_array($s->booking?->pricing_scheme, ['free', 'subsidy', 'community_subsidy']))->pluck('booking_id')->unique()->count();
        $commercialBookings = $allSlots->filter(fn($s) => $s->booking?->pricing_scheme === 'commercial')->pluck('booking_id')->unique()->count();
        $totalSchemeCount = $subsidyBookings + $commercialBookings;

        $subsidyPercentage = $totalSchemeCount > 0 ? round(($subsidyBookings / $totalSchemeCount) * 100, 1) : 0;
        $commercialPercentage = $totalSchemeCount > 0 ? round(($commercialBookings / $totalSchemeCount) * 100, 1) : 0;

        // Total commercial revenue generated in period
        $totalCommercialRevenue = Booking::whereIn('id', $allSlots->pluck('booking_id')->unique())
            ->where('pricing_scheme', 'commercial')
            ->sum('nominal_amount');

        // 7. Incident Statistics & Damage Recap per Room
        $incidentsQuery = RoomIncident::with(['booking.slots.room', 'reportedByDo'])
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', $dateTo);

        if ($dto->floor || $dto->roomId) {
            $incidentsQuery->whereHas('booking.slots.room', function ($rq) use ($dto) {
                if ($dto->floor) $rq->where('floor_level', $dto->floor);
                if ($dto->roomId) $rq->where('id', $dto->roomId);
            });
        }

        $allIncidents = $incidentsQuery->get();
        $totalIncidents = $allIncidents->count();
        $resolvedIncidents = $allIncidents->where('is_resolved', true)->count();
        $unresolvedIncidents = $totalIncidents - $resolvedIncidents;

        // Incidents grouped by category
        $incidentsByCategory = [];
        foreach ($allIncidents as $inc) {
            $cat = $inc->category ?: 'Kerusakan Umum';
            $incidentsByCategory[$cat] = ($incidentsByCategory[$cat] ?? 0) + 1;
        }
        arsort($incidentsByCategory);

        // Incidents grouped by Room
        $incidentsByRoom = [];
        foreach ($allIncidents as $inc) {
            $firstRoom = $inc->booking?->slots->first()?->room;
            $rName = $firstRoom ? $firstRoom->name . ' (Lt ' . $firstRoom->floor_level . ')' : 'Ruangan Umum';
            $incidentsByRoom[$rName] = ($incidentsByRoom[$rName] ?? 0) + 1;
        }
        arsort($incidentsByRoom);

        return [
            'filter' => [
                'date_from' => $dateFrom->toDateString(),
                'date_to' => $dateTo->toDateString(),
                'days_count' => $daysCount,
                'floor' => $dto->floor,
                'room_id' => $dto->roomId,
                'subsector' => $dto->subsector,
                'scheme' => $dto->scheme,
            ],
            'kpis' => [
                'occupancy_rate' => $overallOccupancyRate,
                'total_utilized_hours' => $totalUtilizedHours,
                'total_available_hours' => $totalAvailableCapacityHours,
                'total_bookings' => $totalBookingsCount,
                'total_sessions' => $totalSessionsCount,
                'active_rooms_count' => $activeRoomsCount,
                'total_incidents' => $totalIncidents,
                'resolved_incidents' => $resolvedIncidents,
                'unresolved_incidents' => $unresolvedIncidents,
                'commercial_revenue' => $totalCommercialRevenue,
            ],
            'floor_occupancy' => $floorOccupancy,
            'room_occupancy' => $roomOccupancyList,
            'ekraf_distribution' => $ekrafDistribution,
            'pricing_scheme' => [
                'subsidy_count' => $subsidyBookings,
                'subsidy_percentage' => $subsidyPercentage,
                'commercial_count' => $commercialBookings,
                'commercial_percentage' => $commercialPercentage,
                'total_revenue' => $totalCommercialRevenue,
            ],
            'incidents' => [
                'total' => $totalIncidents,
                'resolved' => $resolvedIncidents,
                'unresolved' => $unresolvedIncidents,
                'by_category' => $incidentsByCategory,
                'by_room' => $incidentsByRoom,
                'recent' => $allIncidents->sortByDesc('created_at')->take(5),
            ],
            'rooms_list' => Room::orderBy('floor_level')->orderBy('name')->get(),
        ];
    }
}
