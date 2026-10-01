<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ResolveRoomIncidentData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\RoomIncident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ResolveRoomIncidentAction
{
    /**
     * Resolve field room incident and transmit Resolved Signal (clearance_status = 'resolved') (FR-DO-06).
     *
     * @param ResolveRoomIncidentData $dto
     * @return array{booking: Booking, incident: RoomIncident, duty_officer: User, resolved_at: Carbon}
     * @throws ValidationException
     */
    public function execute(ResolveRoomIncidentData $dto): array
    {
        $booking = Booking::with(['slots.room', 'eventDetail', 'latestKtpVaultLog', 'incidents'])->findOrFail($dto->bookingId);

        if (! $booking->isCheckedIn() && ! $booking->isCompleted()) {
            throw ValidationException::withMessages([
                'booking' => 'Penyelesaian insiden hanya dapat dilakukan pada acara yang sedang berjalan atau dalam proses checkout.',
            ]);
        }

        // Cari insiden terkait
        $incident = null;
        if ($dto->incidentId) {
            $incident = RoomIncident::where('id', $dto->incidentId)->where('booking_id', $booking->id)->first();
        }
        if (! $incident) {
            $incident = $booking->incidents()->where('is_resolved', false)->latest('id')->first();
        }

        if (! $incident) {
            // Jika tidak ada incident record spesifik, buat / fallback ke insiden terakhir
            $incident = $booking->incidents()->latest('id')->first();
        }

        $dutyOfficer = User::findOrFail($dto->dutyOfficerId);

        return DB::transaction(function () use ($booking, $incident, $dutyOfficer, $dto) {
            $now = Carbon::now();

            // 1. Update status insiden: is_resolved = true, resolved_at = NOW(), resolution_notes
            if ($incident) {
                $incident->update([
                    'is_resolved' => true,
                    'resolution_notes' => $dto->resolutionNotes,
                    'resolved_at' => $now,
                ]);
            }

            // 2. Perbarui status clearance booking menjadi 'resolved'
            $booking->update([
                'clearance_status' => 'resolved',
                'assigned_checkout_do_id' => $booking->assigned_checkout_do_id ?? $dutyOfficer->id,
                'checkout_duty_officer_id' => $booking->checkout_duty_officer_id ?? $dutyOfficer->id,
                'checkout_do_notes' => "Insiden Diselesaikan: {$dto->resolutionNotes}",
            ]);

            if ($dto->slotId) {
                BookingSlot::where('id', $dto->slotId)->update([
                    'assigned_checkout_do_id' => $booking->assigned_checkout_do_id ?? $dutyOfficer->id,
                    'checkout_duty_officer_id' => $booking->checkout_duty_officer_id ?? $dutyOfficer->id,
                ]);
            }

            Log::channel('single')->info("Security & Operation Audit [ROOM_INCIDENT_RESOLVED]: DO {$dutyOfficer->full_name} marked incident as RESOLVED for {$booking->booking_code}. Front Office KTP release unlocked!", [
                'incident_id' => $incident?->id,
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_name' => $dutyOfficer->full_name,
                'resolution_notes' => $dto->resolutionNotes,
                'resolved_at' => $now->toIso8601String(),
            ]);

            return [
                'booking' => $booking->fresh(['latestIncident.photos', 'checkoutDutyOfficer', 'dutyOfficer']),
                'incident' => $incident?->fresh(['photos']),
                'duty_officer' => $dutyOfficer,
                'resolved_at' => $now,
            ];
        });
    }
}
