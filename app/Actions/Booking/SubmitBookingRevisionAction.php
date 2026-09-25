<?php

namespace App\Actions\Booking;

use App\DataTransferObjects\Booking\SubmitBookingRevisionData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SubmitBookingRevisionAction
{
    /**
     * Execute the use case for applicant submitting revised event details & proposal within 24h grace period (REQ-USR-05).
     *
     * @param SubmitBookingRevisionData $data
     * @return Booking
     * @throws InvalidArgumentException
     */
    public function execute(SubmitBookingRevisionData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            /** @var Booking $booking */
            $booking = Booking::query()->lockForUpdate()->findOrFail($data->bookingId);

            $currentStatus = $booking->status instanceof BookingStatus ? $booking->status->value : (string) $booking->status;

            if ($currentStatus !== BookingStatus::REVISED->value) {
                throw new InvalidArgumentException("Permohonan ini tidak dalam status perlu revisi (Status saat ini: {$currentStatus}).");
            }

            // Validasi masa tenggang 24 jam (REQ-USR-05 poin 4)
            if ($booking->revision_deadline && $booking->revision_deadline->isPast()) {
                // Batalkan otomatis jika melewati 24 jam
                $booking->update([
                    'status' => BookingStatus::CANCELLED,
                    'version' => $booking->version + 1,
                ]);

                // Buka kembali slot jadwal untuk publik
                $booking->slots()->update(['is_active' => false]);

                // Catat log pembatalan otomatis
                BookingApproval::query()->create([
                    'booking_id' => $booking->id,
                    'action_by' => $data->userId,
                    'decision' => 'cancelled',
                    'notes' => 'Permohonan dibatalkan otomatis karena melewati batas waktu tenggang revisi 24 jam.',
                    'created_at' => now(),
                ]);

                throw new InvalidArgumentException("Masa tenggang perbaikan 24 jam telah berakhir pada {$booking->revision_deadline->translatedFormat('d M Y H:i')} WIB. Permohonan Anda otomatis dibatalkan dan slot jadwal telah dibuka kembali.");
            }

            // 1. Simpan berkas jika ada penggantian berkas baru
            $updatePayload = [
                'event_name' => $data->eventName,
                'event_category' => $data->eventCategory,
                'ekraf_subsector' => $data->ekrafSubsector,
                'event_type' => $data->eventType,
                'estimated_participants' => $data->estimatedParticipants,
                'event_description' => $data->eventDescription,
                'equipment_needed' => $data->equipmentNeeded,
                'pic_name_snapshot' => $data->picName,
                'pic_phone_snapshot' => $data->picPhone,
                'pic_emergency_phone' => $data->picEmergencyPhone,
                'official_letter_number' => $data->officialLetterNumber,
            ];

            if ($data->proposalFile) {
                $proposalPath = $data->proposalFile->store('proposals', 'public');
                $updatePayload['proposal_file_url'] = '/storage/' . $proposalPath;
            }

            if ($data->posterFile) {
                $posterPath = $data->posterFile->store('posters', 'public');
                $updatePayload['poster_image_url'] = '/storage/' . $posterPath;
            }

            if ($booking->eventDetail) {
                $booking->eventDetail->update($updatePayload);
            }

            // 2. Status kembali menjadi awaiting_marketing & hak slot jadwal tetap aman terikat
            $booking->update([
                'status' => BookingStatus::AWAITING_MARKETING,
                'version' => $booking->version + 1,
            ]);

            $booking->slots()->update(['is_active' => true]);

            // 3. Catat riwayat unggah revisi pada booking_approvals
            $approvalNotes = $data->revisionNotes 
                ? "Pemohon mengirimkan berkas dan rincian perbaikan. Catatan: {$data->revisionNotes}"
                : "Pemohon telah mengirimkan berkas dan rincian perbaikan dalam masa tenggang 24 jam.";

            BookingApproval::query()->create([
                'booking_id' => $booking->id,
                'action_by' => $data->userId,
                'decision' => 'revision_submitted',
                'notes' => $approvalNotes,
                'created_at' => now(),
            ]);

            return $booking->fresh(['eventDetail', 'slots.room', 'user', 'approvals']);
        });
    }
}
