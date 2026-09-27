<!-- ==================================================================== -->
<!-- MODAL PROTOKOL PANITIA PULANG TANPA LAPOR (FR-FO-08) -->
<!-- ==================================================================== -->
<div x-show="showAbandonedModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showAbandonedModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeAbandonedModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showAbandonedModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-rose-900 text-white px-6 py-4 flex items-center justify-between gap-4">
                <div>
                    <span class="text-[10px] uppercase font-bold tracking-wider text-rose-200 block">Protokol Penanganan Anomali (FR-FO-08)</span>
                    <h3 class="text-base font-bold text-white tracking-tight">Tandai Pulang Tanpa Lapor</h3>
                </div>

                <button @click="closeAbandonedModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer" aria-label="Tutup Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
                
                <!-- Info Ringkas Acara -->
                <div class="bg-slate-50 p-3.5 rounded-brand border border-slate-200 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-semibold">Kode Permohonan:</span>
                        <span class="font-mono font-extrabold text-[#001A72]" x-text="abandonedTargetBooking.booking_code"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-semibold">Nama Acara:</span>
                        <span class="font-bold text-slate-900 truncate max-w-[220px]" x-text="abandonedTargetBooking.event_name"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-semibold">Pemilik KTP:</span>
                        <span class="font-bold text-slate-800" x-text="abandonedTargetBooking.ktp_holder_name || 'Panitia'"></span>
                    </div>
                    <template x-if="abandonedTargetBooking.storage_drawer_number">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-semibold">Laci Asal:</span>
                            <span class="font-bold text-indigo-700" x-text="'Laci Operasional ' + abandonedTargetBooking.storage_drawer_number"></span>
                        </div>
                    </template>
                </div>

                <!-- Status Sinyal DO & Implikasi Sanksi -->
                <template x-if="abandonedTargetBooking.clearance_status === 'incident_detected'">
                    <div class="p-3.5 rounded-brand bg-rose-50 border border-rose-300 text-xs space-y-2">
                        <div class="flex items-center gap-2 text-rose-900 font-extrabold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Kasus Ruangan Bermasalah (abandoned_incident)</span>
                        </div>
                        <p class="text-rose-800 text-[11px] leading-relaxed">
                            Inspeksi Duty Officer mencatat adanya insiden kerusakan/kebersihan. Menandai acara ini akan <strong class="font-bold underline">secara otomatis MEMBEKUKAN (SUSPEND) akun pemohon</strong> dari pengajuan booking di masa mendatang sampai ganti rugi diselesaikan.
                        </p>
                    </div>
                </template>

                <template x-if="abandonedTargetBooking.clearance_status !== 'incident_detected'">
                    <div class="p-3.5 rounded-brand bg-amber-50 border border-amber-300 text-xs space-y-1.5">
                        <div class="flex items-center gap-2 text-amber-900 font-extrabold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                            <span>Kasus Ruangan Bersih (abandoned_clean)</span>
                        </div>
                        <p class="text-amber-800 text-[11px] leading-relaxed">
                            Ruangan dinyatakan bersih/tanpa insiden oleh DO. Status acara akan diubah menjadi <strong class="font-bold">Ditinggal (Bersih)</strong> dan KTP fisik dipindahkan ke kotak arsip tertinggal.
                        </p>
                    </div>
                </template>

                <!-- Error Alert -->
                <template x-if="abandonedError">
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-brand text-xs text-rose-800 font-semibold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span x-text="abandonedError"></span>
                    </div>
                </template>

                <!-- Form Input -->
                <div class="space-y-3 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nomor Kotak Arsip KTP Tertinggal (Unclaimed Box ID) <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               x-model="unclaimedBoxId" 
                               placeholder="Contoh: BOX-01, KOTAK-ARSIP-A, KTP-2026-01" 
                               class="w-full px-3.5 py-2 rounded-brand border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-rose-800 focus:border-transparent">
                        <p class="text-[10px] text-slate-500 mt-1">
                            Pindahkan fisik kartu KTP dari laci operasional ke kotak arsip tertinggal, lalu catat nomor kotaknya di sini.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan Petugas FO (Opsional)
                        </label>
                        <textarea x-model="abandonedNotes" 
                                  rows="2" 
                                  placeholder="Contoh: Panitia telah dihubungi via WA pada pukul 17:30 namun tidak ada konfirmasi kedatangan..." 
                                  class="w-full px-3.5 py-2 rounded-brand border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-rose-800 focus:border-transparent"></textarea>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" 
                        @click="closeAbandonedModal()" 
                        class="px-4 py-2 rounded-brand bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        @click="submitMarkAbandoned()" 
                        :disabled="isSubmittingAbandoned || !unclaimedBoxId" 
                        class="px-4 py-2 rounded-brand bg-rose-700 hover:bg-rose-800 text-white font-bold text-xs transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
                    <svg x-show="!isSubmittingAbandoned" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    <svg x-show="isSubmittingAbandoned" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="isSubmittingAbandoned ? 'Menyimpan...' : 'Tandai Pulang Tanpa Lapor'"></span>
                </button>
            </div>

        </div>
    </div>
</div>
