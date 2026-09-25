<?php

namespace App\Actions\Marketing;

use App\DataTransferObjects\Marketing\RejectBookingData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RejectBookingAction
{
    /**
     * Execute the permanent rejection use case for a booking proposal (REQ-MKT-09).
     *
     * @param RejectBookingData $data
     * @return Booking
     * @throws InvalidArgumentException
     */
    public function execute(RejectBookingData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            /** @var Booking $booking */
            $booking = Booking::query()->lockForUpdate()->findOrFail($data->bookingId);

            // Invariant: Only bookings in awaiting_marketing or revised can be rejected
            $currentStatus = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;
            if (! in_array($currentStatus, [BookingStatus::AWAITING_MARKETING->value, BookingStatus::REVISED->value], true)) {
                throw new InvalidArgumentException("Permohonan dengan status '{$currentStatus}' tidak dapat diproses penolakan.");
            }

            // Update status permohonan menjadi rejected
            $booking->update([
                'status' => BookingStatus::REJECTED,
                'version' => $booking->version + 1,
            ]);

            // Pelepasan Slot Otomatis: Seketika menonaktifkan seluruh baris slot terkait pada booking_slots (is_active = false)
            // sehingga jadwal-jadwal tersebut langsung terbuka kembali di kalender publik (REQ-MKT-09)
            $booking->slots()->update(['is_active' => false]);

            // Format catatan evaluasi penolakan lengkap dengan kategori
            $formattedNotes = "[Kategori: {$data->rejectionCategory}] {$data->rejectionNotes}";

            // Catat log keputusan penolakan ke booking_approvals
            BookingApproval::query()->create([
                'booking_id' => $booking->id,
                'action_by' => $data->marketingUserId,
                'decision' => 'rejected',
                'notes' => $formattedNotes,
                'created_at' => Carbon::now(),
            ]);

            return $booking->fresh(['slots.room', 'eventDetail', 'user', 'approvals']);
        });
    }
}
