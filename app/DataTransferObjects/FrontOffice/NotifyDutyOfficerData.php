<?php

namespace App\DataTransferObjects\FrontOffice;

use Illuminate\Http\Request;

readonly class NotifyDutyOfficerData
{
    public function __construct(
        public string $bookingId,
        public ?int $slotId,
        public string $officerId,
        public string $channel = 'whatsapp_web'
    ) {}

    public static function fromRequest(Request $request, string $officerId): self
    {
        return new self(
            bookingId: (string) $request->input('booking_id'),
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
            officerId: $officerId,
            channel: (string) $request->input('channel', 'whatsapp_web')
        );
    }
}
