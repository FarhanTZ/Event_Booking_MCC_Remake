<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemohon - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">

    <!-- Navbar -->
    @include('layouts.partials.navbar')

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/30 text-indigo-200 text-xs font-semibold mb-2">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Pemohon Terverifikasi
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold">Halo, Komunitas Desain Malang!</h1>
                <p class="text-xs sm:text-sm text-indigo-200 mt-1 max-w-xl">
                    Pantau alur kurasi proposal, revisi berkas, hingga unduh E-Pass digital resmi untuk check-in di gedung MCC.
                </p>
            </div>
            <a href="/jadwal" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-white text-indigo-950 font-bold text-sm hover:bg-slate-100 shadow-md transition shrink-0">
                <i data-lucide="calendar-plus" class="w-4 h-4 text-indigo-600"></i>
                <span>Ajukan Booking Baru</span>
            </a>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-medium block">Menunggu Kurasi</span>
                    <span class="text-xl font-bold text-slate-900">1 Pengajuan</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-medium block">Perlu Revisi Dokumen</span>
                    <span class="text-xl font-bold text-rose-600">1 Pengajuan</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-medium block">Disetujui (E-Pass Siap)</span>
                    <span class="text-xl font-bold text-emerald-600">2 Acara</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="archive" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-medium block">Total Riwayat Acara</span>
                    <span class="text-xl font-bold text-slate-900">4 Acara</span>
                </div>
            </div>
        </div>

        <!-- Section List Permohonan & Status (Sesuai FR-USR-07) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-bold text-base text-slate-900">Daftar Permohonan Ruangan Anda</h2>
                <span class="text-xs text-slate-500">Live Status Tracking</span>
            </div>

            <div class="divide-y divide-slate-100">
                <!-- Item 1: Confirmed with E-Pass (FR-USR-09) -->
                <div class="p-5 hover:bg-slate-50/50 transition">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-500">#MCC-2026-X89B</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <i data-lucide="check" class="w-3 h-3"></i> Confirmed (Disetujui)
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base">Workshop UI/UX Design System 2026</h3>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                                <span class="flex items-center gap-1"><i data-lucide="building" class="w-3.5 h-3.5 text-slate-400"></i> Auditorium Utama (Lt. 2)</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> 28 Oktober 2026</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i> 09:00 - 15:00 WIB</span>
                            </div>
                        </div>

                        <!-- Action: Show E-Pass Modal -->
                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                                <i data-lucide="qr-code" class="w-4 h-4"></i>
                                <span>Buka E-Pass Digital</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 2: Revised State with 24h Grace Period (FR-USR-08) -->
                <div class="p-5 bg-amber-50/30 hover:bg-amber-50/50 transition border-l-4 border-amber-500">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-500">#MCC-2026-R41Q</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    <i data-lucide="alert-triangle" class="w-3 h-3"></i> Butuh Revisi Dokumen
                                </span>
                                <span class="text-xs text-amber-700 font-semibold flex items-center gap-1">
                                    <i data-lucide="timer" class="w-3.5 h-3.5"></i> Sisa Waktu: 18 Jam 42 Menit
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base">Pameran Karya Mahasiswa Multimedia</h3>
                            <p class="text-xs text-amber-900 bg-amber-100/60 p-2.5 rounded-lg border border-amber-200">
                                <strong>Catatan Tim Marketing:</strong> "Mohon lampirkan susunan acara (rundown) rinci dan estimasi daya listrik alat pameran pada proposal."
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs transition">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                <span>Unggah Revisi Proposal</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Item 3: Awaiting Marketing Review -->
                <div class="p-5 hover:bg-slate-50/50 transition">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-500">#MCC-2026-P09K</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                                    <i data-lucide="hourglass" class="w-3 h-3"></i> Menunggu Kurasi Tim MCC
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base">Podcast Talkshow Kreator Konten Malang</h3>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
                                <span class="flex items-center gap-1"><i data-lucide="building" class="w-3.5 h-3.5 text-slate-400"></i> Podcast Studio (Lt. 4)</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> 12 November 2026</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 italic">Sedang dalam antrean kurasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('layouts.partials.footer')

</body>
</html>