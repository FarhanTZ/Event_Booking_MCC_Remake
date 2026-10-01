<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Eksekutif Kinerja & Utilisasi Gedung MCC</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9.5px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2.5px solid #001A72;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .title {
            font-size: 15px;
            font-weight: bold;
            color: #001A72;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 9.5px;
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
        .section-heading {
            font-size: 11px;
            font-weight: bold;
            color: #001A72;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 14px;
            margin-bottom: 8px;
        }
        .kpi-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            border-radius: 4px;
            text-align: center;
        }
        .kpi-card .val {
            font-size: 16px;
            font-weight: bold;
            color: #001A72;
            margin-bottom: 2px;
        }
        .kpi-card .lbl {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 10px;
        }
        table.data-table th {
            background-color: #001A72;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 6px;
            text-align: left;
            border: 1px solid #001A72;
        }
        table.data-table td {
            padding: 5px 6px;
            border: 1px solid #e2e8f0;
            font-size: 8.5px;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .progress-bar-bg {
            background-color: #e2e8f0;
            border-radius: 3px;
            height: 8px;
            width: 100%;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: #001A72;
            height: 8px;
            border-radius: 3px;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="title">MALANG CREATIVE CENTER (MCC)</div>
                    <div class="subtitle">Laporan Eksekutif Kinerja Gedung &amp; Analitik Utilisasi (FR-ADM-06)</div>
                    <div style="margin-top: 4px; font-size: 8.5px; color: #64748b;">
                        Periode Analisis: <strong>{{ $dateFrom }} s/d {{ $dateTo }}</strong>
                    </div>
                </td>
                <td class="meta-box" style="width: 35%;">
                    <div>Tanggal Cetak: <strong>{{ $generatedAt }}</strong></div>
                    <div>Otoritas Dokumen: <strong>{{ $generatedBy }}</strong></div>
                    <div>Klasifikasi: <strong>Laporan Resmi Eksekutif</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Ringkasan Eksekutif KPI Utama -->
    <div class="section-heading">1. Ringkasan Kinerja &amp; Utilisasi Utama</div>
    <table class="kpi-grid">
        <tr>
            <td style="width: 25%; padding-right: 4px;">
                <div class="kpi-card">
                    <div class="val">{{ $analytics['kpis']['occupancy_rate'] }}%</div>
                    <div class="lbl">Occupancy Rate Gedung</div>
                </div>
            </td>
            <td style="width: 25%; padding-left: 2px; padding-right: 2px;">
                <div class="kpi-card">
                    <div class="val">{{ $analytics['kpis']['total_utilized_hours'] }} Jam</div>
                    <div class="lbl">Jam Terpakai ({{ $analytics['kpis']['total_bookings'] }} Acara)</div>
                </div>
            </td>
            <td style="width: 25%; padding-left: 2px; padding-right: 2px;">
                <div class="kpi-card">
                    <div class="val">{{ $analytics['pricing_scheme']['subsidy_percentage'] }}%</div>
                    <div class="lbl">Rasio Subsidi Pemkot</div>
                </div>
            </td>
            <td style="width: 25%; padding-left: 4px;">
                <div class="kpi-card">
                    <div class="val">{{ $analytics['kpis']['total_incidents'] }} Kasus</div>
                    <div class="lbl">Insiden Kerusakan ({{ $analytics['kpis']['resolved_incidents'] }} Selesai)</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. Utilisasi per Tingkat Lantai (Lantai 1 s/d 8) -->
    <div class="section-heading">2. Utilisasi &amp; Okupansi per Tingkat Lantai</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tingkat Lantai</th>
                <th style="width: 20%; text-align: center;">Jumlah Ruangan Aktif</th>
                <th style="width: 20%; text-align: right;">Jam Terpakai</th>
                <th style="width: 20%; text-align: right;">Kapasitas Tersedia</th>
                <th style="width: 25%;">Tingkat Okupansi (%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($analytics['floor_occupancy'] as $floor => $data)
                <tr>
                    <td><strong>Lantai {{ $floor }}</strong></td>
                    <td style="text-align: center;">{{ $data['rooms_count'] }} Ruangan</td>
                    <td style="text-align: right;">{{ $data['used_hours'] }} Jam</td>
                    <td style="text-align: right;">{{ $data['available_hours'] }} Jam</td>
                    <td>
                        <div style="font-weight: bold; margin-bottom: 2px;">{{ $data['occupancy_rate'] }}%</div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ min(100, $data['occupancy_rate']) }}%;"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 3. Rasio Pembiayaan & Penerimaan Kas Komersial -->
    <div class="section-heading">3. Rasio Skema Pembiayaan &amp; Pendapatan Komersial</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Skema Pembiayaan</th>
                <th style="text-align: center;">Volume Kegiatan</th>
                <th style="text-align: right;">Proporsi Volume</th>
                <th style="text-align: right;">Akumulasi Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Subsidi Penuh Pemkot Malang (Gratis)</strong></td>
                <td style="text-align: center;">{{ $analytics['pricing_scheme']['subsidy_count'] }} Acara</td>
                <td style="text-align: right;">{{ $analytics['pricing_scheme']['subsidy_percentage'] }}%</td>
                <td style="text-align: right; font-weight: bold; color: #059669;">Rp 0 (Subsidi APBD)</td>
            </tr>
            <tr>
                <td><strong>Sewa Komersial Berbayar</strong></td>
                <td style="text-align: center;">{{ $analytics['pricing_scheme']['commercial_count'] }} Acara</td>
                <td style="text-align: right;">{{ $analytics['pricing_scheme']['commercial_percentage'] }}%</td>
                <td style="text-align: right; font-weight: bold; color: #001A72;">
                    Rp {{ number_format($analytics['pricing_scheme']['total_revenue'], 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 4. Distribusi 17 Sub-Sektor Ekonomi Kreatif -->
    <div class="section-heading">4. Distribusi 17 Sub-Sektor Ekonomi Kreatif (Ekraf)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 55%;">Sub-Sektor Ekraf</th>
                <th style="width: 20%; text-align: center;">Volume Acara</th>
                <th style="width: 20%; text-align: right;">Proporsi (%)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($analytics['ekraf_distribution'] as $sub)
                @if($sub['count'] > 0 || $no <= 8)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td><strong>{{ $sub['subsector'] }}</strong></td>
                        <td style="text-align: center;">{{ $sub['count'] }} Acara</td>
                        <td style="text-align: right;">{{ $sub['percentage'] }}%</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- 5. Rekapitulasi Insiden & Kerusakan Fasilitas -->
    <div class="section-heading">5. Rekapitulasi Insiden Fasilitas Gedung</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40%;">Kategori Insiden</th>
                <th style="width: 20%; text-align: center;">Frekuensi</th>
                <th style="width: 40%;">Ruangan Terdampak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($analytics['incidents']['by_category'] as $cat => $freq)
                <tr>
                    <td><strong>{{ $cat }}</strong></td>
                    <td style="text-align: center;">{{ $freq }} Kasus</td>
                    <td>Terdata pada audit inspeksi Duty Officer</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #64748b;">
                        Nihil insiden fasilitas tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Footer Sign-off -->
    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Tata Kelola Operasional Gedung Malang Creative Center (MCC) &bull; Hak Cipta Dinas Koperasi, Perindustrian dan Perdagangan Kota Malang.
    </div>

</body>
</html>
