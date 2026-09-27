<?php

namespace App\DataTransferObjects\FrontOffice;

use Illuminate\Http\Request;

readonly class ValidateCheckinData
{
    public function __construct(
        public ?string $bookingCode = null,
        public ?string $checkinToken = null,
        public ?string $officerId = null,
        public bool $isEarlyToleranceConfirmed = false,
        public ?string $ktpHolderName = null,
        public ?string $picWhatsappNumber = null,
        public ?string $notes = null
    ) {}

    public static function fromRequest(Request $request, string $officerId): self
    {
        return new self(
            bookingCode: $request->filled('booking_code') ? trim((string) $request->input('booking_code')) : null,
            checkinToken: $request->filled('checkin_token') ? trim((string) $request->input('checkin_token')) : null,
            officerId: $officerId,
            isEarlyToleranceConfirmed: (bool) $request->boolean('confirm_early', false),
            ktpHolderName: $request->filled('ktp_holder_name') ? trim((string) $request->input('ktp_holder_name')) : null,
            picWhatsappNumber: $request->filled('pic_whatsapp_number') ? trim((string) $request->input('pic_whatsapp_number')) : null,
            notes: $request->filled('notes') ? trim((string) $request->input('notes')) : null
        );
    }
}
