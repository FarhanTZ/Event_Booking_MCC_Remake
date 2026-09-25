<?php

namespace App\Actions\Marketing;

use App\DataTransferObjects\Marketing\ApproveBookingData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ApproveBookingAction
{
    /**
     * Execute the approval use case for a booking request (REQ-MKT-07).
     *
     * @param ApproveBookingData $data
     * @return Booking
     * @throws InvalidArgumentException
     */
    public function execute(ApproveBookingData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            /** @var Booking $booking */
            $booking = Booking::query()->lockForUpdate()->findOrFail($data->bookingId);

            // Invariant: Booking must be in awaiting_marketing or revised status
            $currentStatus = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;
            if (! in_array($currentStatus, [BookingStatus::AWAITING_MARKETING->value, BookingStatus::REVISED->value], true)) {
                throw new InvalidArgumentException("Permohonan dengan status '{$currentStatus}' tidak dapat diproses kurasi.");
            }

            if ($data->isSubsidy()) {
                // Skema Subsidi Pemkot Malang (Non-Komersial / Komunitas Ekraf)
                $checkinToken = $this->generateUniqueCheckinToken();

                $booking->update([
                    'pricing_scheme' => 'subsidy',
                    'nominal_amount' => 0.00,
                    'payment_notes' => $data->paymentNotes,
                    'status' => BookingStatus::CONFIRMED,
                    'confirmed_by' => $data->marketingUserId,
                    'checkin_token' => $checkinToken,
                    'version' => $booking->version + 1,
                ]);

                $decisionNotes = $data->approvalNotes 
                    ?: 'Disetujui dengan Skema Subsidi Pemkot Malang (Rp 0.00). E-Pass & QR Code digital diterbitkan.';
            } else {
                // Skema Komersial / Berbayar
                $booking->update([
                    'pricing_scheme' => 'commercial',
                    'nominal_amount' => $data->nominalAmount,
                    'payment_notes' => $data->paymentNotes,
                    'status' => BookingStatus::AWAITING_PAYMENT,
                    'confirmed_by' => null,
                    'checkin_token' => null, // E-Pass ditahan hingga pembayaran divalidasi
                    'version' => $booking->version + 1,
                ]);

                $decisionNotes = $data->approvalNotes 
                    ?: 'Disetujui dengan Skema Komersial. Menunggu pelunasan pembayaran retribusi ke Kas Daerah.';
            }

            // Pastikan seluruh slot jadwal tetap aman terikat di database
            $booking->slots()->update(['is_active' => true]);

            // Catat log approval
            BookingApproval::query()->create([
                'booking_id' => $booking->id,
                'action_by' => $data->marketingUserId,
                'decision' => 'approved',
                'notes' => $decisionNotes,
                'created_at' => now(),
            ]);

            return $booking->fresh(['slots.room', 'eventDetail', 'user', 'approvals']);
        });
    }

    /**
     * Generate unique 64-character token for digital E-Pass & QR Code.
     */
    protected function generateUniqueCheckinToken(): string
    {
        do {
            $token = Str::random(64);
        } while (Booking::query()->where('checkin_token', $token)->exists());

        return $token;
    }
}
