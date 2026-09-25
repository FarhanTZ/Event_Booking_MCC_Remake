<?php

namespace App\Queries\Marketing;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetMarketingDashboardQuery
{
    /**
     * Get aggregate metric KPIs for the marketing dashboard (REQ-MKT-01).
     *
     * @return array<string, int>
     */
    public function getMetrics(): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();

        $metrics = Booking::query()
            ->selectRaw("
                COUNT(CASE WHEN status = 'awaiting_marketing' THEN 1 END) as awaiting_curation,
                COUNT(CASE WHEN status = 'revised' THEN 1 END) as revised,
                COUNT(CASE WHEN status = 'awaiting_payment' THEN 1 END) as awaiting_payment,
                COUNT(CASE WHEN status = 'confirmed' AND updated_at >= ? THEN 1 END) as approved_this_month,
                COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as total_confirmed,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
                COUNT(*) as total_all
            ", [$currentMonthStart])
            ->first();

        return [
            'awaiting_curation' => (int) ($metrics->awaiting_curation ?? 0),
            'revised' => (int) ($metrics->revised ?? 0),
            'awaiting_payment' => (int) ($metrics->awaiting_payment ?? 0),
            'approved_this_month' => (int) ($metrics->approved_this_month ?? 0),
            'total_confirmed' => (int) ($metrics->total_confirmed ?? 0),
            'rejected' => (int) ($metrics->rejected ?? 0),
            'total_all' => (int) ($metrics->total_all ?? 0),
        ];
    }

    /**
     * Get paginated curation proposals with multi-criteria filters & triage priority sorting (REQ-MKT-02 & REQ-MKT-03).
     */
    public function getCurations(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = Booking::query()
            ->with([
                'user',
                'eventDetail',
                'approvals.actionBy',
                'slots' => function ($slotQuery) {
                    $slotQuery->orderBy('event_start_at', 'asc')->with('room');
                },
            ]);

        // 1. Status Filter
        $status = $request->query('status');
        if ($status && in_array($status, ['awaiting_marketing', 'revised', 'awaiting_payment', 'confirmed', 'rejected'], true)) {
            $query->where('status', $status);
        }

        // 2. Search Filter (Booking Code, Event Name, PIC, Institution)
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'ILIKE', "%{$search}%")
                    ->orWhereHas('eventDetail', function ($eq) use ($search) {
                        $eq->where('event_name', 'ILIKE', "%{$search}%")
                            ->orWhere('pic_name_snapshot', 'ILIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('full_name', 'ILIKE', "%{$search}%")
                            ->orWhere('institution_name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // 3. Category Filter
        $category = $request->query('category');
        if ($category && $category !== 'all') {
            $query->whereHas('eventDetail', function ($eq) use ($category) {
                $eq->where('event_category', $category);
            });
        }

        // 4. Ekraf Sub-Sector Filter
        $subSector = $request->query('sub_sector');
        if ($subSector && $subSector !== 'all') {
            $query->whereHas('eventDetail', function ($eq) use ($subSector) {
                $eq->where('ekraf_subsector', $subSector);
            });
        }

        // 5. Floor Level Filter (Lantai 1 s/d 8)
        $floor = $request->query('floor_level');
        if ($floor && $floor !== 'all') {
            $query->whereHas('slots.room', function ($rq) use ($floor) {
                $rq->where('floor_level', (int) $floor);
            });
        }

        // 6. Event Execution Date Range Filter
        $dateFrom = $request->query('date_from');
        if ($dateFrom) {
            $query->whereHas('slots', function ($sq) use ($dateFrom) {
                $sq->where('event_start_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            });
        }

        $dateTo = $request->query('date_to');
        if ($dateTo) {
            $query->whereHas('slots', function ($sq) use ($dateTo) {
                $sq->where('event_start_at', '<=', Carbon::parse($dateTo)->endOfDay());
            });
        }

        // =========================================================================
        // REQ-MKT-02: ALGORITMA TRIASE PRIORITAS WAKTU
        // Prioritas 1: Tanggal acara paling dekat (MIN event_start_at ASC / H-x)
        // Prioritas 2 (FIFO): Waktu pengajuan terlama (created_at ASC) jika tanggal acara sama
        // =========================================================================
        return $query->orderBy(
            BookingSlot::select('event_start_at')
                ->whereColumn('booking_slots.booking_id', 'bookings.id')
                ->orderBy('event_start_at', 'asc')
                ->limit(1),
            'asc'
        )
        ->orderBy('bookings.created_at', 'asc')
        ->paginate($perPage)
        ->withQueryString();
    }

    /**
     * Get top Sub-Sektor Ekonomi Kreatif distribution from proposals.
     *
     * @return array<array{sub_sector: string, count: int}>
     */
    public function getEkrafDistribution(): array
    {
        return EventDetail::select('ekraf_subsector', DB::raw('count(*) as total'))
            ->whereNotNull('ekraf_subsector')
            ->where('ekraf_subsector', '!=', '')
            ->groupBy('ekraf_subsector')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'sub_sector' => (string) $item->ekraf_subsector,
                'count' => (int) $item->total,
            ])
            ->toArray();
    }
}
