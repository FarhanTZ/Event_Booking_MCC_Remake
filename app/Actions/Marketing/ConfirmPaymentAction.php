<?php

namespace App\Actions\Marketing;

use App\DataTransferObjects\Marketing\ConfirmPaymentData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ConfirmPaymentAction
{
    /**
     * Execute the commercial payment verification and confirmation use case (REQ-MKT-10).
     *
     * @param ConfirmPaymentData $data
     * @return Booking
     * @throws InvalidArgumentException
     */
    public function execute(ConfirmPaymentData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            /** @var Booking $booking */
            $booking = Booking::query()->lockForUpdate()->findOrFail($data->bookingId);

            // Invariant: Only bookings in awaiting_payment can be confirmed for payment
            $currentStatus = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;
            if ($currentStatus !== BookingStatus::AWAITING_PAYMENT->value) {
                throw new InvalidArgumentException("Permohonan dengan status '{$currentStatus}' tidak dapat diproses verifikasi pembayaran.");
            }

            // Generate unique 64-character checkin token for E-Pass Digital
            $checkinToken = $this->generateUniqueCheckinToken();
            $now = Carbon::now();

            // Update status permohonan menjadi confirmed dan catat timestamp bayar serta kurator penyetuju
            $booking->update([
                'status' => BookingStatus::CONFIRMED,
                'paid_at' => $now,
                'confirmed_by' => $data->marketingUserId,
                'checkin_token' => $checkinToken,
                'payment_notes' => trim(($booking->payment_notes ? $booking->payment_notes . "\n" : '') . "[Kuitansi/STS: {$data->paymentReference}] " . ($data->paymentNotes ?? '')),
                'version' => $booking->version + 1,
            ]);

            // Pastikan seluruh slot jadwal tetap aman terikat di database (is_active = true)
            $booking->slots()->update(['is_active' => true]);

            // Catat log keputusan verifikasi pembayaran ke booking_approvals
            $approvalNotes = "Pembayaran retribusi daerah berhasil divalidasi. No. Ref/STS: {$data->paymentReference}." . ($data->paymentNotes ? " Catatan: {$data->paymentNotes}" : '');
            BookingApproval::query()->create([
                'booking_id' => $booking->id,
                'action_by' => $data->marketingUserId,
                'decision' => 'payment_verified',
                'notes' => $approvalNotes,
                'created_at' => $now,
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
