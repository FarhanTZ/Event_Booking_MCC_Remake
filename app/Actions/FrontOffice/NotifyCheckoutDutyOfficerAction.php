<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\NotifyCheckoutDutyOfficerData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Services\FrontOffice\DutyOfficerNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class NotifyCheckoutDutyOfficerAction
{
    public function __construct(
        private DutyOfficerNotificationService $notificationService
    ) {}

    /**
     * Dispatch WhatsApp checkout inspection task notification to Duty Officer (FR-FO-06).
     *
     * @param NotifyCheckoutDutyOfficerData $dto
     * @return array{booking: Booking, duty_officer: \App\Models\User, message: string, whatsapp_url: string, notified_at: Carbon}
     * @throws ValidationException
     */
    public function execute(NotifyCheckoutDutyOfficerData $dto): array
    {
        $booking = Booking::with([
            'checkoutDutyOfficer',
            'dutyOfficer',
            'eventDetail',
            'slots.room',
            'slots.checkoutDutyOfficer',
            'slots.dutyOfficer',
            'latestKtpVaultLog',
            'user'
        ])->findOrFail($dto->bookingId);

        $targetSlot = null;
        if ($dto->slotId) {
            $targetSlot = $booking->slots->firstWhere('id', $dto->slotId);
        }
        if (! $targetSlot) {
            $targetSlot = $booking->slots->sortBy('event_start_at')->first();
        }

        $dutyOfficer = $targetSlot?->checkoutDutyOfficer 
            ?? $booking->checkoutDutyOfficer 
            ?? $targetSlot?->dutyOfficer 
            ?? $booking->dutyOfficer;

        if (! $dutyOfficer) {
            throw ValidationException::withMessages([
                'duty_officer' => 'Permohonan ini belum memiliki penugasan Duty Officer checkout. Silakan tentukan DO terlebih dahulu.',
            ]);
        }

        if (empty($dutyOfficer->phone_number)) {
            throw ValidationException::withMessages([
                'phone_number' => "Nomor telepon/WhatsApp staf {$dutyOfficer->full_name} belum terdaftar di sistem.",
            ]);
        }

        $message = $this->notificationService->generateCheckoutWhatsAppMessage($booking, $dutyOfficer, $targetSlot);
        $whatsappUrl = $this->notificationService->generateWhatsAppUrl($dutyOfficer->phone_number, $message);

        return DB::transaction(function () use ($booking, $targetSlot, $dutyOfficer, $dto, $message, $whatsappUrl) {
            $now = Carbon::now();

            // 1. Update Booking level checkout DO notified status
            $booking->update([
                'assigned_checkout_do_id' => $dutyOfficer->id,
                'checkout_duty_officer_id' => $dutyOfficer->id,
                'checkout_do_notified_at' => $now,
            ]);

            // 2. Update BookingSlot level checkout DO notified status
            $query = BookingSlot::where('booking_id', $booking->id);
            if ($dto->slotId) {
                $query->where('id', $dto->slotId);
            }
            $query->update([
                'assigned_checkout_do_id' => $dutyOfficer->id,
                'checkout_duty_officer_id' => $dutyOfficer->id,
                'checkout_do_notified_at' => $now,
                'checkout_do_notified_by' => $dto->officerId,
                'checkout_do_notified_channel' => $dto->channel,
            ]);

            Log::channel('single')->info("Operational Dispatch [NOTIFY_CHECKOUT_DUTY_OFFICER_WHATSAPP]: Checkout task notification dispatched for DO {$dutyOfficer->full_name} on booking {$booking->booking_code}.", [
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
                'booking' => $booking->fresh(['checkoutDutyOfficer', 'slots.checkoutDutyOfficer']),
                'duty_officer' => $dutyOfficer,
                'message' => $message,
                'whatsapp_url' => $whatsappUrl,
                'notified_at' => $now,
            ];
        });
    }
}
