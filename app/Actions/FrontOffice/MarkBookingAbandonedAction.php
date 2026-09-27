<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\MarkAbandonedData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\KtpVaultLog;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MarkBookingAbandonedAction
{
    public function __construct(
        protected SecurityAuditLogger $auditLogger
    ) {}

    /**
     * Execute abandoned protocol when committee leaves without checking out at FO desk (FR-FO-08).
     *
     * @param MarkAbandonedData $dto
     * @return array{booking: Booking, user_suspended: bool, new_status: BookingStatus, unclaimed_box_id: string, ktp_holder_name: string|null}
     * @throws ValidationException
     */
    public function execute(MarkAbandonedData $dto): array
    {
        return DB::transaction(function () use ($dto) {
            /** @var Booking|null $booking */
            $booking = Booking::query()
                ->where('id', $dto->bookingId)
                ->with(['user', 'eventDetail', 'latestKtpVaultLog', 'latestIncident'])
                ->lockForUpdate()
                ->first();

            if (! $booking) {
                throw ValidationException::withMessages([
                    'booking' => ['Permohonan booking tidak ditemukan dalam sistem.'],
                ]);
            }

            if (! $booking->isCheckedIn()) {
                throw ValidationException::withMessages([
                    'status' => ["Aksi protokol tertinggal hanya dapat dilakukan pada peminjaman berstatus Checked-In. Status saat ini: {$booking->status->label()}."],
                ]);
            }

            $user = $booking->user;
            $hasIncident = ($booking->clearance_status === 'incident_detected');
            $newStatus = $hasIncident ? BookingStatus::ABANDONED_INCIDENT : BookingStatus::ABANDONED_CLEAN;
            $now = Carbon::now();

            // 1. Update Booking status & completion metadata
            $booking->status = $newStatus;
            $booking->completed_at = $now;
            $booking->completed_by = $dto->markedById;
            $booking->save();

            // 2. If room has incidents, automatically suspend user account from future bookings
            $userSuspended = false;
            if ($hasIncident && $user) {
                $reason = "Meninggalkan gedung tanpa lapor pasca-acara dengan catatan insiden kerusakan/kebersihan pada booking {$booking->booking_code}.";
                $user->update([
                    'is_suspended' => true,
                    'suspension_reason' => $reason,
                    'suspended_at' => $now,
                ]);
                $userSuspended = true;

                $this->auditLogger->logAction(
                    action: 'user.auto_suspended_abandoned',
                    description: "Akun user {$user->email} otomatis dibekukan karena meninggalkan gedung tanpa lapor dengan insiden ruangan ({$booking->booking_code}).",
                    userId: $dto->markedById,
                    modelType: 'User',
                    modelId: (string) $user->id,
                    metadata: [
                        'booking_code' => $booking->booking_code,
                        'reason' => $reason,
                        'suspended_at' => $now->toIso8601String(),
                    ]
                );
            }

            // 3. Move physical KTP to unclaimed archive box
            /** @var KtpVaultLog|null $ktpLog */
            $ktpLog = $booking->latestKtpVaultLog;
            if ($ktpLog) {
                $ktpLog->unclaimed_box_id = $dto->unclaimedBoxId;
                if ($dto->notes) {
                    $ktpLog->notes = $ktpLog->notes 
                        ? $ktpLog->notes . ' | [Protokol Ditinggal: ' . $dto->notes . ']'
                        : '[Protokol Ditinggal: ' . $dto->notes . ']';
                }
                $ktpLog->save();
            }

            // 4. Record Security Audit Log
            $this->auditLogger->logAction(
                action: 'booking.marked_abandoned',
                description: "Permohonan {$booking->booking_code} ditandai Panitia Pulang Tanpa Lapor ({$newStatus->label()}). Fisik KTP dialihkan ke Kotak Arsip: {$dto->unclaimedBoxId}.",
                userId: $dto->markedById,
                modelType: 'Booking',
                modelId: (string) $booking->id,
                metadata: [
                    'booking_code' => $booking->booking_code,
                    'new_status' => $newStatus->value,
                    'unclaimed_box_id' => $dto->unclaimedBoxId,
                    'has_incident' => $hasIncident,
                    'user_suspended' => $userSuspended,
                    'marked_at' => $now->toIso8601String(),
                ]
            );

            return [
                'booking' => $booking->fresh(['user', 'eventDetail', 'latestKtpVaultLog', 'latestIncident']),
                'user_suspended' => $userSuspended,
                'new_status' => $newStatus,
                'unclaimed_box_id' => $dto->unclaimedBoxId,
                'ktp_holder_name' => $ktpLog?->ktp_holder_name ?? ($booking->eventDetail->pic_name_snapshot ?? ($user?->full_name ?? 'Panitia')),
            ];
        });
    }
}
