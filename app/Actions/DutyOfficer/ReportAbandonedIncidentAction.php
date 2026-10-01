<?php

namespace App\Actions\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ReportAbandonedIncidentData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\IncidentPhoto;
use App\Models\RoomIncident;
use App\Models\User;
use App\Services\FileUploadSecurityService;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ReportAbandonedIncidentAction
{
    /**
     * Report field incident for abandoned event and trigger user suspension (FR-DO-07 Opsi B).
     *
     * @param ReportAbandonedIncidentData $dto
     * @return array{
     *   incident: RoomIncident,
     *   booking: Booking,
     *   duty_officer: User,
     *   user_suspended: bool,
     *   photos_count: int
     * }
     * @throws ValidationException
     */
    public function execute(ReportAbandonedIncidentData $dto): array
    {
        $booking = Booking::with(['slots.room', 'eventDetail', 'latestKtpVaultLog', 'user'])->findOrFail($dto->bookingId);

        if (! $booking->isCheckedIn() && ! $booking->isAbandoned() && ! $booking->isCompleted()) {
            throw ValidationException::withMessages([
                'booking' => 'Pelaporan insiden kasus ditinggal hanya dapat diproses pada permohonan yang aktif atau dalam penanganan anomali.',
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

            // 2. Simpan foto bukti kamera lapangan ke storage publik secara aman
            $savedPhotos = [];
            foreach ($dto->photos as $photoFile) {
                $storedPath = FileUploadSecurityService::storeImage($photoFile, 'incidents');
                $photoModel = IncidentPhoto::create([
                    'incident_id' => $incident->id,
                    'photo_url' => $storedPath,
                    'caption' => "Foto bukti anomali ditinggal DO: {$dto->itemName}",
                    'created_at' => $now,
                ]);
                $savedPhotos[] = $photoModel;
            }

            // 3. Update status booking menjadi ABANDONED_INCIDENT dan clearance_status = 'incident_detected'
            $booking->status = BookingStatus::ABANDONED_INCIDENT;
            $booking->clearance_status = 'incident_detected';
            $booking->completed_at = $booking->completed_at ?? $now;
            $booking->completed_by = $dutyOfficer->id;
            $booking->assigned_checkout_do_id = $booking->assigned_checkout_do_id ?? $dutyOfficer->id;
            $booking->checkout_duty_officer_id = $dutyOfficer->id;
            $booking->checkout_do_notes = "Insiden Ditinggal: {$dto->itemName} - {$dto->description}";
            $booking->save();

            if ($dto->slotId) {
                BookingSlot::where('id', $dto->slotId)->update([
                    'assigned_checkout_do_id' => $dutyOfficer->id,
                    'checkout_duty_officer_id' => $dutyOfficer->id,
                ]);
            }

            // 4. Otomatis tangguhkan / bekukan akun pemohon (User Suspension Trigger)
            $user = $booking->user;
            $userSuspended = false;

            if ($user) {
                $suspensionReason = "Meninggalkan gedung tanpa lapor pasca-acara dengan catatan insiden ruangan ({$dto->itemName}) pada booking {$booking->booking_code}.";
                $user->update([
                    'is_suspended' => true,
                    'suspension_reason' => $suspensionReason,
                    'suspended_at' => $now,
                ]);
                $userSuspended = true;

                SecurityAuditLogger::logAction(
                    action: 'user.auto_suspended_abandoned_incident',
                    description: "Akun user {$user->email} otomatis dibekukan karena meninggalkan gedung tanpa lapor dengan insiden ruangan ({$booking->booking_code}).",
                    userId: $dutyOfficer->id,
                    modelType: 'User',
                    modelId: (string) $user->id,
                    metadata: [
                        'booking_code' => $booking->booking_code,
                        'incident_id' => $incident->id,
                        'category' => $dto->category->value,
                        'item_name' => $dto->itemName,
                        'suspension_reason' => $suspensionReason,
                        'suspended_at' => $now->toIso8601String(),
                    ]
                );
            }

            // 5. Audit Log untuk Pelaporan Insiden Ditinggal
            SecurityAuditLogger::logAction(
                action: 'booking.abandoned_incident_reported',
                description: "DO {$dutyOfficer->full_name} melaporkan insiden ruangan kasus panitia pulang tanpa lapor pada booking {$booking->booking_code} (Status: abandoned_incident).",
                userId: $dutyOfficer->id,
                modelType: 'Booking',
                modelId: (string) $booking->id,
                metadata: [
                    'booking_code' => $booking->booking_code,
                    'incident_id' => $incident->id,
                    'category' => $dto->category->value,
                    'item_name' => $dto->itemName,
                    'photos_count' => count($savedPhotos),
                    'user_suspended' => $userSuspended,
                    'reported_at' => $now->toIso8601String(),
                ]
            );

            Log::channel('single')->alert("Security Alert [ABANDONED_INCIDENT_USER_SUSPENDED]: DO {$dutyOfficer->full_name} reported incident for abandoned booking {$booking->booking_code}. User account {$user?->email} suspended!", [
                'incident_id' => $incident->id,
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'user_id' => $user?->id,
                'user_suspended' => $userSuspended,
            ]);

            return [
                'incident' => $incident->load('photos'),
                'booking' => $booking->fresh(['latestIncident.photos', 'checkoutDutyOfficer', 'dutyOfficer', 'user']),
                'duty_officer' => $dutyOfficer,
                'user_suspended' => $userSuspended,
                'photos_count' => count($savedPhotos),
            ];
        });
    }
}
