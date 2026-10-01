<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ReportRoomIncidentData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\IncidentPhoto;
use App\Models\RoomIncident;
use App\Models\User;
use App\Services\FileUploadSecurityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReportRoomIncidentAction
{
    /**
     * Report field room incident and transmit Red Signal (clearance_status = 'incident_detected') (FR-DO-05).
     *
     * @param ReportRoomIncidentData $dto
     * @return array{incident: RoomIncident, booking: Booking, duty_officer: User, photos_count: int}
     * @throws ValidationException
     */
    public function execute(ReportRoomIncidentData $dto): array
    {
        $booking = Booking::with(['slots.room', 'eventDetail', 'latestKtpVaultLog'])->findOrFail($dto->bookingId);

        if (! $booking->isCheckedIn() && ! $booking->isCompleted()) {
            throw ValidationException::withMessages([
                'booking' => 'Pelaporan insiden ruangan hanya dapat dilakukan pada acara yang sedang berlangsung (Checked-In).',
            ]);
        }

        $dutyOfficer = User::findOrFail($dto->dutyOfficerId);

        return DB::transaction(function () use ($booking, $dutyOfficer, $dto) {
            $now = Carbon::now();

            // 1. Simpan laporan insiden ke tabel room_incidents
            $incident = RoomIncident::create([
                'booking_id' => $booking->id,
                'reported_by_do' => $dutyOfficer->id,
                'category' => $dto->category->value,
                'item_name' => $dto->itemName,
                'description' => $dto->description,
                'is_resolved' => false,
                'created_at' => $now,
            ]);

            // 2. Simpan foto bukti kamera lapangan ke storage publik secara aman & catat ke incident_photos
            $savedPhotos = [];
            foreach ($dto->photos as $photoFile) {
                $storedPath = FileUploadSecurityService::storeImage($photoFile, 'incidents');
                $photoModel = IncidentPhoto::create([
                    'incident_id' => $incident->id,
                    'photo_url' => $storedPath,
                    'caption' => "Foto bukti temuan DO: {$dto->itemName}",
                    'created_at' => $now,
                ]);
                $savedPhotos[] = $photoModel;
            }

            // 3. Ubah status kelayakan menjadi sinyal merah: clearance_status = 'incident_detected'
            $booking->update([
                'clearance_status' => 'incident_detected',
                'assigned_checkout_do_id' => $booking->assigned_checkout_do_id ?? $dutyOfficer->id,
                'checkout_duty_officer_id' => $booking->checkout_duty_officer_id ?? $dutyOfficer->id,
                'checkout_do_notes' => "Laporan Insiden: {$dto->itemName} - {$dto->description}",
            ]);

            if ($dto->slotId) {
                BookingSlot::where('id', $dto->slotId)->update([
                    'assigned_checkout_do_id' => $booking->assigned_checkout_do_id ?? $dutyOfficer->id,
                    'checkout_duty_officer_id' => $booking->checkout_duty_officer_id ?? $dutyOfficer->id,
                ]);
            }

            Log::channel('single')->warning("Security & Operation Alert [ROOM_INCIDENT_RED_SIGNAL]: DO {$dutyOfficer->full_name} reported incident for {$booking->booking_code} (Category: {$dto->category->value}, Item: {$dto->itemName}). Front Office KTP release locked!", [
                'incident_id' => $incident->id,
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_name' => $dutyOfficer->full_name,
                'category' => $dto->category->value,
                'item_name' => $dto->itemName,
                'description' => $dto->description,
                'photos_count' => count($savedPhotos),
                'reported_at' => $now->toIso8601String(),
            ]);

            return [
                'incident' => $incident->load('photos'),
                'booking' => $booking->fresh(['latestIncident.photos', 'checkoutDutyOfficer', 'dutyOfficer']),
                'duty_officer' => $dutyOfficer,
                'photos_count' => count($savedPhotos),
            ];
        });
    }
}
