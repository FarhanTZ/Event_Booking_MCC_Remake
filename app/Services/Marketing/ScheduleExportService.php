<?php

namespace App\Services\Marketing;

use App\Models\Booking;
use App\Models\BookingSlot;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleExportService
{
    /**
     * Export confirmed bookings schedule to PDF format.
     */
    public function exportPdf(?string $dateFrom = null, ?string $dateTo = null, ?int $floor = null): Response
    {
        $slots = $this->querySlots($dateFrom, $dateTo, $floor)->get();

        $pdf = Pdf::loadView('pdf.marketing.schedule-recap', [
            'slots' => $slots,
            'dateFrom' => $dateFrom ? Carbon::parse($dateFrom)->translatedFormat('d F Y') : null,
            'dateTo' => $dateTo ? Carbon::parse($dateTo)->translatedFormat('d F Y') : null,
            'floor' => $floor,
            'generatedAt' => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
            'generatedBy' => auth()->user()?->full_name ?? auth()->user()?->name ?? 'Marketing MCC',
        ])->setPaper('a4', 'landscape');

        $fileName = 'Rekap-Jadwal-Operasional-MCC-' . ($dateFrom ? Carbon::parse($dateFrom)->format('Ymd') : 'Semua') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export confirmed bookings schedule to Excel / CSV format.
     */
    public function exportCsv(?string $dateFrom = null, ?string $dateTo = null, ?int $floor = null): StreamedResponse
    {
        $slots = $this->querySlots($dateFrom, $dateTo, $floor)->get();

        $fileName = 'Rekap-Jadwal-Operasional-MCC-' . ($dateFrom ? Carbon::parse($dateFrom)->format('Ymd') : 'Semua') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($slots) {
            $handle = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8 recognition
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'No',
                'Kode Booking',
                'Nama Acara / Kegiatan',
                'Kategori Acara',
                'Sub-Sektor Ekraf',
                'Ruangan',
                'Lantai',
                'Tanggal Acara',
                'Jam Mulai',
                'Jam Selesai',
                'Buffer Sterilisasi',
                'Nama Lembaga / Pemohon',
                'PIC Acara',
                'No. WhatsApp PIC',
                'Estimasi Peserta',
                'Peralatan / Kebutuhan Khusus',
                'Skema Pembiayaan',
                'Status Permohonan',
            ]);

            $no = 1;
            foreach ($slots as $slot) {
                $booking = $slot->booking;
                $event = $booking?->eventDetail;
                $room = $slot->room;
                $user = $booking?->user;

                fputcsv($handle, [
                    $no++,
                    $booking?->booking_code ?? '-',
                    $event?->event_name ?? '-',
                    $event?->event_category ?? '-',
                    $event?->ekraf_subsector ?? '-',
                    $room?->name ?? '-',
                    $room ? 'Lantai ' . $room->floor_level : '-',
                    $slot->event_start_at->translatedFormat('d/m/Y'),
                    $slot->event_start_at->format('H:i') . ' WIB',
                    $slot->event_end_at->format('H:i') . ' WIB',
                    ($room?->buffer_minutes ?? 30) . ' Menit',
                    $event?->institution_name_snapshot ?? ($user?->institution_name ?? $user?->full_name ?? '-'),
                    $event?->pic_name_snapshot ?? ($user?->full_name ?? '-'),
                    $event?->pic_phone_snapshot ?? ($user?->phone_number ?? '-'),
                    $event?->estimated_participants ?? 0,
                    $event?->equipment_needed ?? 'Tidak ada',
                    $booking?->pricing_scheme === 'commercial' ? 'Komersial' : 'Subsidi Pemkot',
                    'Confirmed (Sah)',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build base query for confirmed slots ordered chronologically by event start date & room.
     */
    protected function querySlots(?string $dateFrom = null, ?string $dateTo = null, ?int $floor = null): Builder
    {
        $query = BookingSlot::query()
            ->where('is_active', true)
            ->whereHas('booking', function ($bq) {
                $bq->whereIn('status', ['confirmed', 'checked_in', 'completed']);
            })
            ->with(['booking.eventDetail', 'booking.user', 'room']);

        if ($dateFrom) {
            $query->where('event_start_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        }

        if ($dateTo) {
            $query->where('event_start_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        if ($floor && $floor > 0) {
            $query->whereHas('room', function ($rq) use ($floor) {
                $rq->where('floor_level', $floor);
            });
        }

        return $query->orderBy('event_start_at', 'asc');
    }
}
