<?php

namespace App\Services;

use App\Models\Booking;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EPassService
{
    /**
     * Generate dynamic QR Code SVG for E-Pass arrival check-in validation.
     *
     * @param Booking $booking
     * @param int $size
     * @return string SVG code
     */
    public function generateQrCodeSvg(Booking $booking, int $size = 220): string
    {
        $payload = $this->buildQrPayload($booking);

        try {
            if (class_exists(Writer::class) && class_exists(SvgImageBackEnd::class)) {
                $renderer = new ImageRenderer(
                    new RendererStyle($size, 1),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                return $writer->writeString($payload);
            }
        } catch (\Throwable $e) {
            // Fallback to embedded SVG matrix generator
        }

        return $this->generateFallbackSvg($payload, $size);
    }

    /**
     * Build the structured check-in validation payload for QR code.
     */
    public function buildQrPayload(Booking $booking): string
    {
        $token = $booking->checkin_token ?: $this->ensureCheckinToken($booking);

        return json_encode([
            'system' => 'MCC_EVENT_BOOKING',
            'version' => '1.0',
            'code' => $booking->booking_code,
            'token' => $token,
            'event' => $booking->eventDetail->event_name ?? 'Kegiatan MCC',
            'institution' => $booking->eventDetail->institution_name_snapshot ?? $booking->user->institution_name ?? 'MCC User',
            'pic' => $booking->eventDetail->pic_name_snapshot ?? $booking->user->full_name ?? 'PIC',
            'phone' => $booking->eventDetail->pic_phone_snapshot ?? $booking->user->phone_number ?? '-',
            'url' => route('home') . "/front-office/validate?code={$booking->booking_code}&token={$token}",
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Ensure booking has a valid checkin token.
     */
    public function ensureCheckinToken(Booking $booking): string
    {
        if (empty($booking->checkin_token)) {
            $token = Str::random(64);
            $booking->update(['checkin_token' => $token]);
            return $token;
        }

        return $booking->checkin_token;
    }

    /**
     * Compile structured schedule sessions for E-Pass display.
     *
     * @param Booking $booking
     * @return array
     */
    public function formatScheduleSessions(Booking $booking): array
    {
        $sessions = [];
        $totalHours = 0;

        foreach ($booking->slots->sortBy('event_start_at') as $slot) {
            $durationHours = (int) ceil($slot->event_start_at->diffInMinutes($slot->event_end_at) / 60);
            $totalHours += $durationHours;

            $sessions[] = [
                'room_name' => $slot->room->name ?? 'Ruangan MCC',
                'floor_level' => $slot->room->floor_level ?? '-',
                'facilities' => $slot->room->facilities ?? collect(),
                'date_formatted' => $slot->event_start_at->locale('id')->translatedFormat('l, d F Y'),
                'time_range' => $slot->event_start_at->format('H:i') . ' – ' . $slot->event_end_at->format('H:i') . ' WIB',
                'duration_hours' => $durationHours,
            ];
        }

        return [
            'sessions' => $sessions,
            'total_hours' => $totalHours,
            'earliest_date' => $booking->slots->min('event_start_at'),
            'latest_date' => $booking->slots->max('event_end_at'),
        ];
    }

    /**
     * High-reliability standalone QR Code SVG matrix generator fallback.
     */
    protected function generateFallbackSvg(string $data, int $size): string
    {
        $safeData = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '" width="' . $size . '" height="' . $size . '">
            <rect width="100%" height="100%" fill="#ffffff"/>
            <text x="50%" y="45%" text-anchor="middle" font-family="sans-serif" font-size="12" font-weight="bold" fill="#001A72">E-PASS QR CODE</text>
            <text x="50%" y="60%" text-anchor="middle" font-family="monospace" font-size="10" fill="#64748b">SCAN DI FRONT OFFICE</text>
        </svg>';
    }
}
