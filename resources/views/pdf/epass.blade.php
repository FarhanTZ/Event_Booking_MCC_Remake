<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Pass Resmi - {{ $booking->booking_code }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .ticket-container {
            border: 2px solid #001A72;
            border-radius: 8px;
            overflow: hidden;
            background: #ffffff;
        }
        .header {
            background-color: #001A72;
            color: #ffffff;
            padding: 16px 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #ffffff;
            margin: 0;
        }
        .header-sub {
            font-size: 9px;
            color: #c7d2fe;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-badge {
            background-color: #10b981;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 4px 10px;
            border-radius: 12px;
            text-transform: uppercase;
            display: inline-block;
        }
        .body-section {
            padding: 18px 20px;
        }
        .qr-section {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 16px;
        }
        .qr-cell {
            width: 160px;
            padding: 14px;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #e2e8f0;
        }
        .qr-box {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 8px;
            display: inline-block;
            border-radius: 4px;
        }
        .meta-cell {
            padding: 14px 18px;
            vertical-align: top;
        }
        .booking-code {
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #001A72;
            letter-spacing: 1px;
            margin: 2px 0 10px 0;
        }
        .label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .value {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .table-sessions {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
            font-size: 10px;
        }
        .table-sessions th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        .table-sessions td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            color: #1e293b;
        }
        .instruction-box {
            background-color: #fef3c7;
            border: 1.5px solid #f59e0b;
            border-radius: 6px;
            padding: 12px 14px;
            margin-top: 14px;
        }
        .instruction-title {
            font-size: 10px;
            font-weight: bold;
            color: #92400e;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .instruction-text {
            font-size: 10px;
            font-weight: bold;
            color: #78350f;
            line-height: 1.4;
            margin: 0;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 10px 20px;
            font-size: 8px;
            color: #64748b;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="vertical-align: middle;">
                        <div class="header-sub">Pemerintah Kota Malang • Diskopindag</div>
                        <h1 class="header-title">E-PASS AKSES RESMI GEDUNG MCC</h1>
                        <div style="font-size: 9px; color: #e0e7ff; margin-top: 1px;">Malang Creative Center - Tiket Masuk & Validasi Kedatangan</div>
                    </td>
                    <td style="text-align: right; vertical-align: middle; width: 140px;">
                        <span class="status-badge">STATUS: DISETUJUI</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body -->
        <div class="body-section">
            
            <!-- QR & Booking Meta -->
            <table class="qr-section">
                <tr>
                    <td class="qr-cell">
                        <div class="qr-box">
                            <img src="data:image/svg+xml;base64,{{ base64_encode($qrCodeSvg) }}" width="120" height="120" alt="QR Code">
                        </div>
                        <div style="font-size: 8px; color: #001A72; font-weight: bold; margin-top: 6px;">SCAN DI FRONT OFFICE</div>
                    </td>
                    <td class="meta-cell">
                        <div class="label">Kode Unik Reservasi</div>
                        <div class="booking-code">#{{ $booking->booking_code }}</div>

                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; vertical-align: top;">
                                    <div class="label">Nama Kegiatan</div>
                                    <div class="value">{{ $booking->eventDetail->event_name ?? 'Kegiatan Komunitas MCC' }}</div>

                                    <div class="label">Sub-Sektor Ekraf</div>
                                    <div class="value" style="font-size: 10px; font-weight: normal;">{{ $booking->eventDetail->ekraf_subsector ?? '-' }}</div>
                                </td>
                                <td style="width: 50%; vertical-align: top;">
                                    <div class="label">Penyelenggara / Komunitas</div>
                                    <div class="value">{{ $booking->eventDetail->institution_name_snapshot ?? $booking->user->institution_name ?? $booking->user->full_name }}</div>

                                    <div class="label">Skema Pembiayaan</div>
                                    <div class="value" style="font-size: 10px; color: #059669;">
                                        {{ $booking->pricing_scheme === 'commercial' ? 'Komersial (Rp ' . number_format($booking->nominal_amount, 0, ',', '.') . ')' : 'Subsidi Pemkot Malang (Gratis)' }}
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- Jadwal & Ruangan Table -->
            <div style="font-weight: bold; font-size: 10px; text-transform: uppercase; color: #0f172a; margin-bottom: 4px;">
                Rincian Ruangan &amp; Jadwal Penggunaan ({{ count($scheduleData['sessions']) }} Sesi • {{ $scheduleData['total_hours'] }} Jam):
            </div>

            <table class="table-sessions">
                <thead>
                    <tr>
                        <th style="width: 25px; text-align: center;">No</th>
                        <th>Nama Ruangan</th>
                        <th style="width: 60px;">Lantai</th>
                        <th>Tanggal Pelaksanaan</th>
                        <th>Waktu Sesi</th>
                        <th style="width: 50px; text-align: center;">Durasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scheduleData['sessions'] as $idx => $sess)
                        <tr>
                            <td style="text-align: center; color: #64748b;">{{ $idx + 1 }}</td>
                            <td style="font-weight: bold; color: #001A72;">{{ $sess['room_name'] }}</td>
                            <td>Lantai {{ $sess['floor_level'] }}</td>
                            <td>{{ $sess['date_formatted'] }}</td>
                            <td style="font-family: monospace; font-weight: bold; color: #001A72;">{{ $sess['time_range'] }}</td>
                            <td style="text-align: center;">{{ $sess['duration_hours'] }} Jam</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- PIC Info -->
            <table style="width: 100%; border-collapse: collapse; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; font-size: 9.5px; margin-top: 6px;">
                <tr>
                    <td style="width: 50%; padding: 4px 8px;">
                        <span class="label">Penanggung Jawab (PIC Lapangan):</span><br>
                        <strong>{{ $booking->eventDetail->pic_name_snapshot ?? $booking->user->full_name }}</strong> ({{ $booking->eventDetail->pic_phone_snapshot ?? $booking->user->phone_number }})
                    </td>
                    <td style="width: 50%; padding: 4px 8px;">
                        <span class="label">Kontak Darurat / No. Surat:</span><br>
                        Darurat: <strong>{{ $booking->eventDetail->pic_emergency_phone ?: '-' }}</strong> | No. Surat: <strong>{{ $booking->eventDetail->official_letter_number ?: '-' }}</strong>
                    </td>
                </tr>
            </table>

            <!-- Mandatory Instruction -->
            <div class="instruction-box">
                <div class="instruction-title">Instruksi Wajib Check-In Hari-H:</div>
                <p class="instruction-text">
                    "Harap datang 30 menit sebelum acara dimulai dan titipkan fisik KTP asli penanggung jawab acara di meja Front Office Lantai Dasar untuk ditukar dengan akses ruangan."
                </p>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td style="text-align: left;">
                        Dokumen Resmi Digital Disahkan Sistem Malang Creative Center (MCC)
                    </td>
                    <td style="text-align: right; font-family: monospace;">
                        Diterbitkan: {{ now()->translatedFormat('d M Y H:i') }} WIB
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
