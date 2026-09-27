<!-- ==================================================================== -->
<!-- MODAL PENDAMPINGAN CHECK-IN & PEMBUKAAN AKSES RUANGAN (FR-DO-02) -->
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
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="openingModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-200 my-8">
            
            <form :action="openingActionUrl" method="POST" class="space-y-0">
                @csrf

                <!-- Modal Header (MCC Brand #001A72) -->
                <div class="bg-[#001A72] text-white p-4 sm:p-5 flex items-start justify-between gap-3 border-b border-white/10">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-white/15 text-white" x-text="activeTask.bookingCode">
                                #MCC-BOOKING
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-400 text-slate-950">
                                FR-DO-02: Buka Akses Ruangan
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-white tracking-tight leading-snug" x-text="`Pembukaan &amp; Serah Terima: ${activeTask.roomName}`">
                            Pembukaan &amp; Serah Terima Ruangan
                        </h3>
                        <p class="text-xs text-indigo-200 mt-0.5" x-text="`${activeTask.eventName} (${activeTask.institutionName})`">
                            Acara &amp; Lembaga
                        </p>
                    </div>

                    <button type="button" 
                            @click="openingModalOpen = false"
                            class="p-1.5 rounded-brand bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-4 sm:p-6 space-y-4 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    
                    <!-- Alert Notice Penting -->
                    <div class="p-3.5 rounded-brand bg-amber-50 border border-amber-200 flex items-start gap-3 text-xs text-amber-900">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <div class="space-y-0.5">
                            <span class="font-bold block">SOP Pembukaan Akses Ruangan:</span>
                            <p class="text-[11px] text-amber-800 leading-relaxed">
                                Pastikan panitia/PIC telah hadir di depan ruangan. Lakukan verifikasi kesiapan fisik kelistrikan, AC, proyektor, dan layout bersama panitia sebelum pintu diserahterimakan.
                            </p>
                        </div>
                    </div>

                    <!-- Checklist Kesiapan Fasilitas Dasar -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                <span>Checklist Kesiapan Fasilitas Dasar:</span>
                            </h4>
                            <span class="text-[10px] text-slate-500 font-semibold">Wajib Diverifikasi DO</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- AC -->
                            <label class="flex items-start gap-2.5 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[ac]" value="1" checked class="mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Pendingin Ruangan (AC)</span>
                                    <span class="text-[11px] text-slate-500">AC menyala dingin &amp; remote tersedia.</span>
                                </div>
                            </label>

                            <!-- Kelistrikan & Audio -->
                            <label class="flex items-start gap-2.5 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[electricity]" value="1" checked class="mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Kelistrikan &amp; Sound System</span>
                                    <span class="text-[11px] text-slate-500">Stop kontak aktif &amp; audio berfungsi.</span>
                                </div>
                            </label>

                            <!-- Proyektor & Display -->
                            <label class="flex items-start gap-2.5 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[projector]" value="1" checked class="mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Proyektor / Display Screen</span>
                                    <span class="text-[11px] text-slate-500">Kabel HDMI &amp; layar proyektor siap.</span>
                                </div>
                            </label>

                            <!-- Layout Meja / Kursi -->
                            <label class="flex items-start gap-2.5 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[furniture_layout]" value="1" checked class="mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Layout Meja &amp; Kursi</span>
                                    <span class="text-[11px] text-slate-500">Kapasitas &amp; susunan sesuai pesanan.</span>
                                </div>
                            </label>

                            <!-- Kebersihan Awal -->
                            <label class="sm:col-span-2 flex items-start gap-2.5 p-3 rounded-brand bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="facility_checklist[cleanliness]" value="1" checked class="mt-0.5 rounded border-slate-300 text-[#001A72] focus:ring-[#001A72]">
                                <div class="text-xs">
                                    <span class="font-bold text-slate-800 block">Kebersihan &amp; Kerapihan Awal</span>
                                    <span class="text-[11px] text-slate-500">Lantai bersih, tempat sampah kosong, dan tidak ada sampah sesi sebelumnya.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Master Fasilitas Terdaftar pada Ruangan -->
                    <div class="space-y-2 pt-2 border-t border-slate-100" x-show="activeTask.facilities && activeTask.facilities.length > 0">
                        <h4 class="text-xs font-black uppercase tracking-wider text-slate-800 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#001A72]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            <span>Daftar Inventaris Master Fasilitas Ruangan:</span>
                        </h4>

                        <div class="bg-slate-50 rounded-brand border border-slate-200 overflow-hidden text-xs">
                            <table class="w-full text-left">
                                <thead class="bg-slate-100/80 text-slate-600 font-bold border-b border-slate-200">
                                    <tr>
                                        <th class="py-2 px-3">Nama Fasilitas</th>
                                        <th class="py-2 px-3 text-center">Jumlah</th>
                                        <th class="py-2 px-3 text-right">Kondisi Awal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    <template x-for="fac in activeTask.facilities" :key="fac.id || fac.facility_name">
                                        <tr class="hover:bg-white transition">
                                            <td class="py-2 px-3 text-slate-800 font-semibold" x-text="fac.facility_name"></td>
                                            <td class="py-2 px-3 text-center font-mono text-slate-700" x-text="`${fac.quantity} ${fac.unit}`"></td>
                                            <td class="py-2 px-3 text-right">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Baik / Siap Pakai
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Peralatan Eksternal Mandiri Pemohon -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-700 block">Peralatan Eksternal yang Dibawa Masuk:</span>
                        <template x-if="activeTask.equipmentItems && activeTask.equipmentItems.length > 0">
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="item in activeTask.equipmentItems" :key="item">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#EEF2FF] border border-indigo-100 text-xs font-semibold text-[#001A72]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#001A72]"></span>
                                        <span x-text="item"></span>
                                    </span>
                                </template>
                            </div>
                        </template>
                        <template x-if="!activeTask.equipmentItems || activeTask.equipmentItems.length === 0">
                            <p class="text-xs text-slate-400 italic">Tidak membawa peralatan eksternal mandiri.</p>
                        </template>
                    </div>

                    <!-- Catatan Serah Terima Awal (Opsional) -->
                    <div class="space-y-1 pt-2 border-t border-slate-100">
                        <label class="text-xs font-bold text-slate-700 block">Catatan Serah Terima Awal (Opsional):</label>
                        <textarea name="notes" 
                                  rows="2" 
                                  placeholder="Contoh: AC diset pada 22°C, panitia membawa 2 mic tambahan sendiri, kunci ruangan telah diserahterimakan..." 
                                  class="w-full px-3 py-2 text-xs rounded-brand border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
                    </div>

                </div>

                <!-- Modal Footer Actions -->
                <div class="bg-slate-50 p-4 border-t border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                    <button type="button" 
                            @click="openingModalOpen = false" 
                            class="w-full sm:w-auto px-4 py-2.5 rounded-brand bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs border border-slate-300 transition shadow-2xs cursor-pointer text-center"
                            style="min-height: 44px;">
                        Batal
                    </button>

                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-brand bg-[#001A72] hover:bg-[#001250] text-white font-bold text-xs sm:text-sm shadow-md transition cursor-pointer active:scale-95"
                            style="min-height: 44px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="m17 5-5-3-5 3"/><path d="m17 19-5 3-5-3"/><path d="M2 12h20"/><path d="m5 7-3 5 3 5"/><path d="m19 7 3 5-3 5"/></svg>
                        <span>Konfirmasi Pintu Terbuka &amp; Serah Terima Selesai</span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
