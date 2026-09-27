<!-- ==================================================================== -->
<!-- SISI KANAN: PROFIL DO, JAM LAPANGAN, METRIK & SOP OPERASIONAL -->
<!-- ==================================================================== -->
<aside x-show="sidebarRight" 
       x-transition:enter="transition-all ease-out duration-200"
       x-transition:enter-start="opacity-0 translate-x-4"
       x-transition:enter-end="opacity-100 translate-x-0"
       x-transition:leave="transition-all ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-x-0"
       x-transition:leave-end="opacity-0 translate-x-4"
       class="w-full lg:w-72 xl:w-80 shrink-0 bg-white rounded-brand border border-slate-200 shadow-card flex flex-col justify-between p-4 sm:p-5 lg:sticky lg:top-4 lg:h-[calc(100vh-2rem)] overflow-y-auto custom-scrollbar z-30 space-y-5">
    
    <div class="space-y-4">
        <!-- 1. Header & Close Button -->
        <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="font-extrabold text-sm tracking-tight text-slate-900">
                    Status Petugas Lapangan
                </span>
            </div>

            <button type="button" 
                    @click="sidebarRight = false" 
                    class="p-1.5 rounded-brand text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer"
                    title="Tutup Panel Kanan">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>

        <!-- 2. Profil Duty Officer Card -->
        <div class="p-3.5 rounded-brand bg-slate-50 border border-slate-200 space-y-2.5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#001A72] text-white flex items-center justify-center font-black text-sm shadow-2xs shrink-0">
                    {{ strtoupper(substr(auth()->user()->full_name ?? 'D', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-black text-slate-900 block truncate">
                        {{ auth()->user()->full_name ?? 'Petugas Lapangan' }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold block truncate">
                        {{ auth()->user()->phone_number ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-[11px]">
                <span class="text-slate-500 font-medium">Role:</span>
                <span class="font-bold text-[#001A72] px-2 py-0.5 rounded-full bg-indigo-50 border border-indigo-100">
                    Duty Officer Lapangan
                </span>
            </div>
        </div>

        <!-- 3. Real-time Clock Card -->
        <div class="p-3.5 rounded-brand bg-[#001A72] text-white shadow-card space-y-1 text-center">
            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-300">Waktu Lapangan (WIB)</span>
            <div class="text-2xl font-black font-mono tracking-tight text-white" x-text="currentTimeString">
                --:--:-- WIB
            </div>
            <p class="text-[11px] text-slate-200 font-medium">
                {{ $target_date->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <!-- 4. Ringkasan Metrik KPI Lapangan -->
        <div class="space-y-2 pt-1">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-1 block">
                Ringkasan Beban Tugas
            </span>

            <div class="space-y-1.5">
                <!-- Total Tugas -->
                <div class="p-2.5 rounded-brand bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-700">Total Tugas Hari Ini</span>
                    <span class="font-black text-slate-900 text-sm">{{ $metrics['total'] ?? 0 }}</span>
                </div>

                <!-- Perlu Buka Ruangan (FR-DO-02) -->
                <div class="p-2.5 rounded-brand bg-amber-50 border border-amber-200 flex items-center justify-between text-xs">
                    <span class="font-bold text-amber-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Perlu Buka Ruangan
                    </span>
                    <span class="font-black text-amber-900 text-sm">{{ $metrics['needs_opening'] ?? 0 }}</span>
                </div>

                <!-- Checked-in -->
                <div class="p-2.5 rounded-brand bg-teal-50 border border-teal-200 flex items-center justify-between text-xs">
                    <span class="font-bold text-teal-800 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></span>
                        Sedang Berlangsung
                    </span>
                    <span class="font-black text-teal-900 text-sm">{{ $metrics['checked_in'] ?? 0 }}</span>
                </div>

                <!-- Akan Datang -->
                <div class="p-2.5 rounded-brand bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                    <span class="font-bold text-emerald-800">Akan Datang (Hari Ini)</span>
                    <span class="font-black text-emerald-900 text-sm">{{ $metrics['upcoming'] ?? 0 }}</span>
                </div>

                <!-- Selesai -->
                <div class="p-2.5 rounded-brand bg-slate-100 border border-slate-200 flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-600">Selesai / Sesi Berakhir</span>
                    <span class="font-black text-slate-700 text-sm">{{ $metrics['completed'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- 5. SOP & Checklist Singkat Duty Officer Lapangan -->
        <div class="space-y-2 pt-2 border-t border-slate-100">
            <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-1 block">
                SOP Inspeksi Lapangan
            </span>

            <div class="p-3 rounded-brand bg-slate-50 border border-slate-200 space-y-2 text-[11px] text-slate-600 leading-relaxed">
                <div class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-bold shrink-0 mt-0.5">1</span>
                    <span>Pastikan ruangan & fasilitas siap pakai sebelum jam mulai acara.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-bold shrink-0 mt-0.5">2</span>
                    <span>Cek rekap peralatan mandiri yang dibawa masuk oleh pemohon.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-bold shrink-0 mt-0.5">3</span>
                    <span>Pantau sisa waktu penggunaan gedung via bilah hitung mundur.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-[#001A72] text-white flex items-center justify-center text-[9px] font-bold shrink-0 mt-0.5">4</span>
                    <span>Lakukan koordinasi inspeksi checkout fisik saat sesi berakhir.</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Bantuan & Kontak Cepat Meja Resepsionis FO -->
    <div class="pt-3 border-t border-slate-100">
        <div class="p-2.5 rounded-brand bg-[#EEF2FF] border border-indigo-100 flex items-center gap-2.5 text-xs text-[#001A72]">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <div class="min-w-0">
                <span class="font-bold block leading-tight">Front Office Lantai 1</span>
                <span class="text-[10px] text-slate-500 block leading-tight">Hubungi jika butuh bantuan kustodian KTP</span>
            </div>
        </div>
    </div>
</aside>
