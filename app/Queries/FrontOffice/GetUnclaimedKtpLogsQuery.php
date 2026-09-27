<?php

namespace App\Queries\FrontOffice;

use App\Enums\BookingStatus;
use App\Models\KtpVaultLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetUnclaimedKtpLogsQuery
{
    /**
     * Get aggregate KPI metrics for Unclaimed KTP Inventory (FR-FO-09).
     *
     * @return array<string, int>
     */
    public function getMetrics(): array
    {
        $baseQuery = KtpVaultLog::query()->whereNull('returned_at');

        $totalRetained = (clone $baseQuery)->count();
        $inUnclaimedBox = (clone $baseQuery)->whereNotNull('unclaimed_box_id')->count();
        $inDrawer = (clone $baseQuery)->whereNull('unclaimed_box_id')->count();

        $withIncidents = (clone $baseQuery)
            ->whereHas('booking', function ($q) {
                $q->where('status', BookingStatus::ABANDONED_INCIDENT)
                    ->orWhere('clearance_status', 'incident_detected');
            })
            ->count();

        return [
            'total_retained_ktp' => $totalRetained,
            'in_unclaimed_box' => $inUnclaimedBox,
            'in_drawer' => $inDrawer,
            'with_incidents' => $withIncidents,
        ];
    }

    /**
     * Get paginated unclaimed KTP inventory records (FR-FO-09).
     *
     * @param Request $request
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUnclaimedKtpLogs(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = KtpVaultLog::query()
            ->whereNull('returned_at')
            ->with([
                'receivedBy',
                'booking.user',
                'booking.eventDetail',
                'booking.latestIncident.photos',
                'booking.slots' => function ($sq) {
                    $sq->orderBy('event_start_at', 'asc')->with('room');
                },
            ]);

        // 1. Status / Category Filter
        $filter = $request->query('status', 'all');
        if ($filter === 'unclaimed_box') {
            $query->whereNotNull('unclaimed_box_id');
        } elseif ($filter === 'in_drawer') {
            $query->whereNull('unclaimed_box_id');
        } elseif ($filter === 'with_incidents') {
            $query->whereHas('booking', function ($q) {
                $q->where('status', BookingStatus::ABANDONED_INCIDENT)
                    ->orWhere('clearance_status', 'incident_detected');
            });
        }

        // 2. Instant Search Filter
        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $likeOperator = DB::getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';

            $query->where(function ($q) use ($search, $likeOperator) {
                $q->where('ktp_holder_name', $likeOperator, "%{$search}%")
                    ->orWhere('nik_number', $likeOperator, "%{$search}%")
                    ->orWhere('unclaimed_box_id', $likeOperator, "%{$search}%")
                    ->orWhere('storage_drawer_number', $likeOperator, "%{$search}%")
                    ->orWhere('pic_whatsapp_number', $likeOperator, "%{$search}%")
                    ->orWhereHas('booking', function ($bq) use ($search, $likeOperator) {
                        $bq->where('booking_code', $likeOperator, "%{$search}%")
                            ->orWhereHas('eventDetail', function ($eq) use ($search, $likeOperator) {
                                $eq->where('event_name', $likeOperator, "%{$search}%")
                                    ->orWhere('pic_name_snapshot', $likeOperator, "%{$search}%")
                                    ->orWhere('institution_name_snapshot', $likeOperator, "%{$search}%");
                            })
                            ->orWhereHas('user', function ($uq) use ($search, $likeOperator) {
                                $uq->where('full_name', $likeOperator, "%{$search}%")
                                    ->orWhere('institution_name', $likeOperator, "%{$search}%");
                            });
                    });
            });
        }

        // Urutkan dari yang paling lama tertahan (retained_at ASC) agar prioritas penanganan jelas
        return $query->orderBy('retained_at', 'asc')
            ->paginate($perPage)
            ->withQueryString();
    }
}
