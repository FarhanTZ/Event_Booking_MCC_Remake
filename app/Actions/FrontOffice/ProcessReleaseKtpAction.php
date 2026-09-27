<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\ReleaseKtpData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\KtpVaultLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ProcessReleaseKtpAction
{
    /**
     * Process KTP release and transition booking session to Completed status (FR-FO-07).
     *
     * @param ReleaseKtpData $dto
     * @return array{booking: Booking, vault_log: KtpVaultLog|null, ktp_holder_name: string|null, storage_drawer_number: string|null}
     * @throws ValidationException
     */
    public function execute(ReleaseKtpData $dto): array
    {
        $booking = Booking::with(['latestKtpVaultLog', 'latestIncident.photos', 'slots'])->findOrFail($dto->bookingId);

        // 1. Guard Clause: Status harus Checked-In
        if ($booking->status !== BookingStatus::CHECKED_IN) {
            throw ValidationException::withMessages([
                'clearance' => "Pelepasan KTP hanya dapat dilakukan untuk acara yang sedang berjalan (Checked-In). Status saat ini: {$booking->status->label()}.",
            ]);
        }

        // 2. Guard Clause: Clearance Signal Check
        if ($booking->hasIncident()) {
            throw ValidationException::withMessages([
                'clearance' => 'Pelepasan KTP diblokir! Ditemukan insiden/kerusakan dari DO yang belum diselesaikan (Status: incident_detected).',
            ]);
        }

        if ($booking->isClearancePending()) {
            throw ValidationException::withMessages([
                'clearance' => 'Sinyal kelayakan ruangan (clearance) belum diinput oleh Duty Officer. KTP fisik wajib ditahan sampai DO memberikan sinyal Aman (cleared) atau Selesai (resolved).',
            ]);
        }

        return DB::transaction(function () use ($booking, $dto) {
            $now = Carbon::now();

            // A. Update Status Booking -> COMPLETED
            $booking->update([
                'status' => BookingStatus::COMPLETED,
                'completed_at' => $now,
                'completed_by' => $dto->releasedById,
            ]);

            // B. Update KTP Vault Log (tandai KTP telah dikembalikan)
            $vaultLog = $booking->latestKtpVaultLog;
            if ($vaultLog) {
                $vaultLog->update([
                    'returned_by_fo' => $dto->releasedById,
                    'returned_at' => $now,
                    'notes' => $dto->notes ? ($vaultLog->notes ? $vaultLog->notes . ' | ' . $dto->notes : $dto->notes) : $vaultLog->notes,
                ]);
            }

            Log::channel('single')->info("Security Audit [FRONT_OFFICE_CHECKOUT_COMPLETED]: KTP released and booking {$booking->booking_code} marked COMPLETED.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'clearance_status' => $booking->clearance_status,
                'released_by_fo' => $dto->releasedById,
                'ktp_holder_name' => $vaultLog?->ktp_holder_name,
                'drawer_number' => $vaultLog?->storage_drawer_number,
                'completed_at' => $now->toIso8601String(),
            ]);

            return [
                'booking' => $booking->fresh(['latestKtpVaultLog', 'slots']),
                'vault_log' => $vaultLog?->fresh(),
                'ktp_holder_name' => $vaultLog?->ktp_holder_name,
                'storage_drawer_number' => $vaultLog?->storage_drawer_number,
            ];
        });
    }
}
