<?php

namespace App\DataTransferObjects\FrontOffice;

readonly class ValidateCheckinData
{
    public function __construct(
        public ?string $bookingCode = null,
        public ?string $checkinToken = null,
        public ?string $officerId = null,
        public bool $isEarlyToleranceConfirmed = false
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request, string $officerId): self
    {
        return new self(
            bookingCode: $request->filled('booking_code') ? trim((string) $request->input('booking_code')) : null,
            checkinToken: $request->filled('checkin_token') ? trim((string) $request->input('checkin_token')) : null,
            officerId: $officerId,
            isEarlyToleranceConfirmed: (bool) $request->boolean('confirm_early', false)
        );
    }
}
