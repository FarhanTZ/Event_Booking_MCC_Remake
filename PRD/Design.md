DESIGN SYSTEM SPECIFICATION (design.md)Project: Malang Creative Center (MCC) Event Reservation SystemBrand Foundation: MCC Deep Blue Corporate IdentityPrimary Color Token: #001A72Target Stack: Tailwind CSS v3.4+, Alpine.js, Blade Components1. Brand Color Tokens & Palette HierarchySeluruh agen AI dan developer wajib menggunakan token warna berikut. Dilarang keras meng-hardcode warna sembarang di file Blade/CSS.1.1 Palette MatrixToken NameHex CodeTailwind TokenPenggunaan UIBrand Primary#001A72mcc-primaryWarna identitas utama: Navbar, Sidebar, Primary CTA Button, Header tabel, Border aktif.Brand Primary Hover#001250mcc-primary-hoverState :hover dan :active pada tombol utama & link navigasi.Brand Primary Light#EEF2FFmcc-primary-lightBackground aksen, baris tabel terpilih, highlight slot aktif, badge netral.Neutral Dark (Text)#0F172Aslate-900Teks judul utama (Heading 1-4), label formulir tebal.Neutral Body (Text)#334155slate-700Teks paragraf, deskripsi form, nilai data read-only.Neutral Muted#64748Bslate-500Subtitle, placeholder input, teks waktu relatif.Neutral Border#CBD5E1slate-300Garis batas input text, divider, border card.Neutral Surface#F8FAFCslate-50Background canvas aplikasi, area dropzone file.Surface Pure White#FFFFFFwhiteBackground card, container modal, dropdown menu.1.2 State & Operational Status Colors (Strict Mapping)Sistem operasional MCC memiliki banyak status transisi. Gunakan pemetaan warna semantik berikut secara konsisten:┌─────────────────────────┬──────────────┬──────────────────────────┬────────────────────────────────────────────────────────┐
│ Konteks Status          │ Hex / Token  │ Tailwind Classes         │ Penggunaan Kasus                                       │
├─────────────────────────┼──────────────┼──────────────────────────┼────────────────────────────────────────────────────────┤
│ Confirmed / Clear       │ Emerald 600  │ bg-emerald-600           │ Booking disetujui, ruangan aman (DO), E-Pass sah,      │
│                         │ Emerald 50   │ text-white / bg-emerald-50│ KTP dikembalikan.                                      │
├─────────────────────────┼──────────────┼──────────────────────────┼────────────────────────────────────────────────────────┤
│ In-Progress / Lock 15m  │ Amber 500    │ bg-amber-500             │ Slot sedang diisi via Redis Lock, menunggu kurasi MKT, │
│                         │ Amber 50     │ text-white / bg-amber-50 │ pending clearance inspeksi DO.                         │
├─────────────────────────┼──────────────┼──────────────────────────┼────────────────────────────────────────────────────────┤
│ Rejected / Incident     │ Rose 600     │ bg-rose-600              │ Kerusakan/kotoran ruangan (DO), penolakan proposal,    │
│                         │ Rose 50      │ text-white / bg-rose-50  │ KTP ditahan, akun suspended / blacklist.               │
├─────────────────────────┼──────────────┼──────────────────────────┼────────────────────────────────────────────────────────┤
│ Revise / Grace Period   │ Indigo 600   │ bg-indigo-600            │ Permohonan butuh perbaikan berkas (timer 24 jam).      │
│                         │ Indigo 50    │ text-white / bg-indigo-50│                                                        │
├─────────────────────────┼──────────────┼──────────────────────────┼────────────────────────────────────────────────────────┤
│ Available (Kalender)    │ Slate 100    │ bg-slate-100             │ Slot jam kosong yang bisa diklik oleh user/guest.      │
│                         │ Slate 700    │ text-slate-700           │                                                        │
└─────────────────────────┴──────────────┴──────────────────────────┴────────────────────────────────────────────────────────┘
2. Tailwind CSS Configuration SnippetSalin konfigurasi ini langsung ke dalam tailwind.config.js:JavaScript/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/View/Components/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        mcc: {
          primary: {
            DEFAULT: '#001A72', // Official Brand Blue
            hover: '#001250',
            light: '#EEF2FF',
            50: '#F0F3FF',
            100: '#E0E7FF',
            200: '#C7D2FE',
            300: '#A5B4FC',
            400: '#818CF8',
            500: '#6366F1',
            600: '#001A72', // Primary Anchor
            700: '#00155C',
            800: '#001046',
            900: '#000A30',
          },
        },
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 1px 3px 0 rgb(0 0 0 / 0.07), 0 1px 2px -1px rgb(0 0 0 / 0.05)',
        'elevated': '0 10px 15px -3px rgb(0 26 114 / 0.08), 0 4px 6px -4px rgb(0 26 114 / 0.04)',
      },
      borderRadius: {
        'brand': '0.625rem', // 10px - standar elemen MCC
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
3. UI Component Standards (Blade + Tailwind Classes)AI Agent wajib mengikuti pola penulisan komponen berikut saat menyusun view:3.1 ButtonsPrimary Button (CTA):HTML<button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand bg-[#001A72] text-white text-sm font-semibold shadow-sm hover:bg-[#001250] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#001A72] transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
    <span>Pesan Ruangan</span>
</button>
Secondary / Neutral Button:HTML<button type="button" class="inline-flex items-center justify-center px-5 py-2.5 rounded-brand border border-slate-300 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition-colors">
    <span>Kembali</span>
</button>
Destructive / Danger Button (Tolak / Laporkan Insiden):HTML<button type="button" class="inline-flex items-center justify-center px-4 py-2 rounded-brand bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 transition-colors">
    <span>Tahan KTP / Lapor Insiden</span>
</button>
3.2 Form Inputs & Validation StatesText Input Standar:HTML<div>
    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
        Nama Kegiatan
    </label>
    <input type="text" class="block w-full rounded-brand border-slate-300 shadow-sm text-sm focus:border-[#001A72] focus:ring-[#001A72] placeholder:text-slate-400" placeholder="Contoh: Malang Design Week 2026">
</div>
Input State Error:HTML<input type="text" class="block w-full rounded-brand border-rose-300 text-rose-900 focus:border-rose-600 focus:ring-rose-600 text-sm">
<p class="mt-1 text-xs text-rose-600 font-medium">Proposal wajib diunggah dalam format PDF.</p>
3.3 Status BadgesConfirmed: <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Disetujui</span>Awaiting Review: <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Kurasi</span>Under Revision: <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Perlu Revisi</span>Incident / Held: <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">KTP Ditahan</span>3.4 Floating Redis Lock Countdown Bar (Alpine.js)Elemen wajib di bagian atas layar form booking user saat slot sedang terkunci 15 menit:HTML<div x-data="countdownTimer(900)" class="sticky top-0 z-40 bg-[#001A72] text-white px-4 py-2.5 shadow-md flex items-center justify-between">
    <div class="flex items-center gap-2">
        <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
        <span class="text-xs font-medium text-slate-100">Slot Ruangan Terkunci Sementara:</span>
        <span class="font-mono font-bold text-sm tracking-wider text-amber-300" x-text="formatTime()">15:00</span>
    </div>
    <span class="text-xs text-slate-300 hidden md:inline">Selesaikan permohonan sebelum waktu habis.</span>
</div>
4. Layout & Grid StandardsMax Container Width: max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 untuk seluruh halaman utama.Dashboard Navbar / Header:Latar belakang: #001A72 (bg-[#001A72]).Teks & Ikon: Putih (text-white) dengan aksen hover text-indigo-200.Card Container:Kelas standar: bg-white rounded-brand border border-slate-200 shadow-card p-6.Kalender Grid Slot:Grid waktu responsif berbasis jam (kolom: nama jam, baris: status slot).Hover slot kosong: hover:border-[#001A72] hover:bg-[#EEF2FF] cursor-pointer.5. Rules for AI Agents (Code Generation Rules)NO RANDOM BLUES: Jangan pernah gunakan Tailwind default blue-500 atau blue-600. Jika butuh warna primer, wajib gunakan bg-[#001A72], text-[#001A72], atau token kustom mcc-primary.HIGH CONTRAST TEXT: Jika background memakai #001A72, teks di atasnya harus putih murni (text-white) atau slate terang (text-slate-100).ACCESSIBLE FOCUS STATES: Setiap tombol dan input wajib memiliki ring kontras focus:ring-2 focus:ring-[#001A72].MOBILE FIRST FOR DO & SCANNER: Tampilan modul Duty Officer dan Front Office Scanner wajib diuji pada breakpoint sm: (ramah sentuhan jempol, minimum hit area $44\times44\text{ px}$).