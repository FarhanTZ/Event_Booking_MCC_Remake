<?php

namespace App\DataTransferObjects\FrontOffice;

use Illuminate\Http\Request;

readonly class InitiateCheckoutDutyOfficerData
{
    public function __construct(
        public string $bookingId,
        public ?int $slotId,
        public string $assignedCheckoutDoId,
        public string $assignedById,
        public ?string $notes = null,
        public bool $sendWhatsApp = true,
        public string $channel = 'whatsapp_web'
    ) {}

    public static function fromRequest(Request $request, string $assignedById): self
    {
        return new self(
            bookingId: (string) $request->input('booking_id'),
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
            assignedCheckoutDoId: (string) ($request->input('assigned_checkout_do_id') ?? $request->input('duty_officer_id')),
            assignedById: $assignedById,
            notes: $request->filled('notes') ? trim((string) $request->input('notes')) : null,
            sendWhatsApp: filter_var($request->input('send_whatsapp', true), FILTER_VALIDATE_BOOLEAN),
            channel: (string) $request->input('channel', 'whatsapp_web')
        );
    }
}
