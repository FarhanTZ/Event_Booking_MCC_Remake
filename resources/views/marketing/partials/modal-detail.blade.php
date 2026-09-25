<!-- ==================================================================== -->
<!-- MODAL REVIEW PROPOSAL & AKSI TRIASE (CENTERED IN VIEWPORT) -->
<!-- ==================================================================== -->
<div x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Wrapper -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-3xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-mono font-bold text-white text-sm bg-white/10 px-2 py-0.5 rounded border border-white/20" x-text="activeItem.code"></span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" 
                              :class="activeItem.status_badge" 
                              x-text="activeItem.status"></span>
                        <template x-if="activeItem.urgency_label">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-600 text-white animate-pulse" 
                                  x-text="activeItem.urgency_label"></span>
                        </template>
                    </div>
                    <h3 class="text-base font-bold text-white mt-1" x-text="activeItem.event_name"></h3>
                </div>
                <button @click="closeModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body: Tabs & Detail Breakdown -->
            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                
                <!-- Seksi 1: Ringkasan Ruangan & Jadwal -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                        <span>1. Jadwal &amp; Ruangan Terpilih</span>
                    </h4>
                    <div class="border border-slate-200 rounded-brand overflow-hidden">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="px-3 py-2">Ruangan</th>
                                    <th class="px-3 py-2">Lantai</th>
                                    <th class="px-3 py-2">Tanggal Sesi</th>
                                    <th class="px-3 py-2">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="(slot, idx) in activeItem.slots" :key="idx">
                                    <tr>
                                        <td class="px-3 py-2 font-bold text-slate-900" x-text="slot.room_name"></td>
                                        <td class="px-3 py-2 text-slate-600" x-text="slot.floor"></td>
                                        <td class="px-3 py-2 font-semibold text-[#001A72]" x-text="slot.date"></td>
                                        <td class="px-3 py-2 font-mono text-slate-800" x-text="slot.time"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Seksi 2: Data Pemohon & PIC -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>2. Data Pemohon &amp; Kontak PIC</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-brand border border-slate-200 text-xs">
                        <div>
                            <span class="text-slate-400 block">Lembaga / Pemohon:</span>
                            <span class="font-bold text-slate-900" x-text="activeItem.institution"></span>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800" x-text="'Tipe: ' + activeItem.account_type"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Email Akun:</span>
                            <span class="font-bold text-slate-900" x-text="activeItem.user_email"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nama PIC Hari-H:</span>
                            <span class="font-bold text-slate-900" x-text="activeItem.pic_name"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Nomor WhatsApp PIC:</span>
                            <span class="font-mono font-bold text-emerald-700" x-text="activeItem.pic_phone"></span>
                        </div>
                    </div>
                </div>

                <!-- Seksi 3: Rincian Event & Ekraf -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                        <span>3. Rincian Event &amp; Kurasi Ekraf</span>
                    </h4>
                    <div class="space-y-3 bg-white p-4 rounded-brand border border-slate-200 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <span class="text-slate-400 block">Kategori Event:</span>
                                <span class="font-bold text-slate-900" x-text="activeItem.event_category"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Sub-Sektor Ekraf:</span>
                                <span class="font-bold text-[#001A72]" x-text="activeItem.ekraf_subsector"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Estimasi Peserta:</span>
                                <span class="font-bold text-slate-900" x-text="activeItem.participants + ' Orang'"></span>
                            </div>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Deskripsi Kegiatan:</span>
                            <p class="text-slate-700 bg-slate-50 p-3 rounded border border-slate-100 leading-relaxed whitespace-pre-line" x-text="activeItem.description"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-1">Peralatan Mandiri yang Dibawa:</span>
                            <p class="text-slate-700 bg-slate-50 p-2.5 rounded border border-slate-100" x-text="activeItem.equipment"></p>
                        </div>
                    </div>
                </div>

                <!-- Seksi 4: Berkas Proposal -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>4. Berkas Proposal &amp; Skema Pembiayaan</span>
                    </h4>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 bg-slate-50 rounded-brand border border-slate-200">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Skema Penetapan Biaya:</span>
                            <span class="text-xs font-semibold text-indigo-700" x-text="activeItem.pricing_scheme"></span>
                        </div>
                        <template x-if="activeItem.proposal_url">
                            <a :href="activeItem.proposal_url" 
                               target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                <span>Buka Berkas Proposal (PDF)</span>
                            </a>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Modal Footer: Action Button Row -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-xs text-slate-500 w-full sm:w-auto">
                    <span class="inline-block w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span>Kode: <strong class="font-mono text-slate-800" x-text="activeItem.code"></strong></span>
                </div>

                <!-- Action Buttons: Destructive, Secondary, Primary CTA -->
                <div class="flex flex-wrap items-center justify-end gap-2.5 w-full sm:w-auto">
                    <button type="button" 
                            @click="closeModal()" 
                            class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                        Tutup
                    </button>

                    <button type="button"
                            @click="alert('Fitur kurasi: Tolak Proposal ' + activeItem.code)"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        <span>Tolak Proposal</span>
                    </button>

                    <button type="button"
                            @click="alert('Fitur kurasi: Minta Revisi ' + activeItem.code)"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        <span>Minta Revisi</span>
                    </button>

                    <button type="button"
                            @click="alert('Fitur kurasi: Setujui Proposal ' + activeItem.code)"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-5 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Setujui Proposal</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
