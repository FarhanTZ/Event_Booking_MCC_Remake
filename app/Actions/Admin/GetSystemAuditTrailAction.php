<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\SystemAuditTrailFilterData;
use App\Models\BookingApproval;
use App\Models\KtpVaultLog;
use App\Models\RoomIncident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GetSystemAuditTrailAction
{
    /**
     * Aggregate, filter, and paginate system-wide audit trails across all operational checkpoints (FR-ADM-07).
     *
     * @param SystemAuditTrailFilterData $dto
     * @return array{audit_logs: LengthAwarePaginator, metrics: array, filter: SystemAuditTrailFilterData, actors: Collection}
     */
    public function execute(SystemAuditTrailFilterData $dto): array
    {
        $dateFrom = $dto->dateFrom ? Carbon::parse($dto->dateFrom)->startOfDay() : null;
        $dateTo = $dto->dateTo ? Carbon::parse($dto->dateTo)->endOfDay() : null;

        $items = collect();

        // 1. Marketing & Curation Approvals + Emergency Overrides
        if ($dto->type === 'all' || $dto->type === 'curation' || $dto->type === 'override') {
            $approvalsQuery = BookingApproval::with(['booking.eventDetail', 'booking.user', 'actionBy']);

            if ($dateFrom) {
                $approvalsQuery->where('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $approvalsQuery->where('created_at', '<=', $dateTo);
            }
            if ($dto->actorId) {
                $approvalsQuery->where('action_by', $dto->actorId);
            }
            if ($dto->actorRole) {
                $approvalsQuery->whereHas('actionBy', fn($aq) => $aq->where('role', $dto->actorRole));
            }
            if ($dto->type === 'override') {
                $approvalsQuery->where('decision', 'emergency_cancelled');
            } elseif ($dto->type === 'curation') {
                $approvalsQuery->where('decision', '!=', 'emergency_cancelled');
            }

            foreach ($approvalsQuery->get() as $approval) {
                $isOverride = $approval->decision === 'emergency_cancelled';
                $decisionLabel = match ($approval->decision) {
                    'approved' => 'Persetujuan Proposal (Approved)',
                    'revised' => 'Permintaan Revisi Proposal',
                    'rejected' => 'Penolakan Proposal (Rejected)',
                    'payment_confirmed' => 'Konfirmasi Pembayaran Kas',
                    'revision_submitted' => 'Pengajuan Ulang Revisi',
                    'emergency_cancelled' => 'Super Override (Batal Darurat)',
                    default => ucfirst(str_replace('_', ' ', $approval->decision)),
                };

                $category = $isOverride ? 'override' : 'curation';

                $items->push([
                    'id' => 'approval_' . $approval->id,
                    'timestamp' => $approval->created_at,
                    'category' => $category,
                    'category_label' => $isOverride ? 'Super Override Darurat' : 'Keputusan Kurasi Marketing',
                    'action_title' => $decisionLabel,
                    'booking_code' => $approval->booking?->booking_code ?? '-',
                    'booking_id' => $approval->booking_id,
                    'event_name' => $approval->booking?->eventDetail?->event_name ?? 'Acara MCC',
                    'applicant_name' => $approval->booking?->user?->full_name ?? '-',
                    'actor_name' => $approval->actionBy?->full_name ?? 'Sistem MCC',
                    'actor_role' => $approval->actionBy?->role?->value ?? ($approval->actionBy?->role ?? 'marketing'),
                    'actor_role_label' => $approval->actionBy?->role ? (is_object($approval->actionBy->role) ? $approval->actionBy->role->label() : ucfirst((string)$approval->actionBy->role)) : 'Staf',
                    'notes' => $approval->notes,
                    'meta' => [
                        'decision' => $approval->decision,
                        'is_override' => $isOverride,
                    ],
                ]);
            }
        }

        // 2. Front Office Physical KTP Vault Logs
        if ($dto->type === 'all' || $dto->type === 'vault') {
            $vaultQuery = KtpVaultLog::with(['booking.eventDetail', 'booking.user', 'receivedBy', 'returnedBy']);

            if ($dateFrom) {
                $vaultQuery->where(function ($q) use ($dateFrom) {
                    $q->where('retained_at', '>=', $dateFrom)
                        ->orWhere('returned_at', '>=', $dateFrom);
                });
            }
            if ($dateTo) {
                $vaultQuery->where(function ($q) use ($dateTo) {
                    $q->where('retained_at', '<=', $dateTo)
                        ->orWhere('returned_at', '<=', $dateTo);
                });
            }
            if ($dto->actorId) {
                $vaultQuery->where(function ($q) use ($dto) {
                    $q->where('received_by_fo', $dto->actorId)
                        ->orWhere('returned_by_fo', $dto->actorId);
                });
            }
            if ($dto->actorRole) {
                $vaultQuery->where(function ($q) use ($dto) {
                    $q->whereHas('receivedBy', fn($rq) => $rq->where('role', $dto->actorRole))
                        ->orWhereHas('returnedBy', fn($rq) => $rq->where('role', $dto->actorRole));
                });
            }

            foreach ($vaultQuery->get() as $log) {
                // Check-in Retention Log
                $items->push([
                    'id' => 'vault_in_' . $log->id,
                    'timestamp' => $log->retained_at,
                    'category' => 'vault',
                    'category_label' => 'Kustodian KTP Front Office',
                    'action_title' => 'Penyimpanan KTP Fisik Pemohon',
                    'booking_code' => $log->booking?->booking_code ?? '-',
                    'booking_id' => $log->booking_id,
                    'event_name' => $log->booking?->eventDetail?->event_name ?? 'Acara MCC',
                    'applicant_name' => $log->ktp_holder_name ?: ($log->booking?->user?->full_name ?? '-'),
                    'actor_name' => $log->receivedBy?->full_name ?? 'Front Office FO',
                    'actor_role' => $log->receivedBy?->role?->value ?? ($log->receivedBy?->role ?? 'front_office'),
                    'actor_role_label' => $log->receivedBy?->role ? (is_object($log->receivedBy->role) ? $log->receivedBy->role->label() : ucfirst((string)$log->receivedBy->role)) : 'Front Office',
                    'notes' => 'KTP fisik disimpan di Laci #' . ($log->storage_drawer_number ?: '-') . ($log->notes ? '. Catatan: ' . $log->notes : ''),
                    'meta' => [
                        'drawer' => $log->storage_drawer_number,
                        'event' => 'checkin_retain',
                    ],
                ]);

                // Check-out Return Log (if already returned)
                if ($log->returned_at) {
                    $items->push([
                        'id' => 'vault_out_' . $log->id,
                        'timestamp' => $log->returned_at,
                        'category' => 'vault',
                        'category_label' => 'Kustodian KTP Front Office',
                        'action_title' => 'Pengembalian KTP Fisik Pemohon',
                        'booking_code' => $log->booking?->booking_code ?? '-',
                        'booking_id' => $log->booking_id,
                        'event_name' => $log->booking?->eventDetail?->event_name ?? 'Acara MCC',
                        'applicant_name' => $log->ktp_holder_name ?: ($log->booking?->user?->full_name ?? '-'),
                        'actor_name' => $log->returnedBy?->full_name ?? ($log->receivedBy?->full_name ?? 'Front Office FO'),
                        'actor_role' => $log->returnedBy?->role?->value ?? ($log->returnedBy?->role ?? 'front_office'),
                        'actor_role_label' => $log->returnedBy?->role ? (is_object($log->returnedBy->role) ? $log->returnedBy->role->label() : ucfirst((string)$log->returnedBy->role)) : 'Front Office',
                        'notes' => 'KTP fisik telah dikembalikan ke pemohon setelah status clearance terpenuhi.',
                        'meta' => [
                            'drawer' => $log->storage_drawer_number,
                            'event' => 'checkout_return',
                        ],
                    ]);
                }
            }
        }

        // 3. Duty Officer Room Incidents & Damage Reporting
        if ($dto->type === 'all' || $dto->type === 'incident') {
            $incidentsQuery = RoomIncident::with(['booking.eventDetail', 'booking.user', 'reportedByDo']);

            if ($dateFrom) {
                $incidentsQuery->where('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $incidentsQuery->where('created_at', '<=', $dateTo);
            }
            if ($dto->actorId) {
                $incidentsQuery->where('reported_by_do', $dto->actorId);
            }
            if ($dto->actorRole) {
                $incidentsQuery->whereHas('reportedByDo', fn($rq) => $rq->where('role', $dto->actorRole));
            }

            foreach ($incidentsQuery->get() as $incident) {
                $items->push([
                    'id' => 'incident_' . $incident->id,
                    'timestamp' => $incident->created_at,
                    'category' => 'incident',
                    'category_label' => 'Inspeksi & Insiden Lapangan',
                    'action_title' => 'Pelaporan Insiden Fasilitas: ' . $incident->category,
                    'booking_code' => $incident->booking?->booking_code ?? '-',
                    'booking_id' => $incident->booking_id,
                    'event_name' => $incident->booking?->eventDetail?->event_name ?? 'Acara MCC',
                    'applicant_name' => $incident->booking?->user?->full_name ?? '-',
                    'actor_name' => $incident->reportedByDo?->full_name ?? 'Duty Officer DO',
                    'actor_role' => $incident->reportedByDo?->role?->value ?? ($incident->reportedByDo?->role ?? 'duty_officer'),
                    'actor_role_label' => $incident->reportedByDo?->role ? (is_object($incident->reportedByDo->role) ? $incident->reportedByDo->role->label() : ucfirst((string)$incident->reportedByDo->role)) : 'Duty Officer',
                    'notes' => ($incident->item_name ? '[' . $incident->item_name . '] ' : '') . $incident->description . ($incident->is_resolved ? ' (Status: Terselesaikan)' : ' (Status: Menunggu Ganti Rugi)'),
                    'meta' => [
                        'category' => $incident->category,
                        'is_resolved' => $incident->is_resolved,
                        'resolved_at' => $incident->resolved_at,
                    ],
                ]);
            }
        }

        // 4. Search Filter (by Booking Code, Event Name, Actor Name, Applicant Name, or Notes)
        if ($dto->search) {
            $needle = mb_strtolower($dto->search);
            $items = $items->filter(function ($item) use ($needle) {
                return str_contains(mb_strtolower((string) $item['booking_code']), $needle)
                    || str_contains(mb_strtolower((string) $item['event_name']), $needle)
                    || str_contains(mb_strtolower((string) $item['applicant_name']), $needle)
                    || str_contains(mb_strtolower((string) $item['actor_name']), $needle)
                    || str_contains(mb_strtolower((string) $item['notes']), $needle)
                    || str_contains(mb_strtolower((string) $item['action_title']), $needle);
            });
        }

        // Sort descending by timestamp
        $sortedItems = $items->sortByDesc('timestamp')->values();

        // Count metrics
        $metrics = [
            'total_logs' => $sortedItems->count(),
            'curation_count' => $sortedItems->where('category', 'curation')->count(),
            'vault_count' => $sortedItems->where('category', 'vault')->count(),
            'incident_count' => $sortedItems->where('category', 'incident')->count(),
            'override_count' => $sortedItems->where('category', 'override')->count(),
        ];

        // Manual Pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = $dto->perPage;
        $paginatedItems = $sortedItems->slice(($page - 1) * $perPage, $perPage)->values();

        $paginator = new LengthAwarePaginator(
            $paginatedItems,
            $sortedItems->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // List of all active staff actors for dropdown filter
        $actors = User::whereIn('role', ['super_admin', 'marketing', 'front_office', 'duty_officer'])
            ->orderBy('full_name')
            ->get();

        return [
            'audit_logs' => $paginator,
            'metrics' => $metrics,
            'filter' => $dto,
            'actors' => $actors,
        ];
    }
}
