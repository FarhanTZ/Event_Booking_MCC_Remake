<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\EmergencyCancelBookingData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\BookingSlot;
use Illuminate\Support\Facades\DB;

class EmergencyCancelBookingAction
{
    /**
     * Execute Super Override emergency booking cancellation (FR-ADM-05).
     *
     * @param Booking $booking
     * @param EmergencyCancelBookingData $dto
     * @return Booking
     */
    public function execute(Booking $booking, EmergencyCancelBookingData $dto): Booking
    {
        return DB::transaction(function () use ($booking, $dto) {
            // 1. Update Booking status to CANCELLED
            $booking->update([
                'status' => BookingStatus::CANCELLED,
            ]);

            // 2. Immediately release all booking slots (is_active = false)
            BookingSlot::where('booking_id', $booking->id)->update([
                'is_active' => false,
            ]);

            // 3. Category label mapping
            $categoryLabels = [
                'agenda_pemerintah_darurat' => 'Agenda Mendadak Forkopimda / Pemkot Malang',
                'kerusakan_teknis_gedung' => 'Kerusakan Teknis / Struktural Gedung Darurat',
                'kondisi_kahar_force_majeure' => 'Kondisi Kahar (Force Majeure)',
                'pelanggaran_berat_keamanan' => 'Pelanggaran Berat Protokol Keamanan',
                'lainnya' => 'Intervensi Khusus Super Admin',
            ];
            $categoryLabel = $categoryLabels[$dto->emergencyCategory] ?? $dto->emergencyCategory;

            // 4. Record permanent audit trail
            BookingApproval::create([
                'booking_id' => $booking->id,
                'action_by' => $dto->adminId,
                'decision' => 'emergency_cancelled',
                'notes' => "[SUPER OVERRIDE - {$categoryLabel}] {$dto->cancellationReason}",
                'created_at' => now(),
            ]);

            return $booking->fresh(['slots', 'approvals', 'user', 'eventDetail']);
        });
    }
}
