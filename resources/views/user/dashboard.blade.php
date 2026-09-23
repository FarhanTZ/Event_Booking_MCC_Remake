
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemohon - MCC Event Booking</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(["resources/css/app.css", "resources/js/app.js"])
    <style>body { font-family: "Plus Jakarta Sans", sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">

    <!-- Navbar -->
    @include("layouts.partials.navbar")

    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
        <!-- Solid Corporate Hero / Welcome Banner -->
        <div class="bg-[#001A72] rounded-brand p-6 sm:p-7 text-white shadow-card mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-white/10 text-amber-300 text-xs font-bold mb-2 border border-white/20">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Pemohon Terverifikasi
                </div>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight">Portal Pemohon: Komunitas Desain Malang</h1>
                <p class="text-xs sm:text-sm text-slate-200 mt-1 max-w-xl">
                    Pantau alur kurasi proposal, revisi berkas 24 jam, dan akses tiket E-Pass digital resmi untuk check-in.
                </p>
            </div>
            <a href="/jadwal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-brand bg-white text-[#001A72] font-bold text-xs hover:bg-slate-100 shadow-sm transition-colors shrink-0">
                <i data-lucide="calendar-plus" class="w-4 h-4 text-[#001A72]"></i>
                <span>Ajukan Booking Baru</span>
            </a>
        </div>

        <!-- Metric Grid (Tanpa ornamen dekorasi nirfungsi) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Menunggu Kurasi</span>
                        <span class="text-lg font-extrabold text-slate-900">1 Pengajuan</span>
                    </div>
                </div>
            </x-card.base>

            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Perlu Revisi (24h)</span>
                        <span class="text-lg font-extrabold text-indigo-700">1 Pengajuan</span>
                    </div>
                </div>
            </x-card.base>

            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Disetujui (E-Pass)</span>
                        <span class="text-lg font-extrabold text-emerald-700">2 Acara</span>
                    </div>
                </div>
            </x-card.base>

            <x-card.base>
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-brand bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 font-bold uppercase tracking-wider block">Total Riwayat</span>
                        <span class="text-lg font-extrabold text-slate-900">4 Acara</span>
                    </div>
                </div>
            </x-card.base>
        </div>

        <!-- Section Permohonan dengan Semantic Status Badges -->
        <div class="bg-white rounded-brand border border-slate-200 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
                <h2 class="font-bold text-sm text-slate-900">Daftar Permohonan Ruangan Anda</h2>
                <span class="text-xs text-slate-500 font-medium">Live Status Tracking</span>
            </div>

            <div class="divide-y divide-slate-100">
                <!-- Status Confirmed -->
                <div class="p-5 hover:bg-slate-50/70 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-500">#MCC-2026-X89B</span>
                                <x-badge.status status="confirmed">Disetujui</x-badge.status>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Workshop UI/UX Design System 2026</h3>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                                <span class="flex items-center gap-1"><i data-lucide="building" class="w-3.5 h-3.5 text-slate-400"></i> Auditorium Utama (Lt. 2)</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> 28 Oktober 2026</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i> 09:00 - 15:00 WIB</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-brand text-xs font-semibold shadow-xs transition-colors">
                                <i data-lucide="qr-code" class="w-4 h-4"></i>
                                <span>Buka E-Pass Digital</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Revised with Grace Period 24h -->
                <div class="p-5 bg-indigo-50/20 hover:bg-indigo-50/40 transition-colors border-l-4 border-indigo-600">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-500">#MCC-2026-R41Q</span>
                                <x-badge.status status="revised">Perlu Revisi Dokumen</x-badge.status>
                                <span class="text-xs text-indigo-900 font-bold flex items-center gap-1">
                                    <i data-lucide="timer" class="w-3.5 h-3.5 text-indigo-600"></i> Sisa Waktu Grace Period: 18 Jam 42 Menit
                                </span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Pameran Karya Mahasiswa Multimedia</h3>
                            <p class="text-xs text-slate-700 bg-white p-3 rounded-brand border border-indigo-100 shadow-2xs leading-relaxed">
                                <strong class="text-indigo-900">Catatan Tim Marketing:</strong> "Mohon lampirkan susunan acara (rundown) rinci dan estimasi daya listrik alat pameran pada proposal."
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <button class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-brand text-xs font-semibold shadow-xs transition-colors">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                <span>Unggah Revisi Proposal</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Awaiting Marketing Review -->
                <div class="p-5 hover:bg-slate-50/70 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-500">#MCC-2026-P09K</span>
                                <x-badge.status status="locked">Menunggu Kurasi</x-badge.status>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Podcast Talkshow Kreator Konten Malang</h3>
                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 font-medium">
                                <span class="flex items-center gap-1"><i data-lucide="building" class="w-3.5 h-3.5 text-slate-400"></i> Podcast Studio (Lt. 4)</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i> 12 November 2026</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 italic">Sedang dalam antrean kurasi marketing</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include("layouts.partials.footer")

</body>
</html>
