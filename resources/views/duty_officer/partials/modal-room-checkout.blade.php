<!-- ==================================================================== -->
<!-- MOBILE BOTTOM SHEET: LEMBAR KERJA CEK KELAYAKAN FISIK RUANGAN CHECKOUT (FR-DO-03, FR-DO-04, FR-DO-05, FR-DO-06) -->
<!-- ==================================================================== -->
<div x-show="checkoutModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-checkout-inspection-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Backdrop -->
    <div x-show="checkoutModalOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="checkoutModalOpen = false"
         class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>

    <div class="flex min-h-full items-end justify-center text-center sm:items-center p-0 sm:p-4">
        <div x-show="checkoutModalOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-t-3xl sm:rounded-brand bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-200 max-h-[94vh] flex flex-col"
             x-data="roomInspectionForm()">
            
            <!-- Drag Handle Bar for Mobile Touch Feel -->
            <div class="pt-3 pb-1 flex justify-center sm:hidden bg-[#001A72] cursor-pointer" @click="checkoutModalOpen = false">
                <div class="w-12 h-1.5 rounded-full bg-white/30"></div>
            </div>

            <!-- Modal Header -->
            <div class="bg-gradient-to-br from-[#001A72] to-[#001250] text-white p-4 sm:p-5 flex items-start justify-between gap-3 shrink-0">
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white/20 text-white" x-text="activeTask.bookingCode">
                            #MCC-BOOKING
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-teal-400 text-slate-950">
                            FR-DO-03: Inspeksi Fisik Ruangan
                        </span>
                    </div>
                    <h3 class="text-base font-black text-white tracking-tight leading-snug line-clamp-1" x-text="`Cek Kelayakan: ${activeTask.roomName}`">
                        Inspeksi Checkout
                    </h3>
                    <p class="text-xs text-indigo-200 mt-0.5 truncate" x-text="`${activeTask.eventName}`">
                        Nama Acara
                    </p>
                </div>

                <button type="button" 
                        @click="checkoutModalOpen = false"
                        class="p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition cursor-pointer shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- TAB SWITCHER: [ Kondisi Aman (Clear) | Selesaikan Insiden (Jika ada) | Laporkan Insiden ] -->
            <div class="p-1.5 bg-slate-100 border-b border-slate-200 shrink-0 grid gap-1.5"
                 :class="activeTask.incident && !activeTask.incident.isResolved ? 'grid-cols-3' : 'grid-cols-2'">
                
                <!-- Tab 1: Sinyal Aman (Clear) -->
                <button type="button" 
                        @click="inspectionMode = 'clear'"
                        class="py-2.5 px-2 rounded-2xl text-xs font-black transition flex items-center justify-center gap-1 cursor-pointer select-none"
                        :class="inspectionMode === 'clear' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    <span class="truncate">Aman (Clear)</span>
                </button>

                <!-- Tab 2: Selesaikan Insiden (FR-DO-06) (Tampil jika ada insiden aktif) -->
                <template x-if="activeTask.incident && !activeTask.incident.isResolved">
                    <button type="button" 
                            @click="inspectionMode = 'resolve'"
                            class="py-2.5 px-2 rounded-2xl text-xs font-black transition flex items-center justify-center gap-1 cursor-pointer select-none"
                            :class="inspectionMode === 'resolve' ? 'bg-indigo-600 text-white shadow-xs' : 'text-indigo-700 hover:bg-indigo-50'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-indigo-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span class="truncate">Selesaikan</span>
                    </button>
                </template>

                <!-- Tab 3: Laporkan Insiden Baru -->
                <button type="button" 
                        @click="inspectionMode = 'incident'"
                        class="py-2.5 px-2 rounded-2xl text-xs font-black transition flex items-center justify-center gap-1 cursor-pointer select-none"
                        :class="inspectionMode === 'incident' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:text-rose-700'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-rose-200 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span class="truncate">Lapor Insiden</span>
                </button>
            </div>

            <!-- ======================================================== -->
            <!-- 1. FORM SINYAL HIJAU / CLEARANCE (FR-DO-04) -->
            <!-- ======================================================== -->
            <form x-show="inspectionMode === 'clear'" 
                  :action="`/duty-officer/bookings/${activeTask.bookingId}/clearance`" 
                  method="POST" 
                  class="flex flex-col h-full min-h-0 flex-1">
                @csrf

                <!-- Body Checklist -->
                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-slate-800">
                    
                    <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-xs text-emerald-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        <p class="text-xs text-emerald-900 leading-snug">
                            Sinyal Hijau akan <strong>seketika membuka akses tombol pelepasan KTP</strong> di meja Front Office Lantai Dasar.
                        </p>
                    </div>

                    <!-- Checklist 1: Kebersihan Ruangan (Wajib Centang) -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 block">
                                1. Checklist Kebersihan Ruangan (Wajib)
                            </span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">3 Item</span>
                        </div>

                        <div class="space-y-2">
                            <!-- Bebas Sampah Konsumsi -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="cleanliness_checklist[trash]" value="1" required x-model="clearChecklist.trash" class="w-5 h-5 mt-0.5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <div class="text-xs">
                                    <span class="font-black text-slate-900 block">Area Bebas Sampah Konsumsi</span>
                                    <span class="text-[11px] text-slate-500 leading-tight">Kotak makanan, botol minuman, dan sisa konsumsi telah dibuang ke tempat sampah.</span>
                                </div>
                            </label>

                            <!-- Sisa Dekorasi / Lakban -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="cleanliness_checklist[tape]" value="1" required x-model="clearChecklist.tape" class="w-5 h-5 mt-0.5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <div class="text-xs">
                                    <span class="font-black text-slate-900 block">Bekas Lakban &amp; Perekat Bersih</span>
                                    <span class="text-[11px] text-slate-500 leading-tight">Lakban lantai, double tape panggung, dan perekat dekorasi telah dikelupas tuntas.</span>
                                </div>
                            </label>

                            <!-- Poster & Spanduk Dinding -->
                            <label class="flex items-start gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                <input type="checkbox" name="cleanliness_checklist[posters]" value="1" required x-model="clearChecklist.posters" class="w-5 h-5 mt-0.5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <div class="text-xs">
                                    <span class="font-black text-slate-900 block">Poster &amp; Backdrop Telah Dicopot</span>
                                    <span class="text-[11px] text-slate-500 leading-tight">Dinding, kaca, dan standing banner bebas dari materi promosi kegiatan.</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Checklist 2: Inventaris & Fasilitas Master Ruangan -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <span class="text-[11px] font-black uppercase tracking-wider text-slate-700 block">
                            2. Checklist Inventaris &amp; Fasilitas Ruangan
                        </span>

                        <div class="space-y-2">
                            <template x-if="activeTask.facilities && activeTask.facilities.length > 0">
                                <template x-for="fac in activeTask.facilities" :key="fac.id">
                                    <label class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 cursor-pointer transition select-none">
                                        <div class="flex items-center gap-2.5">
                                            <input type="checkbox" :name="`inventory_checklist[${fac.id}]`" value="1" checked class="w-5 h-5 rounded-lg border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            <div class="text-xs">
                                                <span class="font-black text-slate-900" x-text="fac.facility_name"></span>
                                                <span class="text-[11px] text-slate-500 block" x-text="`${fac.quantity} ${fac.unit}`"></span>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Utuh</span>
                                    </label>
                                </template>
                            </template>

                            <template x-if="!activeTask.facilities || activeTask.facilities.length === 0">
                                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-500 italic">
                                    Fasilitas standar ruangan (AC, Mic, Kabel HDMI, Remote, Meja &amp; Kursi) telah diverifikasi lengkap dan utuh.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Catatan Tambahan (Opsional) -->
                    <div class="space-y-1 pt-2 border-t border-slate-100">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">Catatan Verifikasi (Opsional):</label>
                        <textarea name="notes" 
                                  rows="2" 
                                  placeholder="Contoh: Seluruh fasilitas aman, ruangan terkunci rapi..." 
                                  class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-300 focus:ring-[#001A72] focus:border-[#001A72]"></textarea>
                    </div>

                </div>

                <!-- Footer Submit Sinyal Hijau -->
                <div class="bg-slate-50 p-3.5 border-t border-slate-200 flex flex-col gap-2 shrink-0">
                    <button type="submit" 
                            :disabled="!isClearChecklistValid()"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-black text-xs sm:text-sm shadow-md transition cursor-pointer active:scale-98"
                            style="min-height: 48px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>VERIFIKASI SELESAI — KONDISI AMAN (CLEAR)</span>
                    </button>

                    <button type="button" 
                            @click="checkoutModalOpen = false" 
                            class="w-full py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200 transition text-center cursor-pointer">
                        Tutup
                    </button>
                </div>
            </form>

            <!-- ======================================================== -->
            <!-- 2. FORM PENYELESAIAN INSIDEN (FR-DO-06: Resolve Incident) -->
            <!-- ======================================================== -->
            <form x-show="inspectionMode === 'resolve'" 
                  :action="`/duty-officer/bookings/${activeTask.bookingId}/incident/resolve`" 
                  method="POST" 
                  class="flex flex-col h-full min-h-0 flex-1">
                @csrf
                <input type="hidden" name="incident_id" :value="activeTask.incident ? activeTask.incident.id : ''">

                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-slate-800">
                    
                    <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 flex items-start gap-2.5 text-xs text-indigo-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <p class="text-xs text-indigo-900 leading-snug">
                            Inspeksi ulang setelah tindakan perbaikan oleh panitia. Menandai insiden selesai akan <strong>mengirim sinyal biru (Resolved)</strong> yang membuka kembali tombol pelepasan KTP di Front Office.
                        </p>
                    </div>

                    <!-- Detail Insiden Aktif yang Dilaporkan -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block">
                            Temuan Insiden Sebelumnya:
                        </span>
                        <div class="space-y-1 text-xs">
                            <p class="font-black text-slate-900" x-text="activeTask.incident ? activeTask.incident.itemName : '-'"></p>
                            <p class="text-slate-600 text-xs" x-text="activeTask.incident ? activeTask.incident.description : '-'"></p>
                        </div>
                    </div>

                    <!-- Input Kolom Catatan Penyelesaian (Resolution Notes) -->
                    <div class="space-y-1.5 pt-1">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                            Catatan Penyelesaian (Resolution Notes) <span class="text-indigo-600">*</span>:
                        </label>
                        <textarea name="resolution_notes" 
                                  rows="3" 
                                  required 
                                  placeholder="Contoh: Panitia telah membersihkan seluruh area dan mengganti kabel HDMI baru..." 
                                  class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-300 focus:ring-indigo-500 focus:border-indigo-500 font-medium text-slate-900"></textarea>
                    </div>

                </div>

                <!-- Footer Submit Selesaikan Insiden -->
                <div class="bg-slate-50 p-3.5 border-t border-slate-200 flex flex-col gap-2 shrink-0">
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs sm:text-sm shadow-md transition cursor-pointer active:scale-98"
                            style="min-height: 48px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        <span>TANDAI MASALAH TERSELESAIKAN (RESOLVE INCIDENT)</span>
                    </button>

                    <button type="button" 
                            @click="checkoutModalOpen = false" 
                            class="w-full py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200 transition text-center cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>

            <!-- ======================================================== -->
            <!-- 3. FORM SINYAL MERAH / PELAPORAN INSIDEN (FR-DO-05) -->
            <!-- ======================================================== -->
            <form x-show="inspectionMode === 'incident'" 
                  :action="`/duty-officer/bookings/${activeTask.bookingId}/incident`" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="flex flex-col h-full min-h-0 flex-1">
                @csrf

                <!-- Body Pelaporan Insiden -->
                <div class="p-4 sm:p-5 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-slate-800">
                    
                    <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-xs text-rose-950">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <p class="text-xs text-rose-900 leading-snug">
                            Laporan ini akan <strong>mengirim sinyal merah dan mengunci layar Front Office</strong> sehingga KTP fisik jaminan pemohon ditahan sampai penyelesaian ganti rugi.
                        </p>
                    </div>

                    <!-- Kategori Insiden Dropdown -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                            Kategori Insiden <span class="text-rose-600">*</span>:
                        </label>
                        <select name="category" required class="w-full px-3.5 py-3 text-xs rounded-2xl border border-slate-300 focus:ring-rose-500 focus:border-rose-500 bg-white font-bold text-slate-800">
                            <option value="cleanliness">Kebersihan Ruangan (Kotor Parah / Sampah Berserakan)</option>
                            <option value="property_damage" selected>Kerusakan Fasilitas / Properti Gedung</option>
                            <option value="equipment_loss">Kehilangan Aset / Peralatan Master</option>
                        </select>
                    </div>

                    <!-- Nama Barang / Fasilitas Terdampak -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                            Nama Barang / Fasilitas Terdampak <span class="text-rose-600">*</span>:
                        </label>
                        <input type="text" 
                               name="item_name" 
                               required 
                               placeholder="Contoh: Kabel HDMI Putus, Dinding Tercoret Spidol..." 
                               class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-300 focus:ring-rose-500 focus:border-rose-500 font-medium text-slate-900">
                    </div>

                    <!-- Deskripsi Kronologis -->
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                            Deskripsi Kronologis Temuan Lapangan <span class="text-rose-600">*</span>:
                        </label>
                        <textarea name="description" 
                                  rows="3" 
                                  required 
                                  placeholder="Uraikan detail temuan kondisi fisik, lokasi spesifik kerusakan, atau kronologi yang didapati di ruangan..." 
                                  class="w-full px-3.5 py-2.5 text-xs rounded-2xl border border-slate-300 focus:ring-rose-500 focus:border-rose-500 font-normal text-slate-900"></textarea>
                    </div>

                    <!-- Foto Bukti Kamera Lapangan -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-700 block">
                                Foto Bukti Kamera Lapangan <span class="text-rose-600">*</span>:
                            </label>
                            <span class="text-[10px] font-bold text-slate-400">Maks 5 Foto (JPG/PNG)</span>
                        </div>

                        <!-- Touch-friendly Photo Upload Input Box -->
                        <div class="relative border-2 border-dashed border-rose-300 hover:border-rose-400 bg-rose-50/50 rounded-2xl p-4 text-center cursor-pointer transition">
                            <input type="file" 
                                   name="photos[]" 
                                   multiple 
                                   accept="image/*" 
                                   capture="environment"
                                   required 
                                   @change="handlePhotoChange($event)"
                                   class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10">
                            <div class="space-y-1.5 pointer-events-none">
                                <div class="w-10 h-10 mx-auto rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/></svg>
                                </div>
                                <span class="text-xs font-black text-rose-900 block">Ambil Foto / Upload dari Kamera</span>
                                <span class="text-[11px] text-slate-500 block" x-text="photoCount > 0 ? `${photoCount} foto dipilih` : 'Klik untuk buka kamera atau pilih dari galeri'"></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Submit Sinyal Merah -->
                <div class="bg-slate-50 p-3.5 border-t border-slate-200 flex flex-col gap-2 shrink-0">
                    <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-black text-xs sm:text-sm shadow-md transition cursor-pointer active:scale-98"
                            style="min-height: 48px;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>KIRIM LAPORAN INSIDEN &amp; TAHAN KTP</span>
                    </button>

                    <button type="button" 
                            @click="checkoutModalOpen = false" 
                            class="w-full py-2.5 rounded-2xl bg-white hover:bg-slate-100 text-slate-600 font-bold text-xs border border-slate-200 transition text-center cursor-pointer">
                        Batal
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    function roomInspectionForm() {
        return {
            inspectionMode: 'clear',
            photoCount: 0,
            clearChecklist: {
                trash: true,
                tape: true,
                posters: true
            },
            init() {
                if (this.activeTask && this.activeTask.initialTab) {
                    this.inspectionMode = this.activeTask.initialTab;
                } else if (this.activeTask && this.activeTask.incident && !this.activeTask.incident.isResolved) {
                    this.inspectionMode = 'resolve';
                }
            },
            isClearChecklistValid() {
                return this.clearChecklist.trash && this.clearChecklist.tape && this.clearChecklist.posters;
            },
            handlePhotoChange(e) {
                this.photoCount = e.target.files ? e.target.files.length : 0;
            }
        };
    }
</script>
