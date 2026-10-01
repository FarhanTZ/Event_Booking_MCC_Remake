<?php

namespace App\Services\Admin;

use App\Actions\Admin\GetExecutiveAnalyticsAction;
use App\DataTransferObjects\Admin\ExecutiveAnalyticsFilterData;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExecutiveReportExportService
{
    public function __construct(
        protected GetExecutiveAnalyticsAction $analyticsAction
    ) {}

    /**
     * Export Executive Analytics Report to official PDF format.
     */
    public function exportPdf(ExecutiveAnalyticsFilterData $dto): Response
    {
        $analytics = $this->analyticsAction->execute($dto);

        $dateFromStr = $dto->dateFrom ? Carbon::parse($dto->dateFrom)->translatedFormat('d F Y') : Carbon::now()->startOfMonth()->translatedFormat('d F Y');
        $dateToStr = $dto->dateTo ? Carbon::parse($dto->dateTo)->translatedFormat('d F Y') : Carbon::now()->endOfMonth()->translatedFormat('d F Y');

        $pdf = Pdf::loadView('pdf.admin.executive-report', [
            'analytics' => $analytics,
            'dateFrom' => $dateFromStr,
            'dateTo' => $dateToStr,
            'generatedAt' => Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB',
            'generatedBy' => auth()->user()?->full_name ?? 'Super Administrator MCC',
        ])->setPaper('a4', 'portrait');

        $fileName = 'Laporan-Eksekutif-Kinerja-MCC-' . Carbon::now()->format('Ymd-His') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Export Executive Analytics Data to CSV / Excel format.
     */
    public function exportCsv(ExecutiveAnalyticsFilterData $dto): StreamedResponse
    {
        $analytics = $this->analyticsAction->execute($dto);
        $kpis = $analytics['kpis'];
        $floorOccupancy = $analytics['floor_occupancy'];
        $roomOccupancy = $analytics['room_occupancy'];
        $ekrafDist = $analytics['ekraf_distribution'];
        $scheme = $analytics['pricing_scheme'];

        $fileName = 'Laporan-Eksekutif-Kinerja-MCC-' . Carbon::now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($kpis, $floorOccupancy, $roomOccupancy, $ekrafDist, $scheme) {
            $handle = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8 recognition
            fputs($handle, "\xEF\xBB\xBF");

            // Section 1: Ringkasan Eksekutif
            fputcsv($handle, ['RINGKASAN EKSEKUTIF KINERJA GEDUNG MALANG CREATIVE CENTER (MCC)']);
            fputcsv($handle, ['Dicetak Pada', Carbon::now()->translatedFormat('d F Y, H:i') . ' WIB']);
            fputcsv($handle, []);

            fputcsv($handle, ['METRIK UTAMA', 'NILAI']);
            fputcsv($handle, ['Tingkat Okupansi Gedung (Occupancy Rate)', $kpis['occupancy_rate'] . '%']);
            fputcsv($handle, ['Total Jam Terpakai', $kpis['total_utilized_hours'] . ' Jam']);
            fputcsv($handle, ['Total Kapasitas Jam Tersedia', $kpis['total_available_hours'] . ' Jam']);
            fputcsv($handle, ['Total Reservasi Berlangsung / Selesai', $kpis['total_bookings'] . ' Acara']);
            fputcsv($handle, ['Total Sesi Jadwal', $kpis['total_sessions'] . ' Sesi']);
            fputcsv($handle, ['Volume Kegiatan Subsidi Pemkot Malang (Gratis)', $scheme['subsidy_count'] . ' Acara (' . $scheme['subsidy_percentage'] . '%)']);
            fputcsv($handle, ['Volume Kegiatan Sewa Komersial', $scheme['commercial_count'] . ' Acara (' . $scheme['commercial_percentage'] . '%)']);
            fputcsv($handle, ['Total Penerimaan Kas Komersial', 'Rp ' . number_format($scheme['total_revenue'], 0, ',', '.')]);
            fputcsv($handle, ['Total Insiden Fasilitas Tercatat', $kpis['total_incidents'] . ' Kasus']);
            fputcsv($handle, ['Insiden Terselesaikan', $kpis['resolved_incidents'] . ' Kasus']);
            fputcsv($handle, ['Insiden Menunggu Ganti Rugi', $kpis['unresolved_incidents'] . ' Kasus']);
            fputcsv($handle, []);

            // Section 2: Okupansi per Tingkat Lantai
            fputcsv($handle, ['UTILISASI & OKUPANSI PER TINGKAT LANTAI (LANTAI 1 S/D 8)']);
            fputcsv($handle, ['Lantai', 'Jumlah Ruangan Aktif', 'Jam Terpakai', 'Kapasitas Tersedia (Jam)', 'Tingkat Okupansi (%)']);
            foreach ($floorOccupancy as $f => $data) {
                fputcsv($handle, [
                    'Lantai ' . $f,
                    $data['rooms_count'] . ' Ruangan',
                    $data['used_hours'] . ' Jam',
                    $data['available_hours'] . ' Jam',
                    $data['occupancy_rate'] . '%',
                ]);
            }
            fputcsv($handle, []);

            // Section 3: Okupansi per Ruangan
            fputcsv($handle, ['REKAP UTILISASI RUANGAN']);
            fputcsv($handle, ['Nama Ruangan', 'Lantai', 'Kapasitas (Orang)', 'Jumlah Acara', 'Jam Terpakai', 'Okupansi (%)']);
            foreach ($roomOccupancy as $room) {
                fputcsv($handle, [
                    $room['name'],
                    'Lantai ' . $room['floor_level'],
                    $room['capacity'] . ' Orang',
                    $room['events_count'] . ' Acara',
                    $room['used_hours'] . ' Jam',
                    $room['occupancy_rate'] . '%',
                ]);
            }
            fputcsv($handle, []);

            // Section 4: Distribusi 17 Sub-Sektor Ekraf
            fputcsv($handle, ['DISTRIBUSI 17 SUB-SEKTOR EKONOMI KREATIF (EKRAF)']);
            fputcsv($handle, ['Sub-Sektor Ekraf', 'Jumlah Acara', 'Proporsi (%)']);
            foreach ($ekrafDist as $sub) {
                fputcsv($handle, [
                    $sub['subsector'],
                    $sub['count'] . ' Acara',
                    $sub['percentage'] . '%',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
