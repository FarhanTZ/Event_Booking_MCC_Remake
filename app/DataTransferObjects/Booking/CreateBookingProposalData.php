<?php

namespace App\DataTransferObjects\Booking;

use Illuminate\Http\UploadedFile;

readonly class CreateBookingProposalData
{
    /**
     * @param int $roomId
     * @param string $date YYYY-MM-DD
     * @param array<string> $slots
     * @param string $userId
     * @param string $eventName
     * @param string $eventCategory
     * @param string $ekrafSubsector
     * @param string $eventType
     * @param string $eventDescription
     * @param string|null $equipmentNeeded
     * @param array<string> $requestedFacilities
     * @param int $estimatedParticipants
     * @param string $picName
     * @param string $picPhone
     * @param string|null $picEmergencyPhone
     * @param string $institutionName
     * @param string|null $officialLetterNumber
     * @param UploadedFile $proposalFile
     * @param UploadedFile|null $posterFile
     * @param bool $tosAgreed
     */
    public function __construct(
        public int $roomId,
        public string $date,
        public array $slots,
        public string $userId,
        public string $eventName,
        public string $eventCategory,
        public string $ekrafSubsector,
        public string $eventType,
        public string $eventDescription,
        public ?string $equipmentNeeded,
        public array $requestedFacilities,
        public int $estimatedParticipants,
        public string $picName,
        public string $picPhone,
        public ?string $picEmergencyPhone,
        public string $institutionName,
        public ?string $officialLetterNumber,
        public UploadedFile $proposalFile,
        public ?UploadedFile $posterFile,
        public bool $tosAgreed
    ) {}
}
