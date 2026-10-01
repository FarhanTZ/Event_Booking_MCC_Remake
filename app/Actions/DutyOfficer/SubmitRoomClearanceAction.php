<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\SubmitRoomClearanceData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubmitRoomClearanceAction
{
    /**
     * Submit room clearance signal (FR-DO-04: Green Signal - Room Clean & Intact).
     *
     * @param SubmitRoomClearanceData $dto
     * @return array{booking: Booking, duty_officer: User, cleared_at: Carbon}
     * @throws ValidationException
     */
    public function execute(SubmitRoomClearanceData $dto): array
    {
        $booking = Booking::with(['slots.room.facilities', 'eventDetail', 'latestKtpVaultLog'])->findOrFail($dto->bookingId);

        // 1. Guard Clause: Hanya acara yang Checked-In atau Completed yang dapat di-clear
        if (! $booking->isCheckedIn() && ! $booking->isCompleted()) {
            throw ValidationException::withMessages([
                'booking' => 'Verifikasi kelayakan ruangan checkout hanya dapat dilakukan pada acara yang sedang berlangsung (Checked-In).',
            ]);
        }

        $dutyOfficer = User::findOrFail($dto->dutyOfficerId);

        return DB::transaction(function () use ($booking, $dutyOfficer, $dto) {
            $now = Carbon::now();

            // 2. Simpan status kelayakan: clearance_status = 'cleared'
            $booking->update([
                'clearance_status' => 'cleared',
                'assigned_checkout_do_id' => $booking->assigned_checkout_do_id ?? $dutyOfficer->id,
                'checkout_duty_officer_id' => $booking->checkout_duty_officer_id ?? $dutyOfficer->id,
                'checkout_do_notes' => $dto->notes ? ($booking->checkout_do_notes ? $booking->checkout_do_notes . ' | ' . $dto->notes : $dto->notes) : $booking->checkout_do_notes,
            ]);

            // Update BookingSlot level jika ada
            if ($dto->slotId) {
                BookingSlot::where('id', $dto->slotId)->update([
                    'assigned_checkout_do_id' => $booking->assigned_checkout_do_id ?? $dutyOfficer->id,
                    'checkout_duty_officer_id' => $booking->checkout_duty_officer_id ?? $dutyOfficer->id,
                ]);
            }

            Log::channel('single')->info("Security & Operation Audit [ROOM_CLEARANCE_GREEN_SIGNAL]: DO {$dutyOfficer->full_name} submitted CLEARED signal for {$booking->booking_code}.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_name' => $dutyOfficer->full_name,
                'cleared_at' => $now->toIso8601String(),
                'cleanliness_checklist' => $dto->cleanlinessChecklist,
                'inventory_checklist' => $dto->inventoryChecklist,
                'notes' => $dto->notes,
            ]);

            return [
                'booking' => $booking->fresh(['checkoutDutyOfficer', 'dutyOfficer', 'latestKtpVaultLog', 'slots.room']),
                'duty_officer' => $dutyOfficer,
                'cleared_at' => $now,
            ];
        });
    }
}
