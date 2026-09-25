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
            <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <h3 class="text-base font-bold text-white tracking-tight leading-snug truncate" x-text="activeItem.event_name"></h3>
                    
                    <template x-if="activeItem.urgency_label">
                        <div class="relative inline-flex items-center shrink-0"
                             x-data="{ showUrgentTooltip: false }"
                             @mouseenter="showUrgentTooltip = true"
                             @mouseleave="showUrgentTooltip = false">
                            <span class="inline-flex items-center justify-center p-1 rounded-full bg-rose-500 hover:bg-rose-600 text-white animate-pulse cursor-pointer shadow-xs"
                                  :title="activeItem.urgency_label">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>
                            </span>
                            <div x-show="showUrgentTooltip"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 translate-y-1"
                                 style="display: none;"
                                 class="absolute left-0 top-full mt-1.5 z-50 pointer-events-none drop-shadow-xl whitespace-nowrap">
                                <div class="bg-slate-900 text-white text-[10px] font-bold px-2.5 py-1 rounded shadow-lg border border-slate-700 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-ping"></span>
                                    <span x-text="activeItem.urgency_label"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <button @click="closeModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body: Tabs & Detail Breakdown -->
            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                
                <!-- Strip Metadata Permohonan & Status -->
                <div class="bg-slate-50 p-3.5 rounded-brand border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Kode Permohonan</span>
                            <span class="font-mono font-extrabold text-[#001A72] text-sm" x-text="activeItem.code"></span>
                        </div>
                        <div class="h-6 w-px bg-slate-200"></div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Diajukan Pada</span>
                            <span class="font-medium text-slate-700" x-text="activeItem.created_at"></span>
                        </div>
                    </div>

                    <div class="text-right space-y-1.5">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Status Kurasi</span>
                        <div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold inline-block shadow-2xs" 
                                  :class="activeItem.status_badge" 
                                  x-text="activeItem.status"></span>
                        </div>
                    </div>
                </div>

                <!-- Seksi 1: Evaluasi Jadwal Multi-Ruangan & Multi-Hari (REQ-MKT-05) -->
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            <span>1. Evaluasi Jadwal Multi-Ruangan &amp; Multi-Hari</span>
                        </h4>

                        <!-- Ringkasan Akumulasi Pemakaian Gedung -->
                        <div class="flex items-center gap-2 text-[11px]">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-indigo-50 text-[#001A72] font-bold border border-indigo-100">
                                <span x-text="(activeItem.slots_count || activeItem.slots.length) + ' Sesi'"></span>
                            </span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded bg-slate-100 text-slate-800 font-bold border border-slate-200">
                                <span class="text-slate-500 font-normal">Akumulasi:</span>
                                <strong x-text="activeItem.total_duration"></strong>
                            </span>
                        </div>
                    </div>

                    <!-- Warning Alert jika Overcapacity terdeteksi -->
                    <template x-if="activeItem.has_overcapacity">
                        <div class="p-3 rounded-brand bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-xs text-rose-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            <div>
                                <span class="font-extrabold text-rose-700 block text-[11px] uppercase tracking-wide">Peringatan Overcapacity Terdeteksi</span>
                                <p class="text-[11px] text-rose-800 leading-relaxed mt-0.5">
                                    Estimasi peserta (<strong x-text="activeItem.participants + ' orang'"></strong>) melebihi batas kapasitas maksimal salah satu ruangan terpilih. Kurator disarankan meminta revisi ruangan atau penyesuaian kuota peserta.
                                </p>
                            </div>
                        </div>
                    </template>

                    <!-- Tabel Terpadu Multi-Ruangan -->
                    <div class="border border-slate-200 rounded-brand overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 text-[11px]">
                                    <tr>
                                        <th class="px-3 py-2.5">Ruangan &amp; Lantai</th>
                                        <th class="px-3 py-2.5">Tanggal Sesi</th>
                                        <th class="px-3 py-2.5">Jam Acara</th>
                                        <th class="px-3 py-2.5">Sterilisasi (+30m)</th>
                                        <th class="px-3 py-2.5 text-right">Kapasitas Maksimal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(slot, idx) in activeItem.slots" :key="idx">
                                        <tr class="hover:bg-slate-50/80 transition-colors" :class="slot.is_overcapacity ? 'bg-rose-50/40' : ''">
                                            <td class="px-3 py-2.5 align-middle">
                                                <span class="font-bold text-slate-900 block" x-text="slot.room_name"></span>
                                                <span class="text-[10px] text-slate-500 font-semibold" x-text="slot.floor"></span>
                                            </td>
                                            <td class="px-3 py-2.5 align-middle font-medium text-slate-800" x-text="slot.date"></td>
                                            <td class="px-3 py-2.5 align-middle font-mono font-semibold text-[#001A72]" x-text="slot.time"></td>
                                            <td class="px-3 py-2.5 align-middle text-[11px] text-slate-600 font-mono" x-text="slot.buffer_time"></td>
                                            <td class="px-3 py-2.5 align-middle text-right">
                                                <div class="inline-flex items-center justify-end gap-1.5 font-semibold text-slate-800">
                                                    <span x-text="slot.capacity_formatted || (slot.capacity + ' Orang')"></span>
                                                    
                                                    <!-- Warning Icon jika Melebihi Batas (Overcapacity) -->
                                                    <template x-if="slot.is_overcapacity">
                                                        <div class="relative inline-flex items-center shrink-0"
                                                             x-data="{ showCapTooltip: false }"
                                                             @mouseenter="showCapTooltip = true"
                                                             @mouseleave="showCapTooltip = false">
                                                            <span class="inline-flex items-center justify-center p-0.5 rounded-full text-rose-600 bg-rose-100 animate-pulse cursor-pointer shadow-2xs"
                                                                  :title="'Overcapacity +' + slot.overcapacity_diff + ' orang'">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/></svg>
                                                            </span>
                                                            <div x-show="showCapTooltip"
                                                                 x-transition:enter="transition ease-out duration-150"
                                                                 x-transition:enter-start="opacity-0 translate-x-1"
                                                                 x-transition:enter-end="opacity-100 translate-x-0"
                                                                 x-transition:leave="transition ease-in duration-100"
                                                                 x-transition:leave-start="opacity-100 translate-x-0"
                                                                 x-transition:leave-end="opacity-0 translate-x-1"
                                                                 style="display: none;"
                                                                 class="absolute right-full top-1/2 -translate-y-1/2 mr-2 z-50 pointer-events-none drop-shadow-xl whitespace-nowrap">
                                                                <div class="bg-rose-900 text-white text-[10px] font-bold px-2 py-1 rounded shadow-lg border border-rose-700 flex items-center gap-1">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-ping"></span>
                                                                    <span x-text="'Kelebihan ' + slot.overcapacity_diff + ' orang'"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    <!-- Checkmark Icon jika Kapasitas Sesuai -->
                                                    <template x-if="!slot.is_overcapacity">
                                                        <span class="text-emerald-600 inline-flex items-center shrink-0" title="Kapasitas Memadai">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                        </span>
                                                    </template>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Akumulasi Durasi Gedung -->
                        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-600">
                            <span>Total akumulasi waktu acara: <strong class="text-slate-900" x-text="activeItem.total_duration"></strong></span>
                            <span>Total termasuk buffer sterilisasi: <strong class="text-[#001A72]" x-text="activeItem.total_duration_with_buffer"></strong></span>
                        </div>
                    </div>
                </div>

                <!-- Seksi 2: Data Pemohon & Integritas Rekam Jejak (REQ-MKT-04) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>2. Integritas Rekam Jejak &amp; Profil Pemohon</span>
                        </h4>
                    </div>

                    <div class="space-y-3">
                        <!-- Badge Integritas / Rekam Jejak Pemohon -->
                        <template x-if="activeItem.is_suspended">
                            <div class="p-3.5 rounded-brand bg-rose-50 border border-rose-200 flex items-start gap-3 text-xs text-rose-900">
                                <div class="w-7 h-7 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 font-bold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-extrabold text-rose-700 uppercase tracking-wide text-[11px]">Peringatan Kritis: Akun Ditangguhkan (Suspended)</span>
                                        <template x-if="activeItem.suspended_at">
                                            <span class="text-[10px] text-rose-600 font-medium" x-text="'Sejak ' + activeItem.suspended_at"></span>
                                        </template>
                                    </div>
                                    <p class="text-rose-800 text-[11px]" x-text="'Alasan: ' + (activeItem.suspension_reason || 'Pelanggaran ketentuan gedung terdahulu')"></p>
                                </div>
                            </div>
                        </template>

                        <template x-if="!activeItem.is_suspended && activeItem.incident_count > 0">
                            <div class="p-3 rounded-brand bg-amber-50 border border-amber-200 flex items-center gap-2.5 text-xs text-amber-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                <div>
                                    <span class="font-bold text-amber-800" x-text="'Catatan Insiden Gedung: Terdeteksi ' + activeItem.incident_count + ' insiden pada event terdahulu.'"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="!activeItem.is_suspended && (!activeItem.incident_count || activeItem.incident_count === 0)">
                            <div class="p-2.5 rounded-brand bg-emerald-50 border border-emerald-200 flex items-center justify-between gap-2 text-xs text-emerald-900">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <span class="font-bold text-emerald-800">Rekam Jejak Bersih (Clean Track Record)</span>
                                </div>
                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100/70 px-2 py-0.5 rounded-full">
                                    Status Akun Aktif &amp; Terverifikasi
                                </span>
                            </div>
                        </template>

                        <!-- Profil Legalitas & Kontak Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-brand border border-slate-200 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nama Lembaga / Pemohon:</span>
                                <span class="font-bold text-slate-900 text-sm block" x-text="activeItem.institution"></span>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-800" x-text="'Tipe Akun: ' + activeItem.account_type"></span>
                            </div>

                            <div>
                                <span class="text-slate-400 block text-[11px]">Alamat Lengkap / Domisili:</span>
                                <span class="font-medium text-slate-800 block leading-relaxed" x-text="activeItem.user_address"></span>
                            </div>

                            <div class="border-t border-slate-200/80 pt-2.5">
                                <span class="text-slate-400 block text-[11px]">Nama PIC Hari-H:</span>
                                <span class="font-bold text-slate-900" x-text="activeItem.pic_name"></span>
                                <span class="text-slate-500 text-[11px] block" x-text="'Akun: ' + activeItem.user_name"></span>
                            </div>

                            <div class="border-t border-slate-200/80 pt-2.5">
                                <span class="text-slate-400 block text-[11px]">Nomor WhatsApp PIC:</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="font-mono font-bold text-emerald-700" x-text="activeItem.pic_phone"></span>
                                    <template x-if="activeItem.pic_phone && activeItem.pic_phone !== '-'">
                                        <a :href="'https://wa.me/' + activeItem.pic_phone.replace(/[^0-9]/g, '')" 
                                           target="_blank" 
                                           class="text-[10px] font-bold text-emerald-700 hover:text-emerald-800 underline inline-flex items-center gap-0.5 ml-1">
                                            Chat PIC &rarr;
                                        </a>
                                    </template>
                                </div>
                                <span class="text-slate-500 text-[11px] block mt-0.5" x-text="'Email: ' + activeItem.user_email"></span>
                            </div>

                            <div class="border-t border-slate-200/80 pt-2.5">
                                <span class="text-slate-400 block text-[11px]">Media Sosial:</span>
                                <span class="font-medium text-slate-800" x-text="activeItem.social_media"></span>
                            </div>

                            <div class="border-t border-slate-200/80 pt-2.5">
                                <span class="text-slate-400 block text-[11px]">Website Resmi:</span>
                                <template x-if="activeItem.website && activeItem.website !== '-'">
                                    <a :href="activeItem.website.startsWith('http') ? activeItem.website : 'https://' + activeItem.website" 
                                       target="_blank" 
                                       class="font-medium text-[#001A72] hover:underline truncate block" 
                                       x-text="activeItem.website"></a>
                                </template>
                                <template x-if="!activeItem.website || activeItem.website === '-'">
                                    <span class="text-slate-500">-</span>
                                </template>
                            </div>
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
                            <span class="text-slate-500 block mb-1.5 font-bold text-[11px] flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m16 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16"/><line x1="12" x2="12" y1="12" y2="12"/></svg>
                                <span>Daftar Peralatan Eksternal Mandiri (Barang Bawaan Pemohon):</span>
                            </span>
                            <div class="bg-slate-50 p-3 rounded-brand border border-slate-200">
                                <p class="text-slate-800 leading-relaxed font-medium whitespace-pre-line" x-text="activeItem.equipment"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seksi 4: Berkas Proposal & Poster Kegiatan (REQ-MKT-06) -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#001A72] mb-2 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>4. Berkas Proposal &amp; Banner Poster Acara</span>
                    </h4>

                    <div class="space-y-3 bg-slate-50 p-4 rounded-brand border border-slate-200 text-xs">
                        <!-- Baris 1: Skema Pembiayaan & Nomor Surat -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pb-3 border-b border-slate-200">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Skema Penetapan Biaya:</span>
                                <span class="text-xs font-bold text-indigo-900" x-text="activeItem.pricing_scheme"></span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Nomor Surat Pengantar / Resmi:</span>
                                <span class="text-xs font-bold text-slate-800 font-mono" x-text="activeItem.official_letter || 'Tidak dicantumkan'"></span>
                            </div>
                        </div>

                        <!-- Baris 2: Berkas Proposal (Buka di Tab Baru) -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-brand border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-brand bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 border border-rose-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="10" x2="8" y1="12" y2="12"/><line x1="16" x2="8" y1="16" y2="16"/></svg>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Dokumen Proposal Acara (Format PDF)</span>
                                    <span class="text-[11px] text-slate-500 block">Dapat langsung ditinjau di tab peramban baru</span>
                                </div>
                            </div>

                            <template x-if="activeItem.proposal_url">
                                <a :href="activeItem.proposal_url" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-bold transition shadow-xs shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                                    <span>Buka Proposal di Tab Baru</span>
                                </a>
                            </template>

                            <template x-if="!activeItem.proposal_url">
                                <span class="text-[11px] text-slate-400 font-semibold italic">Tidak ada berkas proposal</span>
                            </template>
                        </div>

                        <!-- Baris 3: Banner Poster Acara (Buka di Tab Baru) -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-white rounded-brand border border-slate-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-brand bg-indigo-50 text-[#001A72] flex items-center justify-center shrink-0 border border-indigo-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Banner / Poster Acara (Format Gambar)</span>
                                    <span class="text-[11px] text-slate-500 block">Pratinjau visual poster pendukung kegiatan</span>
                                </div>
                            </div>

                            <template x-if="activeItem.poster_url">
                                <a :href="activeItem.poster_url" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-brand bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold transition shadow-2xs shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                                    <span>Buka Poster di Tab Baru</span>
                                </a>
                            </template>

                            <template x-if="!activeItem.poster_url">
                                <span class="text-[11px] text-slate-400 font-semibold italic">Tidak melampirkan poster</span>
                            </template>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer: Action Button Row -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 flex flex-wrap items-center justify-end gap-2.5">
                <button type="button" 
                        @click="closeModal()" 
                        class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                    Tutup
                </button>

                <!-- Tombol Tolak Proposal (Hanya tampil jika belum final) -->
                <template x-if="activeItem.status_raw === 'awaiting_marketing' || activeItem.status_raw === 'revised'">
                    <button type="button"
                            @click="openReject()"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        <span>Tolak Proposal</span>
                    </button>
                </template>

                <!-- Tombol Minta Revisi (Hanya tampil jika belum final) -->
                <template x-if="activeItem.status_raw === 'awaiting_marketing'">
                    <button type="button"
                            @click="openRevise()"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-4 py-2 rounded-brand bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        <span>Minta Revisi</span>
                    </button>
                </template>

                <!-- Tombol Setujui Proposal (REQ-MKT-07) -->
                <template x-if="activeItem.status_raw === 'awaiting_marketing' || activeItem.status_raw === 'revised'">
                    <button type="button"
                            @click="openApproval()"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-5 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>Setujui Proposal</span>
                    </button>
                </template>

                <!-- Tombol Konfirmasi Pembayaran Kasir (REQ-MKT-10) -->
                <template x-if="activeItem.status_raw === 'awaiting_payment'">
                    <button type="button"
                            @click="openPayment()"
                            class="min-h-[44px] inline-flex items-center gap-1.5 px-5 py-2 rounded-brand bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="m14 15 2 2 4-4"/></svg>
                        <span>Konfirmasi Pembayaran Diterima</span>
                    </button>
                </template>
            </div>

        </div>
    </div>
</div>

<!-- ==================================================================== -->
<!-- MODAL PERSETUJUAN & PENETAPAN SKEMA PEMBIAYAAN (REQ-MKT-07) -->
<!-- ==================================================================== -->
<div x-show="showApprovalModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="approval-modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showApprovalModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeApprovalModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Box -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showApprovalModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-[#001A72] text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-emerald-400 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <div>
                        <h3 id="approval-modal-title" class="text-sm font-bold text-white tracking-tight leading-snug">
                            Persetujuan Kurasi &amp; Penetapan Biaya
                        </h3>
                        <p class="text-[11px] text-slate-200 font-mono" x-text="activeItem.code + ' &bull; ' + activeItem.institution"></p>
                    </div>
                </div>

                <button @click="closeApprovalModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Dialog">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Approval Form Content -->
            <form :action="'/marketing/bookings/' + activeItem.id + '/approve'" 
                  method="POST" 
                  @submit="approvalForm.isSubmitting = true" 
                  class="p-6 space-y-5">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- Form Introduction / Summary -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-brand text-xs text-slate-700 space-y-1">
                    <span class="font-extrabold text-slate-900 block truncate" x-text="activeItem.event_name"></span>
                    <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500 font-medium">
                        <span x-text="'Kategori: ' + activeItem.event_category"></span>
                        <span>&bull;</span>
                        <span x-text="'Sub-Sektor: ' + activeItem.ekraf_subsector"></span>
                        <span>&bull;</span>
                        <span x-text="activeItem.participants + ' Peserta'"></span>
                    </div>
                </div>

                <!-- Skema Pembiayaan Selection (Radio Group) -->
                <div class="space-y-2.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                        Pilih Skema Pembiayaan Resmi <span class="text-rose-500">*</span>
                    </label>

                    <!-- Option 1: Skema Subsidi Pemkot Malang -->
                    <label class="relative flex items-start p-3.5 rounded-brand border-2 cursor-pointer transition-all"
                           :class="approvalForm.pricing_scheme === 'subsidy' ? 'border-[#001A72] bg-indigo-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" 
                               name="pricing_scheme" 
                               value="subsidy" 
                               x-model="approvalForm.pricing_scheme" 
                               class="mt-1 h-4 w-4 text-[#001A72] border-slate-300 focus:ring-[#001A72]">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-extrabold text-xs text-slate-900">
                                    Skema Subsidi Pemkot Malang
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Gratis Rp 0.00
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                                Untuk kegiatan Komunitas Ekraf &amp; Non-Komersial.
                            </p>
                        </div>
                    </label>

                    <!-- Option 2: Skema Komersial / Berbayar -->
                    <label class="relative flex items-start p-3.5 rounded-brand border-2 cursor-pointer transition-all"
                           :class="approvalForm.pricing_scheme === 'commercial' ? 'border-[#001A72] bg-indigo-50/40 shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'">
                        <input type="radio" 
                               name="pricing_scheme" 
                               value="commercial" 
                               x-model="approvalForm.pricing_scheme" 
                               class="mt-1 h-4 w-4 text-[#001A72] border-slate-300 focus:ring-[#001A72]">
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-extrabold text-xs text-slate-900">
                                    Skema Komersial / Berbayar
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                    Retribusi Daerah
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                                Dikenakan tarif retribusi sesuai Perda Kota Malang.
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Dynamic Detail Box for Subsidi -->
                <template x-if="approvalForm.pricing_scheme === 'subsidy'">
                    <div class="p-3.5 rounded-brand bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-1.5">
                        <div class="flex items-center gap-2 font-bold text-emerald-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span>Dampak Keputusan Subsidi:</span>
                        </div>
                        <ul class="text-[11px] text-emerald-800 space-y-1 pl-5 list-disc">
                            <li>Nominal otomatis disetel menjadi <strong>Rp 0.00</strong>.</li>
                            <li>Status permohonan langsung berpindah menjadi <strong class="text-emerald-900 font-extrabold">Disetujui (Confirmed)</strong>.</li>
                            <li>Sistem otomatis mengaktifkan <strong>penerbitan E-Pass digital &amp; QR Code</strong> validasi hari-H untuk pemohon.</li>
                        </ul>
                    </div>
                </template>

                <!-- Dynamic Fields for Komersial -->
                <template x-if="approvalForm.pricing_scheme === 'commercial'">
                    <div class="space-y-4 pt-1">
                        <!-- Nominal Amount Input -->
                        <div>
                            <label for="modal_nominal_amount" class="block text-xs font-bold text-slate-800 mb-1">
                                Nominal Tarif Retribusi (Rupiah) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-brand">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-slate-500 font-bold text-xs">Rp</span>
                                </div>
                                <input type="number" 
                                       id="modal_nominal_amount" 
                                       name="nominal_amount" 
                                       x-model="approvalForm.nominal_amount" 
                                       required 
                                       min="0" 
                                       step="1000" 
                                       placeholder="Contoh: 1500000"
                                       class="w-full pl-10 pr-3 py-2 text-xs font-semibold rounded-brand border border-slate-300 focus:border-[#001A72] focus:ring-1 focus:ring-[#001A72] outline-none">
                            </div>
                            <template x-if="approvalForm.nominal_amount && approvalForm.nominal_amount > 0">
                                <p class="text-[11px] text-[#001A72] font-semibold mt-1" x-text="'Terbilang: Rp ' + formatRupiah(approvalForm.nominal_amount)"></p>
                            </template>
                        </div>

                        <!-- Optional: Payment Notes / Koordinasi WhatsApp -->
                        <div>
                            <label for="modal_payment_notes" class="block text-xs font-bold text-slate-800 mb-1">
                                Catatan / Instruksi Pembayaran &amp; Koordinasi WhatsApp (Opsional)
                            </label>
                            <textarea id="modal_payment_notes" 
                                      name="payment_notes" 
                                      x-model="approvalForm.payment_notes" 
                                      rows="2" 
                                      placeholder="Contoh: Rincian tarif dan tata cara penyetoran retribusi daerah akan dikonfirmasikan lebih lanjut oleh tim Marketing MCC melalui WhatsApp PIC."
                                      class="w-full p-2.5 text-xs rounded-brand border border-slate-300 focus:border-[#001A72] focus:ring-1 focus:ring-[#001A72] outline-none"></textarea>
                            <p class="text-[11px] text-slate-500 mt-1">
                                Pembahasan detail mekanisme pembayaran dapat langsung dikoordinasikan secara fleksibel bersama pemohon melalui kontak WhatsApp.
                            </p>
                        </div>

                        <!-- Info Alert Komersial -->
                        <div class="p-3.5 rounded-brand bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1.5">
                            <div class="flex items-center gap-2 font-bold text-amber-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>Dampak Keputusan Komersial:</span>
                            </div>
                            <ul class="text-[11px] text-amber-800 space-y-1 pl-5 list-disc">
                                <li>Status permohonan berpindah menjadi <strong class="text-amber-900 font-extrabold">Menunggu Pembayaran (Awaiting Payment)</strong>.</li>
                                <li>Hak seluruh slot jadwal di database <strong>tetap aman terikat</strong> (tidak dapat dipesan pengguna lain).</li>
                                <li>Penerbitan <strong>E-Pass &amp; QR Code ditahan sementara</strong> hingga bukti pembayaran divalidasi oleh Marketing / Kasir.</li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- Optional: Approval / Curator Notes -->
                <div>
                    <label for="modal_approval_notes" class="block text-xs font-bold text-slate-800 mb-1">
                        Catatan Internal Kurator (Opsional)
                    </label>
                    <textarea id="modal_approval_notes" 
                              name="approval_notes" 
                              x-model="approvalForm.approval_notes" 
                              rows="2" 
                              placeholder="Catatan tambahan hasil kurasi tim marketing..."
                              class="w-full p-2 text-xs rounded-brand border border-slate-300 focus:border-[#001A72] focus:ring-1 focus:ring-[#001A72] outline-none"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="closeApprovalModal()" 
                            class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="approvalForm.isSubmitting"
                            class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white text-xs font-bold transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="approvalForm.isSubmitting">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!approvalForm.isSubmitting">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </template>
                        <span>Konfirmasi Persetujuan Proposal</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ==================================================================== -->
<!-- MODAL PERMINTAAN REVISI PROPOSAL (REQ-MKT-08) -->
<!-- ==================================================================== -->
<div x-show="showReviseModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="revise-modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showReviseModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeReviseModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Box -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showReviseModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-indigo-900 text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-indigo-300 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                    </div>
                    <div>
                        <h3 id="revise-modal-title" class="text-sm font-bold text-white tracking-tight leading-snug">
                            Permintaan Revisi Dokumen Proposal
                        </h3>
                        <p class="text-[11px] text-indigo-200 font-mono" x-text="activeItem.code + ' &bull; ' + activeItem.institution"></p>
                    </div>
                </div>

                <button @click="closeReviseModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Dialog">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Revise Form Content -->
            <form :action="'/marketing/bookings/' + activeItem.id + '/revise'" 
                  method="POST" 
                  @submit="reviseForm.isSubmitting = true" 
                  class="p-6 space-y-5">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- Context Information Alert -->
                <div class="p-3.5 rounded-brand bg-indigo-50 border border-indigo-200 text-xs text-indigo-950 space-y-1.5">
                    <div class="flex items-center gap-2 font-bold text-indigo-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Ketentuan Permintaan Revisi (REQ-MKT-08):</span>
                    </div>
                    <ul class="text-[11px] text-indigo-900 space-y-1 pl-5 list-disc">
                        <li>Digunakan jika substansi acara layak namun terdapat <strong>kekurangan administratif minor</strong> pada berkas proposal.</li>
                        <li>Status permohonan akan menjadi <strong class="text-indigo-950 font-bold">Perlu Revisi (Revised)</strong>.</li>
                        <li>Sistem otomatis menetapkan <strong>tenggat waktu 24 jam</strong> (<em>grace period</em>) bagi pemohon untuk mengunggah berkas perbaikan.</li>
                        <li>Hak seluruh slot jadwal di database <strong>tetap aman terikat</strong> dan tidak dapat direbut oleh pemesan lain selama masa 24 jam.</li>
                    </ul>
                </div>

                <!-- Input Revision Notes -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="modal_revision_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                            Catatan Evaluasi Revisi Spesifik <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-mono font-semibold"
                              :class="reviseForm.revision_notes.length >= 20 ? 'text-emerald-700' : 'text-slate-400'"
                              x-text="reviseForm.revision_notes.length + ' / min 20 karakter'"></span>
                    </div>
                    <textarea id="modal_revision_notes" 
                              name="revision_notes" 
                              x-model="reviseForm.revision_notes" 
                              required 
                              rows="4" 
                              minlength="20"
                              maxlength="2000"
                              placeholder="Tuliskan kekurangan dokumen atau revisi teknis yang perlu diperbaiki oleh pemohon (contoh: 'Mohon lampirkan rundown acara terperinci dan surat pengantar resmi bertandatangan pimpinan pada berkas proposal')..."
                              class="w-full p-3 text-xs rounded-brand border border-slate-300 focus:border-[#001A72] focus:ring-1 focus:ring-[#001A72] outline-none leading-relaxed"></textarea>
                    <template x-if="reviseForm.revision_notes.length > 0 && reviseForm.revision_notes.length < 20">
                        <p class="text-[11px] text-rose-600 font-semibold">
                            Catatan terlalu pendek. Harap berikan instruksi perbaikan yang jelas dan spesifik (minimal 20 karakter).
                        </p>
                    </template>
                </div>

                <!-- Footer Buttons -->
                <div class="border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="closeReviseModal()" 
                            class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="reviseForm.isSubmitting || reviseForm.revision_notes.trim().length < 20"
                            class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2 rounded-brand bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-bold transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="reviseForm.isSubmitting">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!reviseForm.isSubmitting">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-indigo-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                        </template>
                        <span>Kirim Permintaan Revisi (24 Jam)</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ==================================================================== -->
<!-- MODAL PENOLAKAN PERMANEN PROPOSAL (REQ-MKT-09) -->
<!-- ==================================================================== -->
<div x-show="showRejectModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="reject-modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showRejectModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeRejectModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Box -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showRejectModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-rose-700 text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-rose-200 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                    </div>
                    <div>
                        <h3 id="reject-modal-title" class="text-sm font-bold text-white tracking-tight leading-snug">
                            Penolakan Permanen Permohonan Event
                        </h3>
                        <p class="text-[11px] text-rose-100 font-mono" x-text="activeItem.code + ' &bull; ' + activeItem.institution"></p>
                    </div>
                </div>

                <button @click="closeRejectModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Dialog">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Reject Form Content -->
            <form :action="'/marketing/bookings/' + activeItem.id + '/reject'" 
                  method="POST" 
                  @submit="rejectForm.isSubmitting = true" 
                  class="p-6 space-y-5">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- Context Information Alert -->
                <div class="p-3.5 rounded-brand bg-rose-50 border border-rose-200 text-xs text-rose-950 space-y-1.5">
                    <div class="flex items-center gap-2 font-bold text-rose-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Dampak Keputusan Penolakan Permanen (REQ-MKT-09):</span>
                    </div>
                    <ul class="text-[11px] text-rose-900 space-y-1 pl-5 list-disc">
                        <li>Digunakan jika kegiatan bertentangan dengan norma, melanggar regulasi gedung, atau di luar 17 sub-sektor ekraf.</li>
                        <li>Status permohonan akan seketika berubah menjadi <strong class="text-rose-950 font-bold">Ditolak (Rejected)</strong>.</li>
                        <li><strong>Pelepasan Slot Otomatis:</strong> Seluruh slot ruangan terkait otomatis dinonaktifkan (<em>is_active = false</em>) sehingga langsung terbuka kembali di kalender publik.</li>
                        <li>Keputusan ini bersifat permanen dan mengakhiri proses triase permohonan.</li>
                    </ul>
                </div>

                <!-- Input Rejection Category (Dropdown / Select) -->
                <div class="space-y-1.5">
                    <label for="modal_rejection_category" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                        Kategori Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <select id="modal_rejection_category" 
                            name="rejection_category" 
                            x-model="rejectForm.rejection_category" 
                            required 
                            class="w-full p-2.5 text-xs font-semibold rounded-brand border border-slate-300 bg-white focus:border-rose-600 focus:ring-1 focus:ring-rose-600 outline-none">
                        <option value="regulasi_gedung">Melanggar Regulasi &amp; Tata Tertib Gedung MCC</option>
                        <option value="di_luar_ekraf">Di Luar 17 Sub-Sektor Ekonomi Kreatif (Non-Ekraf)</option>
                        <option value="norma_ketertiban">Bertentangan dengan Norma Sosial / Ketertiban Umum</option>
                        <option value="overcapacity_ekstrem">Kapasitas &amp; Teknis Berisiko Ekstrem (Overcapacity)</option>
                        <option value="lainnya">Alasan Teknis / Operasional Lainnya</option>
                    </select>
                </div>

                <!-- Input Rejection Notes (Textarea) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="modal_rejection_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                            Uraian Catatan Penolakan Tertulis <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] font-mono font-semibold"
                              :class="rejectForm.rejection_notes.length >= 20 ? 'text-emerald-700' : 'text-slate-400'"
                              x-text="rejectForm.rejection_notes.length + ' / min 20 karakter'"></span>
                    </div>
                    <textarea id="modal_rejection_notes" 
                              name="rejection_notes" 
                              x-model="rejectForm.rejection_notes" 
                              required 
                              rows="4" 
                              minlength="20"
                              maxlength="2000"
                              placeholder="Uraikan secara jelas dan tertulis alasan penolakan permohonan ini untuk catatan evaluasi dan transparansi (contoh: 'Kegiatan yang diajukan tidak termasuk dalam 17 sub-sektor ekonomi kreatif yang difasilitasi oleh Gedung MCC dan berpotensi mengganggu aktivitas ruangan lain')..."
                              class="w-full p-3 text-xs rounded-brand border border-slate-300 focus:border-rose-600 focus:ring-1 focus:ring-rose-600 outline-none leading-relaxed"></textarea>
                    <template x-if="rejectForm.rejection_notes.length > 0 && rejectForm.rejection_notes.length < 20">
                        <p class="text-[11px] text-rose-600 font-semibold">
                            Uraian alasan terlalu pendek. Harap berikan alasan penolakan yang rinci dan jelas (minimal 20 karakter).
                        </p>
                    </template>
                </div>

                <!-- Footer Buttons -->
                <div class="border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="closeRejectModal()" 
                            class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="rejectForm.isSubmitting || rejectForm.rejection_notes.trim().length < 20"
                            class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2 rounded-brand bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="rejectForm.isSubmitting">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!rejectForm.isSubmitting">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        </template>
                        <span>Konfirmasi Penolakan Permanen</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ==================================================================== -->
<!-- MODAL VERIFIKASI PEMBAYARAN KASIR (REQ-MKT-10) -->
<!-- ==================================================================== -->
<div x-show="showPaymentModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="payment-modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="showPaymentModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closePaymentModal()" 
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <!-- Centered Modal Box -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6 relative z-10 pointer-events-none">
        <div x-show="showPaymentModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.stop
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-xl border border-slate-200 pointer-events-auto my-8">
            
            <!-- Modal Header -->
            <div class="bg-purple-900 text-white px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-purple-300 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/><path d="m14 15 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 id="payment-modal-title" class="text-sm font-bold text-white tracking-tight leading-snug">
                            Verifikasi &amp; Konfirmasi Pembayaran Kasir
                        </h3>
                        <p class="text-[11px] text-purple-200 font-mono" x-text="activeItem.code + ' &bull; ' + activeItem.institution"></p>
                    </div>
                </div>

                <button @click="closePaymentModal()" class="p-1.5 rounded-brand text-white/80 hover:text-white hover:bg-white/10 transition cursor-pointer shrink-0" aria-label="Tutup Dialog">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>

            <!-- Payment Confirmation Form Content -->
            <form :action="'/marketing/bookings/' + activeItem.id + '/confirm-payment'" 
                  method="POST" 
                  @submit="paymentForm.isSubmitting = true" 
                  class="p-6 space-y-5">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <!-- Summary Box Tagihan -->
                <div class="p-4 bg-purple-50 border border-purple-200 rounded-brand text-xs text-purple-950 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Total Tagihan Retribusi Gedung:</span>
                        <span class="font-mono font-black text-sm text-purple-900" x-text="'Rp ' + formatRupiah(activeItem.nominal_amount)"></span>
                    </div>
                    <div class="border-t border-purple-200/80 pt-1.5 text-[11px] text-purple-900 space-y-0.5">
                        <span class="font-bold block">Acara: <span class="font-semibold text-slate-800" x-text="activeItem.event_name"></span></span>
                        <template x-if="activeItem.payment_notes">
                            <span class="block text-slate-600">Instruksi Bayar: <span class="italic" x-text="activeItem.payment_notes"></span></span>
                        </template>
                    </div>
                </div>

                <!-- Info Alert Dampak Verifikasi -->
                <div class="p-3.5 rounded-brand bg-emerald-50 border border-emerald-200 text-xs text-emerald-950 space-y-1.5">
                    <div class="flex items-center gap-2 font-bold text-emerald-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>Konsekuensi Validasi Pembayaran (REQ-MKT-10):</span>
                    </div>
                    <ul class="text-[11px] text-emerald-900 space-y-1 pl-5 list-disc">
                        <li>Status permohonan berpindah dari <strong class="text-amber-900">awaiting_payment</strong> menjadi <strong class="text-emerald-900 font-extrabold">Disetujui Sah (Confirmed)</strong>.</li>
                        <li>Sistem otomatis mencatat timestamp bayar (<em>paid_at = NOW()</em>) dan identitas kurator/kasir penyetuju.</li>
                        <li>Seketika menerbitkan <strong>E-Pass digital &amp; Dynamic QR Code</strong> validasi hari-H untuk pemohon.</li>
                    </ul>
                </div>

                <!-- Input Nomor Referensi Transaksi / Kuitansi STS -->
                <div class="space-y-1.5">
                    <label for="modal_payment_reference" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                        Nomor Referensi Transaksi / No. Kuitansi STS <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="modal_payment_reference" 
                           name="payment_reference" 
                           x-model="paymentForm.payment_reference" 
                           required 
                           minlength="3" 
                           maxlength="100" 
                           placeholder="Contoh: STS-MCC-2026/09/001 atau NOREF-BJM-988776655" 
                           class="w-full p-2.5 text-xs font-mono font-bold rounded-brand border border-slate-300 focus:border-purple-700 focus:ring-1 focus:ring-purple-700 outline-none">
                    <p class="text-[11px] text-slate-500">
                        Masukkan nomor kuitansi STS (Surat Tanda Setor) Kas Daerah atau nomor referensi transfer bank yang valid.
                    </p>
                </div>

                <!-- Optional: Notes Kasir / Petugas -->
                <div class="space-y-1">
                    <label for="modal_kasir_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                        Catatan Kasir / Validasi Tambahan (Opsional)
                    </label>
                    <textarea id="modal_kasir_notes" 
                              name="payment_notes" 
                              x-model="paymentForm.payment_notes" 
                              rows="2" 
                              placeholder="Contoh: Pembayaran telah dikonfirmasi masuk ke rekening Kas Daerah BPKAD Kota Malang pada tanggal 25/09/2026 pukul 10:15 WIB." 
                              class="w-full p-2 text-xs rounded-brand border border-slate-300 focus:border-purple-700 focus:ring-1 focus:ring-purple-700 outline-none leading-relaxed"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div class="border-t border-slate-200 pt-4 flex items-center justify-end gap-2.5">
                    <button type="button" 
                            @click="closePaymentModal()" 
                            class="min-h-[44px] px-4 py-2 rounded-brand border border-slate-300 bg-white text-slate-700 text-xs font-bold hover:bg-slate-100 transition cursor-pointer shadow-2xs">
                        Batal
                    </button>

                    <button type="submit" 
                            :disabled="paymentForm.isSubmitting || paymentForm.payment_reference.trim().length < 3" 
                            class="min-h-[44px] inline-flex items-center gap-2 px-5 py-2 rounded-brand bg-purple-700 hover:bg-purple-800 text-white text-xs font-bold transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="paymentForm.isSubmitting">
                            <svg class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <template x-if="!paymentForm.isSubmitting">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        </template>
                        <span>Konfirmasi Pembayaran Lunas</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
