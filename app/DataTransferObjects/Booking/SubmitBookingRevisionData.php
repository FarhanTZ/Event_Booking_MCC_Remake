<?php

namespace App\DataTransferObjects\Booking;

use Illuminate\Http\UploadedFile;

readonly class SubmitBookingRevisionData
{
    public function __construct(
        public string $bookingId,
        public string $userId,
        public string $picName,
        public string $picPhone,
        public ?string $picEmergencyPhone = null,
        public ?string $officialLetterNumber = null,
        public string $eventName = '',
        public string $eventCategory = '',
        public string $ekrafSubsector = '',
        public string $eventType = 'Terbuka Untuk Umum',
        public int $estimatedParticipants = 1,
        public string $eventDescription = '',
        public ?string $equipmentNeeded = null,
        public ?UploadedFile $proposalFile = null,
        public ?UploadedFile $posterFile = null,
        public ?string $revisionNotes = null,
    ) {}
}

