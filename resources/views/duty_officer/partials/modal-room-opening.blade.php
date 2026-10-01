<!-- ==================================================================== -->
<!-- MOBILE BOTTOM SHEET: PENDAMPINGAN CHECK-IN & PEMBUKAAN AKSES RUANGAN (FR-DO-02) -->
<!-- ==================================================================== -->
<div x-show="openingModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-opening-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="openingModalOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="openingModalOpen = false"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-full items-end justify-center text-center sm:items-center p-0 sm:p-4">
        <div x-show="openingModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-t-3xl sm:rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 max-h-[92vh] flex flex-col">
            
            <form :action="openingActionUrl" method="POST" class="flex flex-col h-full min-h-0">
                @csrf

                <!-- Drag Handle Bar for Mobile Touch Feel -->
                <div class="pt-3 pb-1 flex justify-center sm:hidden bg-[#001A72] cursor-pointer" @click="openingModalOpen = false">
                    <div class="w-12 h-1.5 rounded-full bg-white/30"></div>
                </div>

                <!-- Modal Header -->
                <div class="bg-gradient-to-br from-[#001A72] to-[#001250] text-white p-4 sm:p-5 flex items-start justify-between gap-3 shrink-0">
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white" x-text="activeTask.bookingCode">
                                #MCC-BOOKING
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-400 text-slate-950">
                                FR-DO-02: Buka Ruangan
                            </span>
                        </div>
                        <h3 class="text-base font-black text-white tracking-tight leading-snug line-clamp-1" x-text="`Buka &amp; Serah Terima: ${activeTask.roomName}`">
                            Pembukaan Ruangan
                        </h3>
                        <p class="text-xs text-indigo-200 mt-0.5 truncate" x-text="`${activeTask.eventName}`">
                            Nama Acara
                        </p>
                    </div>

                    <button type="button" 
                            @click="openingModalOpen = false"
                            class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-4 sm:p-5 space-y-3.5 overflow-y-auto custom-scrollbar flex-1 text-slate-800">
                    
                    <!-- Alert Notice Penting -->
                    <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-2.5 text-xs text-amber-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <p class="text-[11px] text-amber-800 leading-snug">
                            Pastikan panitia/PIC hadir di lokasi. Lakukan verifikasi fisik fasilitas bersama panitia sebelum serah terima pintu ruangan.
                        </p>
                    </div>

                    <!-- Checklist Kesiapan Fasilitas Dasar -->
                    <div class="space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                            Checklist Verifikasi Fisik Lapangan:
                        </span>

                        <div class="space-y-2">
                            <!-- AC -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[ac]" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-black text-slate-800 block">Pendingin Ruangan (AC)</span>
                                    <span class="text-[10px] text-slate-500">AC menyala dingin &amp; remote tersedia</span>
                                </div>
                            </label>

                            <!-- Kelistrikan & Audio -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[electricity]" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-black text-slate-800 block">Kelistrikan &amp; Sound System</span>
                                    <span class="text-[10px] text-slate-500">Stop kontak aktif &amp; mic / audio berfungsi</span>
                                </div>
                            </label>

                            <!-- Proyektor & Display -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[projector]" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-black text-slate-800 block">Proyektor / Display Screen</span>
                                    <span class="text-[10px] text-slate-500">Kabel HDMI &amp; layar proyektor berfungsi</span>
                                </div>
                            </label>

                            <!-- Layout Meja & Kursi -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[furniture_layout]" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-black text-slate-800 block">Layout Meja &amp; Kursi</span>
                                    <span class="text-[10px] text-slate-500">Susunan perabotan sesuai pesanan</span>
                                </div>
                            </label>

                            <!-- Kebersihan Awal -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[cleanliness]" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-black text-slate-800 block">Kebersihan &amp; Kerapihan</span>
                                    <span class="text-[10px] text-slate-500">Lantai bersih &amp; bebas sampah sesi sebelumnya</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Peralatan Eksternal Bawaan Pemohon -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                            Peralatan Bawaan Pemohon:
                        </span>
                        <template x-if="activeTask.equipmentItems && activeTask.equipmentItems.length > 0">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="item in activeTask.equipmentItems" :key="item">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-[#EEF2FF] border border-indigo-100 text-xs font-semibold text-[#001A72]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#001A72]"></span>
                                        <span x-text="item"></span>
                                    </span>
                                </template>
                            </div>
                        </template>
                        <template x-if="!activeTask.equipmentItems || activeTask.equipmentItems.length === 0">
                            <p class="text-xs text-slate-400 italic">Tidak membawa peralatan eksternal tambahan.</p>
                        </template>
                    </div>

                    <!-- Catatan Serah Terima (Opsional) -->
                    <div class="space-y-1 pt-2 border-t border-slate-100">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">Catatan Serah Terima (Opsional):</label>
                        <textarea name="notes" 
                                  rows="2" 
                                  placeholder="Contoh: AC diset 22°C, panitia bawa 2 mic wireless sendiri..." 
                                  class="w-full px-3 py-2 text-xs rounded-2xl border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
                    </div>

                </div>

                <!-- Modal Footer Actions (Sticky Bottom) -->
                <div class="bg-slate-50 p-3.5 border-t border-slate-200 flex flex-col gap-2 shrink-0">
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-2xl bg-[#001A72] hover:bg-[#001250] text-white font-black text-xs shadow-md transition cursor-pointer active:scale-98"
                            style="min-height: 48px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                        <span>KONFIRMASI BUKA &amp; SERAH TERIMA</span>
                    </button>

                    <button type="button" 
                            @click="openingModalOpen = false" 
                            class="w-full py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200 transition text-center cursor-pointer">
                        Batal
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
