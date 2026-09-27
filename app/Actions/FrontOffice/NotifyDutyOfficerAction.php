<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\NotifyDutyOfficerData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Services\FrontOffice\DutyOfficerNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NotifyDutyOfficerAction
{
    public function __construct(
        private DutyOfficerNotificationService $notificationService
    ) {}

    /**
     * Mark and record Duty Officer notification as dispatched and return WhatsApp URL & message (FR-FO-05).
     *
     * @param NotifyDutyOfficerData $dto
     * @return array{booking: Booking, duty_officer: \App\Models\User, message: string, whatsapp_url: string, notified_at: Carbon}
     * @throws ValidationException
     */
    public function execute(NotifyDutyOfficerData $dto): array
    {
        $booking = Booking::with(['dutyOfficer', 'eventDetail', 'slots.room', 'slots.dutyOfficer', 'latestKtpVaultLog', 'user'])
            ->findOrFail($dto->bookingId);

        $targetSlot = null;
        if ($dto->slotId) {
            $targetSlot = $booking->slots->firstWhere('id', $dto->slotId);
        }
        if (! $targetSlot) {
            $targetSlot = $booking->slots->sortBy('event_start_at')->first();
        }

        $dutyOfficer = $targetSlot?->dutyOfficer ?? $booking->dutyOfficer;

        if (! $dutyOfficer) {
            throw ValidationException::withMessages([
                'duty_officer' => 'Permohonan ini belum memiliki penugasan Duty Officer (DO). Silakan disposisikan DO terlebih dahulu.',
            ]);
        }

        if (empty($dutyOfficer->phone_number)) {
            throw ValidationException::withMessages([
                'phone_number' => "Nomor telepon/WhatsApp staf {$dutyOfficer->full_name} belum terdaftar di sistem.",
            ]);
        }

        $message = $this->notificationService->generateWhatsAppMessage($booking, $dutyOfficer, $targetSlot);
        $whatsappUrl = $this->notificationService->generateWhatsAppUrl($dutyOfficer->phone_number, $message);

        return DB::transaction(function () use ($booking, $targetSlot, $dutyOfficer, $dto, $message, $whatsappUrl) {
            $now = Carbon::now();

            // 1. Catat status notifikasi tugas terkirim di level booking
            $booking->update([
                'do_notified_at' => $now,
            ]);

            // 2. Catat status notifikasi di level slot sesi ruangan
            $query = BookingSlot::where('booking_id', $booking->id);
            if ($dto->slotId) {
                $query->where('id', $dto->slotId);
            }
            $query->update([
                'do_notified_at' => $now,
                'do_notified_by' => $dto->officerId,
                'do_notified_channel' => $dto->channel,
            ]);

            Log::channel('single')->info("Operational Dispatch [NOTIFY_DUTY_OFFICER_WHATSAPP]: Notification recorded for DO {$dutyOfficer->full_name} ({$dutyOfficer->phone_number}) on booking {$booking->booking_code}.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'duty_officer_id' => $dutyOfficer->id,
                'duty_officer_name' => $dutyOfficer->full_name,
                'phone_number' => $dutyOfficer->phone_number,
                'notified_at' => $now->toIso8601String(),
                'notified_by' => $dto->officerId,
                'channel' => $dto->channel,
                'message_preview' => $message,
            ]);

            return [
                'booking' => $booking->fresh(['dutyOfficer', 'slots.dutyOfficer']),
                'duty_officer' => $dutyOfficer,
                'message' => $message,
                'whatsapp_url' => $whatsappUrl,
                'notified_at' => $now,
            ];
        });
    }
}
