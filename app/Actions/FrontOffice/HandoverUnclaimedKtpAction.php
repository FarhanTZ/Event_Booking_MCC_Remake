<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\HandoverUnclaimedKtpData;
use App\Models\KtpVaultLog;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HandoverUnclaimedKtpAction
{
    public function __construct(
        protected SecurityAuditLogger $auditLogger
    ) {}

    /**
     * Execute late retrieval handover of unclaimed KTP card (FR-FO-09).
     *
     * @param HandoverUnclaimedKtpData $dto
     * @return array{ktp_log: KtpVaultLog, booking: \App\Models\Booking|null, was_unsuspended: bool, retrieved_by: string}
     * @throws ValidationException
     */
    public function execute(HandoverUnclaimedKtpData $dto): array
    {
        return DB::transaction(function () use ($dto) {
            /** @var KtpVaultLog|null $ktpLog */
            $ktpLog = KtpVaultLog::query()
                ->where('id', $dto->ktpVaultLogId)
                ->with(['booking.user', 'booking.eventDetail'])
                ->lockForUpdate()
                ->first();

            if (! $ktpLog) {
                throw ValidationException::withMessages([
                    'ktp_vault_log' => ['Data log KTP tidak ditemukan dalam brankas inventaris.'],
                ]);
            }

            if ($ktpLog->returned_at !== null) {
                throw ValidationException::withMessages([
                    'returned_at' => ['KTP fisik ini sudah diserahkan sebelumnya pada ' . $ktpLog->returned_at->format('d/m/Y H:i') . ' WIB.'],
                ]);
            }

            $now = Carbon::now();
            $booking = $ktpLog->booking;
            $user = $booking?->user;

            // 1. Update KTP Vault Log
            $ktpLog->returned_by_fo = $dto->handledByFoId;
            $ktpLog->returned_at = $now;

            $handoverDetail = "Serah terima susulan kepada {$dto->retrievedByName}" . ($dto->retrievedByNik ? " (NIK: {$dto->retrievedByNik})" : "");
            if ($dto->notes) {
                $handoverDetail .= " - Catatan: {$dto->notes}";
            }

            $ktpLog->notes = $ktpLog->notes 
                ? $ktpLog->notes . ' | [' . $handoverDetail . ']'
                : '[' . $handoverDetail . ']';

            $ktpLog->save();

            // 2. Unsuspend User if requested
            $wasUnsuspended = false;
            if ($dto->unsuspendUser && $user && $user->is_suspended) {
                $user->update([
                    'is_suspended' => false,
                    'suspension_reason' => null,
                    'suspended_at' => null,
                ]);
                $wasUnsuspended = true;

                $this->auditLogger->logAction(
                    action: 'user.unsuspended_ktp_retrieval',
                    description: "Blokir akun user {$user->email} dibuka kembali setelah serah-terima KTP tertinggal diselesaikan.",
                    userId: $dto->handledByFoId,
                    modelType: 'User',
                    modelId: (string) $user->id,
                    metadata: [
                        'retrieved_by' => $dto->retrievedByName,
                        'retrieved_nik' => $dto->retrievedByNik,
                        'unsuspended_at' => $now->toIso8601String(),
                    ]
                );
            }

            // 3. Record Security Audit Log
            $this->auditLogger->logAction(
                action: 'ktp_vault.unclaimed_handover',
                description: "KTP fisik {$ktpLog->ktp_holder_name} (Kotak Arsip: {$ktpLog->unclaimed_box_id}) diserahkan kepada {$dto->retrievedByName}.",
                userId: $dto->handledByFoId,
                modelType: 'KtpVaultLog',
                modelId: (string) $ktpLog->id,
                metadata: [
                    'ktp_holder_name' => $ktpLog->ktp_holder_name,
                    'unclaimed_box_id' => $ktpLog->unclaimed_box_id,
                    'retrieved_by_name' => $dto->retrievedByName,
                    'retrieved_by_nik' => $dto->retrievedByNik,
                    'was_unsuspended' => $wasUnsuspended,
                    'returned_at' => $now->toIso8601String(),
                ]
            );

            return [
                'ktp_log' => $ktpLog->fresh(['booking.user', 'booking.eventDetail', 'returnedBy']),
                'booking' => $booking,
                'was_unsuspended' => $wasUnsuspended,
                'retrieved_by' => $dto->retrievedByName,
            ];
        });
    }
}
