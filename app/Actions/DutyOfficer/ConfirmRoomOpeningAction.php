<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ConfirmRoomOpeningData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ConfirmRoomOpeningAction
{
    /**
     * Confirm physical room opening, facility readiness inspection, and initial handover (FR-DO-02).
     *
     * @param ConfirmRoomOpeningData $dto
     * @return array{booking: Booking, duty_officer: User, opened_at: Carbon}
     * @throws ValidationException
     */
    public function execute(ConfirmRoomOpeningData $dto): array
    {
        $booking = Booking::with(['slots.room.facilities', 'eventDetail', 'dutyOfficer'])->findOrFail($dto->bookingId);

        if (! $booking->isCheckedIn()) {
            throw ValidationException::withMessages([
                'booking' => 'Pembukaan akses ruangan fisik hanya dapat dilakukan pada permohonan yang telah Checked-In di Front Office.',
            ]);
        }

        $dutyOfficer = User::findOrFail($dto->dutyOfficerId);

        return DB::transaction(function () use ($booking, $dutyOfficer, $dto) {
            $now = Carbon::now();

            // 1. Update Booking level room opening and checklist
            $booking->update([
                'room_opened_at' => $now,
                'room_opened_by' => $dutyOfficer->id,
                'room_open_notes' => $dto->notes,
                'facility_checklist' => $dto->facilityChecklist,
                // If duty_officer_id was not yet set on booking, assign the acting DO
                'duty_officer_id' => $booking->duty_officer_id ?? $dutyOfficer->id,
            ]);

            // 2. Update BookingSlot level room opening
            $slotQuery = BookingSlot::where('booking_id', $booking->id);
            if ($dto->slotId) {
                $slotQuery->where('id', $dto->slotId);
            }

            $slotQuery->update([
                'room_opened_at' => $now,
                'room_opened_by' => $dutyOfficer->id,
                'room_open_notes' => $dto->notes,
                'duty_officer_id' => DB::raw('COALESCE(duty_officer_id, \'' . $dutyOfficer->id . '\')'),
            ]);

            Log::channel('single')->info("Security & Operation Audit [ROOM_ACCESS_OPENED]: DO {$dutyOfficer->full_name} opened physical room for {$booking->booking_code}.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_name' => $dutyOfficer->full_name,
                'opened_at' => $now->toIso8601String(),
                'facility_checklist' => $dto->facilityChecklist,
                'notes' => $dto->notes,
            ]);

            return [
                'booking' => $booking->fresh(['roomOpenedBy', 'dutyOfficer', 'slots.room.facilities', 'eventDetail']),
                'duty_officer' => $dutyOfficer,
                'opened_at' => $now,
            ];
        });
    }
}
