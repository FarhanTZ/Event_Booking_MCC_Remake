<?php

namespace App\Services\FrontOffice;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\User;

class DutyOfficerNotificationService
{
    /**
     * Generate structured WhatsApp dispatch message text for Duty Officer (FR-FO-05).
     *
     * Format:
     * "Halo [Nama DO], acara [Nama Event] di [Nama Ruangan - Lantai X] telah CHECK-IN. PIC: [Nama PIC] ([Nomor WA]). Jadwal: [Jam Mulai - Jam Selesai]. Mohon lakukan pendampingan pembukaan ruangan dan bersiap untuk inspeksi fisik saat acara berakhir."
     */
    public function generateWhatsAppMessage(Booking $booking, User $dutyOfficer, ?BookingSlot $slot = null): string
    {
        $targetSlot = $slot ?? $booking->slots->sortBy('event_start_at')->first();
        $room = $targetSlot?->room;
        $eventDetail = $booking->eventDetail;

        $doName = $dutyOfficer->full_name;
        $eventName = $eventDetail?->event_name ?? 'Acara MCC';
        $roomName = $room ? "{$room->name} - Lantai {$room->floor_level}" : 'Ruangan Gedung MCC';
        
        $picName = $eventDetail?->pic_name_snapshot ?? ($booking->user->full_name ?? 'PIC Acara');
        $picPhone = $booking->latestKtpVaultLog?->pic_whatsapp_number 
                    ?? ($eventDetail?->pic_phone_snapshot ?? ($booking->user->phone_number ?? '-'));

        $startTime = $targetSlot?->event_start_at ? $targetSlot->event_start_at->format('H:i') : '-';
        $endTime = $targetSlot?->event_end_at ? $targetSlot->event_end_at->format('H:i') . ' WIB' : '-';
        $schedule = "{$startTime} - {$endTime}";

        $message = "Halo {$doName}, acara {$eventName} di {$roomName} telah CHECK-IN. PIC: {$picName} ({$picPhone}). Jadwal: {$schedule}. Mohon lakukan pendampingan pembukaan ruangan dan bersiap untuk inspeksi fisik saat acara berakhir.";

        return $message;
    }

    /**
     * Generate structured WhatsApp checkout inspection task message text for Duty Officer (FR-FO-06).
     *
     * Format:
     * "Halo [Nama DO], acara [Nama Event] di [Nama Ruangan - Lantai X] telah selesai dan panitia mengajukan checkout. Mohon segera inspeksi fisik ruangan (kebersihan & inventaris fasilitas) serta input sinyal clearance di sistem."
     */
    public function generateCheckoutWhatsAppMessage(Booking $booking, User $dutyOfficer, ?BookingSlot $slot = null): string
    {
        $targetSlot = $slot ?? $booking->slots->sortBy('event_start_at')->first();
        $room = $targetSlot?->room;
        $eventDetail = $booking->eventDetail;

        $doName = $dutyOfficer->full_name;
        $eventName = $eventDetail?->event_name ?? 'Acara MCC';
        $roomName = $room ? "{$room->name} - Lantai {$room->floor_level}" : 'Ruangan Gedung MCC';

        $message = "Halo {$doName}, acara {$eventName} di {$roomName} telah selesai dan panitia mengajukan checkout. Mohon segera inspeksi fisik ruangan (kebersihan & inventaris fasilitas) serta input sinyal clearance di sistem.";

        return $message;
    }

    /**
     * Generate Click-to-Chat WhatsApp URL (wa.me) with sanitized phone number and encoded message.
     */
    public function generateWhatsAppUrl(string $phoneNumber, string $message): string
    {
        // Normalisasi nomor telepon: Ubah 08xxx atau +628xxx menjadi 628xxx
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $encodedText = urlencode($message);

        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encodedText}";
    }
}
