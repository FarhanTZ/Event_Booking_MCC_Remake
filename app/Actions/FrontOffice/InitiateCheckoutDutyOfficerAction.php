<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\InitiateCheckoutDutyOfficerData;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;
use App\Services\FrontOffice\DutyOfficerNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InitiateCheckoutDutyOfficerAction
{
    public function __construct(
        private DutyOfficerNotificationService $notificationService
    ) {}

    /**
     * Initiate checkout inspection and assign active shift Duty Officer (FR-FO-06).
     *
     * @param InitiateCheckoutDutyOfficerData $dto
     * @return array{booking: Booking, duty_officer: User, message: string, whatsapp_url: string, assigned_at: Carbon, notified_at: ?Carbon}
     * @throws ValidationException
     */
    public function execute(InitiateCheckoutDutyOfficerData $dto): array
    {
        $booking = Booking::with([
            'slots.room',
            'slots.dutyOfficer',
            'slots.checkoutDutyOfficer',
            'eventDetail',
            'dutyOfficer',
            'latestKtpVaultLog',
            'user'
        ])->findOrFail($dto->bookingId);

        if (! $booking->canInitiateCheckout()) {
            throw ValidationException::withMessages([
                'booking' => 'Inisiasi checkout hanya dapat dilakukan pada acara yang sedang berlangsung (Checked-In).',
            ]);
        }

        $dutyOfficer = User::where('id', $dto->assignedCheckoutDoId)
            ->where('role', UserRole::DUTY_OFFICER)
            ->first();

        if (! $dutyOfficer) {
            throw ValidationException::withMessages([
                'assigned_checkout_do_id' => 'Staf yang dipilih bukan merupakan Duty Officer aktif yang berwenang pada shift berjalan.',
            ]);
        }

        $targetSlot = null;
        if ($dto->slotId) {
            $targetSlot = $booking->slots->firstWhere('id', $dto->slotId);
        }
        if (! $targetSlot) {
            $targetSlot = $booking->slots->sortBy('event_start_at')->first();
        }

        $message = $this->notificationService->generateCheckoutWhatsAppMessage($booking, $dutyOfficer, $targetSlot);
        $whatsappUrl = ! empty($dutyOfficer->phone_number)
            ? $this->notificationService->generateWhatsAppUrl($dutyOfficer->phone_number, $message)
            : '';

        return DB::transaction(function () use ($booking, $targetSlot, $dutyOfficer, $dto, $message, $whatsappUrl) {
            $now = Carbon::now();
            $notifiedAt = $dto->sendWhatsApp ? $now : null;

            // 1. Update Booking level checkout DO
            $booking->update([
                'assigned_checkout_do_id' => $dutyOfficer->id,
                'checkout_duty_officer_id' => $dutyOfficer->id,
                'checkout_do_assigned_at' => $now,
                'checkout_initiated_at' => $now,
                'checkout_do_assigned_by' => $dto->assignedById,
                'checkout_initiated_by' => $dto->assignedById,
                'checkout_do_notes' => $dto->notes,
                'checkout_do_notified_at' => $notifiedAt,
            ]);

            // 2. Update BookingSlot level checkout DO
            $query = BookingSlot::where('booking_id', $booking->id);
            if ($dto->slotId) {
                $query->where('id', $dto->slotId);
            }

            $query->update([
                'assigned_checkout_do_id' => $dutyOfficer->id,
                'checkout_duty_officer_id' => $dutyOfficer->id,
                'checkout_do_assigned_at' => $now,
                'checkout_initiated_at' => $now,
                'checkout_do_assigned_by' => $dto->assignedById,
                'checkout_initiated_by' => $dto->assignedById,
                'checkout_do_notes' => $dto->notes,
                'checkout_do_notified_at' => $notifiedAt,
                'checkout_do_notified_by' => $dto->sendWhatsApp ? $dto->assignedById : null,
                'checkout_do_notified_channel' => $dto->sendWhatsApp ? $dto->channel : null,
            ]);

            Log::channel('single')->info("Security & Operation Audit [CHECKOUT_INSPECTION_INITIATED]: DO {$dutyOfficer->full_name} assigned for checkout inspection on {$booking->booking_code}.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'assigned_checkout_do_id' => $dutyOfficer->id,
                'assigned_checkout_do_name' => $dutyOfficer->full_name,
                'phone_number' => $dutyOfficer->phone_number,
                'assigned_by' => $dto->assignedById,
                'assigned_at' => $now->toIso8601String(),
                'notes' => $dto->notes,
                'send_whatsapp' => $dto->sendWhatsApp,
                'message_preview' => $message,
            ]);

            return [
                'booking' => $booking->fresh(['checkoutDutyOfficer', 'dutyOfficer', 'slots.checkoutDutyOfficer', 'slots.room']),
                'duty_officer' => $dutyOfficer,
                'message' => $message,
                'whatsapp_url' => $whatsappUrl,
                'assigned_at' => $now,
                'notified_at' => $notifiedAt,
            ];
        });
    }
}
