<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Jadwal Operasional Gedung MCC</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #001A72;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            color: #001A72;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9px;
            color: #475569;
            margin-top: 2px;
        }
        .meta-box {
            text-align: right;
            font-size: 8.5px;
            color: #64748b;
        }
        .meta-box strong {
            color: #001A72;
        }
        .filter-badge {
            display: inline-block;
            background-color: #EEF2FF;
            color: #001A72;
            border: 1px solid #C7D2FE;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8.5px;
            margin-top: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th {
            background-color: #001A72;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 6px 5px;
            text-align: left;
            border: 1px solid #001A72;
        }
        table.data-table td {
            padding: 5px;
            border: 1px solid #cbd5e1;
            font-size: 8.5px;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge-subsidy {
            background-color: #d1fae5;
            color: #065f46;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5px;
        }
        .badge-commercial {
            background-color: #f3e8ff;
            color: #6b21a8;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7.5px;
        }
        .footer {
            margin-top: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        .footer table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen Resmi -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="title">Jadwal Operasional Gedung MCC (Confirmed Agenda)</div>
                    <div class="subtitle">Malang Creative Center • Dokumen Resmi Koordinasi Front Office, Keamanan &amp; Tim Teknis</div>
                    @if($dateFrom || $dateTo || $floor)
                        <div class="filter-badge">
                            Filter: 
                            @if($dateFrom && $dateTo)
                                Periode {{ $dateFrom }} s/d {{ $dateTo }}
                            @elseif($dateFrom)
                                Mulai {{ $dateFrom }}
                            @elseif($dateTo)
                                Sampai {{ $dateTo }}
                            @endif
                            @if($floor)
                                • Lantai {{ $floor }}
                            @endif
                        </div>
                    @else
                        <div class="filter-badge">Semua Jadwal Acara Terkonfirmasi</div>
                    @endif
                </td>
                <td class="meta-box" style="width: 35%;">
                    <div>Tanggal Cetak: <strong>{{ $generatedAt }}</strong></div>
                    <div>Petugas Kurator: <strong>{{ $generatedBy }}</strong></div>
                    <div>Total Sesi: <strong>{{ $slots->count() }} Jadwal</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%; text-align: center;">No</th>
                <th style="width: 10%;">Kode &amp; Tanggal</th>
                <th style="width: 12%;">Ruangan &amp; Lantai</th>
                <th style="width: 8%;">Waktu (Jam)</th>
                <th style="width: 18%;">Nama Acara &amp; Kategori</th>
                <th style="width: 16%;">Lembaga / Pemohon</th>
                <th style="width: 13%;">Kontak PIC</th>
                <th style="width: 5%; text-align: center;">Peserta</th>
                <th style="width: 15%;">Kebutuhan Peralatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($slots as $index => $slot)
                @php
                    $booking = $slot->booking;
                    $event = $booking?->eventDetail;
                    $room = $slot->room;
                    $user = $booking?->user;
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>
                        <strong style="font-family: monospace; color: #001A72;">{{ $booking?->booking_code ?? '-' }}</strong><br>
                        <span>{{ $slot->event_start_at->translatedFormat('d M Y') }}</span><br>
                        @if($booking?->pricing_scheme === 'commercial')
                            <span class="badge-commercial">Komersial</span>
                        @else
                            <span class="badge-subsidy">Subsidi</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $room?->name ?? 'Ruangan MCC' }}</strong><br>
                        <span style="color: #64748b;">Lantai {{ $room?->floor_level ?? '-' }} (Kapasitas: {{ $room?->capacity ?? '-' }})</span>
                    </td>
                    <td>
                        <strong>{{ $slot->event_start_at->format('H:i') }} - {{ $slot->event_end_at->format('H:i') }}</strong><br>
                        <span style="color: #64748b; font-size: 7.5px;">Buffer: {{ $room?->buffer_minutes ?? 30 }} Menit</span>
                    </td>
                    <td>
                        <strong>{{ $event?->event_name ?? 'Acara MCC' }}</strong><br>
                        <span style="color: #475569;">{{ $event?->event_category ?? '-' }} • {{ $event?->ekraf_subsector ?? '-' }}</span>
                    </td>
                    <td>
                        <strong>{{ $event?->institution_name_snapshot ?? ($user?->institution_name ?? $user?->full_name ?? '-') }}</strong>
                    </td>
                    <td>
                        <strong>{{ $event?->pic_name_snapshot ?? ($user?->full_name ?? '-') }}</strong><br>
                        <span style="font-family: monospace; color: #047857;">{{ $event?->pic_phone_snapshot ?? ($user?->phone_number ?? '-') }}</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $event?->estimated_participants ?? '-' }}
                    </td>
                    <td>
                        <span>{{ $event?->equipment_needed ?: 'Tidak ada peralatan khusus' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 15px; color: #94a3b8; font-style: italic;">
                        Tidak ada jadwal agenda acara sah (confirmed) pada filter rentang yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Dokumen -->
    <div class="footer">
        <table>
            <tr>
                <td style="width: 50%;">
                    Malang Creative Center (MCC) • Jl. A. Yani No.53, Blimbing, Kota Malang, Jawa Timur
                </td>
                <td style="width: 50%; text-align: right;">
                    Dokumen Internal Operasional — Rujukan Front Office, Security, &amp; Teknisi
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
