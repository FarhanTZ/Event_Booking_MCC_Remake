<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ConfirmAbandonedCleanData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ConfirmAbandonedCleanAction
{
    /**
     * Confirm abandoned room is clean and intact (FR-DO-07 Opsi A).
     *
     * @param ConfirmAbandonedCleanData $dto
     * @return array{booking: Booking, duty_officer: User}
     * @throws ValidationException
     */
    public function execute(ConfirmAbandonedCleanData $dto): array
    {
        $booking = Booking::with(['slots.room', 'eventDetail', 'latestKtpVaultLog', 'user'])->findOrFail($dto->bookingId);

        if (! $booking->isCheckedIn() && ! $booking->isAbandoned() && ! $booking->isCompleted()) {
            throw ValidationException::withMessages([
                'booking' => 'Konfirmasi ruangan ditinggal bersih hanya dapat diproses pada permohonan yang aktif atau sedang dalam penanganan anomali.',
            ]);
        }

        $dutyOfficer = User::findOrFail($dto->dutyOfficerId);

        return DB::transaction(function () use ($booking, $dutyOfficer, $dto) {
            $now = Carbon::now();

            // 1. Update Booking status to ABANDONED_CLEAN and clearance_status to 'cleared'
            $booking->status = BookingStatus::ABANDONED_CLEAN;
            $booking->clearance_status = 'cleared';
            $booking->completed_at = $booking->completed_at ?? $now;
            $booking->completed_by = $dutyOfficer->id;
            $booking->assigned_checkout_do_id = $booking->assigned_checkout_do_id ?? $dutyOfficer->id;
            $booking->checkout_duty_officer_id = $dutyOfficer->id;
            $booking->checkout_do_notes = $dto->notes 
                ? "[Ditinggal Bersih] {$dto->notes}" 
                : "Dikonfirmasi Ditinggal Bersih & Fasilitas Lengkap oleh DO {$dutyOfficer->full_name}.";
            $booking->save();

            // 2. Update slot assignment if slotId present
            if ($dto->slotId) {
                BookingSlot::where('id', $dto->slotId)->update([
                    'assigned_checkout_do_id' => $dutyOfficer->id,
                    'checkout_duty_officer_id' => $dutyOfficer->id,
                ]);
            }

            // 3. Record Security & Operational Audit Log
            SecurityAuditLogger::logAction(
                action: 'booking.abandoned_clean_confirmed',
                description: "DO {$dutyOfficer->full_name} mengonfirmasi ruangan {$booking->booking_code} ditinggal dalam kondisi bersih dan aman (abandoned_clean).",
                userId: $dutyOfficer->id,
                modelType: 'Booking',
                modelId: (string) $booking->id,
                metadata: [
                    'booking_code' => $booking->booking_code,
                    'duty_officer_id' => $dutyOfficer->id,
                    'duty_officer_name' => $dutyOfficer->full_name,
                    'cleanliness_checklist' => $dto->cleanlinessChecklist,
                    'inventory_checklist' => $dto->inventoryChecklist,
                    'notes' => $dto->notes,
                    'timestamp' => $now->toIso8601String(),
                ]
            );

            Log::channel('single')->info("Operational Alert [ABANDONED_CLEAN]: DO {$dutyOfficer->full_name} confirmed clean abandoned room for {$booking->booking_code}.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
            ]);

            return [
                'booking' => $booking->fresh(['slots.room', 'eventDetail', 'latestKtpVaultLog', 'checkoutDutyOfficer', 'dutyOfficer']),
                'duty_officer' => $dutyOfficer,
            ];
        });
    }
}
