<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\AssignDutyOfficerData;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AssignDutyOfficerAction
{
    /**
     * Assign and dispatch a Duty Officer to a booking and its room slots (FR-FO-04).
     *
     * @param AssignDutyOfficerData $dto
     * @return array{booking: Booking, duty_officer: User, assigned_slots_count: int}
     * @throws ValidationException
     */
    public function execute(AssignDutyOfficerData $dto): array
    {
        $booking = Booking::with(['slots.room', 'eventDetail'])->findOrFail($dto->bookingId);

        $dutyOfficer = User::where('id', $dto->dutyOfficerId)
            ->where('role', UserRole::DUTY_OFFICER)
            ->first();

        if (! $dutyOfficer) {
            throw ValidationException::withMessages([
                'duty_officer_id' => 'Staf yang dipilih bukan merupakan Duty Officer aktif yang berwenang.',
            ]);
        }

        return DB::transaction(function () use ($booking, $dutyOfficer, $dto) {
            $now = Carbon::now();

            // 1. Kaitkan penugasan Duty Officer ke entitas Booking
            $booking->update([
                'duty_officer_id' => $dutyOfficer->id,
            ]);

            // 2. Kaitkan penugasan Duty Officer ke slot sesi ruangan terkait
            $query = BookingSlot::where('booking_id', $booking->id);
            if ($dto->slotId) {
                $query->where('id', $dto->slotId);
            }

            $affectedSlots = $query->update([
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_assigned_at' => $now,
                'duty_officer_assigned_by' => $dto->assignedById,
                'duty_officer_notes' => $dto->notes,
            ]);

            Log::channel('single')->info("Security & Operation Audit [DUTY_OFFICER_DISPATCH]: DO {$dutyOfficer->full_name} assigned to booking {$booking->booking_code} ({$affectedSlots} slots).", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_name' => $dutyOfficer->full_name,
                'assigned_by' => $dto->assignedById,
                'assigned_at' => $now->toIso8601String(),
                'notes' => $dto->notes,
            ]);

            return [
                'booking' => $booking->fresh(['dutyOfficer', 'slots.dutyOfficer', 'slots.room']),
                'duty_officer' => $dutyOfficer,
                'assigned_slots_count' => $affectedSlots,
            ];
        });
    }
}
