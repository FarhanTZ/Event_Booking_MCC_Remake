<?php

namespace App\Actions\Marketing;

use App\DataTransferObjects\Marketing\ReviseBookingData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ReviseBookingAction
{
    /**
     * Execute the revision request use case for a booking proposal (REQ-MKT-08).
     *
     * @param ReviseBookingData $data
     * @return Booking
     * @throws InvalidArgumentException
     */
    public function execute(ReviseBookingData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            /** @var Booking $booking */
            $booking = Booking::query()->lockForUpdate()->findOrFail($data->bookingId);

            // Invariant: Only bookings in awaiting_marketing can be marked for revision
            $currentStatus = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;
            if ($currentStatus !== BookingStatus::AWAITING_MARKETING->value) {
                throw new InvalidArgumentException("Permohonan dengan status '{$currentStatus}' tidak dapat diminta revisi.");
            }

            // Penetapan tenggat waktu 24 jam (REQ-MKT-08)
            $revisionDeadline = Carbon::now()->addHours($data->gracePeriodHours);

            $booking->update([
                'status' => BookingStatus::REVISED,
                'revision_deadline' => $revisionDeadline,
                'version' => $booking->version + 1,
            ]);

            // Mempertahankan seluruh slot jadwal multi-ruangan di database agar tidak direbut orang lain
            $booking->slots()->update(['is_active' => true]);

            // Catat log evaluasi revisi ke booking_approvals
            BookingApproval::query()->create([
                'booking_id' => $booking->id,
                'action_by' => $data->marketingUserId,
                'decision' => 'revised',
                'notes' => $data->revisionNotes,
                'created_at' => Carbon::now(),
            ]);

            return $booking->fresh(['slots.room', 'eventDetail', 'user', 'approvals']);
        });
    }
}
