Role & Task:
Bertindaklah sebagai Senior Full-Stack Frontend Engineer. Bangun halaman/komponen web untuk Malang Creative Center (MCC) Event Reservation System dengan mengimplementasikan DESIGN SYSTEM SPECIFICATION (design.md) secara ketat, akurat, dan tanpa deviasi.

Target Stack:
- HTML5 / Blade Component Structure
- Tailwind CSS v3.4+ (menggunakan konfigurasi token MCC yang ditentukan)
- Alpine.js v3.x untuk reaktivitas status dan floating countdown timer

Strict Design System & Brand Rules:
1. Brand Identity & Colors:
   - Primary Brand Color: `#001A72` (wajib gunakan token mcc-primary atau arbitrary `[#001A72]`). Dilarang keras menggunakan Tailwind default blue (blue-500, blue-600, dsb).
   - Primary Hover: `#001250` (mcc-primary-hover).
   - Primary Light / Active Accent: `#EEF2FF` (mcc-primary-light).
   - Brand Accent: Bersih dan profesional menggunakan `text-white`, `bg-white`, dan `border-white/20`. Aksen warna emas/amber untuk elemen branding/UI telah dihilangkan dan digantikan dengan warna putih bersih.
   - Typography: Font family 'Plus Jakarta Sans', 'Inter', sans-serif.
   - Text Hierarchy:
     * Heading / Label Bold: `#0F172A` (slate-900)
     * Body / Deskripsi: `#334155` (slate-700)
     * Subtitle / Muted: `#64748B` (slate-500)
     * Border Standar: `#CBD5E1` (slate-300)
     * Canvas Background: `#F8FAFC` (slate-50)
     * Card Surface: `#FFFFFF` (white) dengan rounded-brand (`0.625rem` / 10px) dan border border-slate-200 shadow-card.
   - Contrast Rule: Pada background `#001A72`, seluruh teks dan ikon WAJIB `text-white` atau `text-slate-100` dengan border `border-white/20`.

2. Operational Status Color Mapping:
   Gunakan status badge dan indikator sesuai matriks operasional:
   - Confirmed / Clear: `bg-emerald-50 text-emerald-700 border-emerald-200` (atau `bg-emerald-600 text-white` untuk badge solid).
   - In-Progress / Redis Lock / Awaiting Kurasi: `bg-amber-50 text-amber-700 border-amber-200` (atau `bg-amber-500 text-white`).
   - Rejected / Incident / KTP Ditahan: `bg-rose-50 text-rose-700 border-rose-200` (atau `bg-rose-600 text-white`).
   - Revise / Grace Period: `bg-indigo-50 text-indigo-700 border-indigo-200` (atau `bg-indigo-600 text-white`).
   - Available Slot: `bg-slate-100 text-slate-700 hover:border-[#001A72] hover:bg-[#EEF2FF] cursor-pointer`.

3. Komponen Wajib yang Harus Dihasilkan:
   - Floating Redis Lock Countdown Bar: Sticky di bagian atas layar menggunakan Alpine.js (`x-data="countdownTimer(900)"`) dengan timer mundur 15 menit, indikator dot `bg-white animate-pulse`, format waktu digital putih tebal, dan notice teks pendukung.
   - Main Header / Navbar MCC: Latar belakang `bg-[#001A72]` atau `bg-white`, logo/branding resmi MCC, teks putih/slate-900 kontras tinggi, dan indikator user/role aktif.
   - Reservation Booking View & Schedule Slot Matrix: Grid kalender waktu reservasi ruangan (kolom jam, baris status ruangan/slot) dengan state interaktif hover slot kosong (`hover:bg-[#EEF2FF] hover:border-[#001A72]`).
   - Form Pemesanan Ruangan: Input nama kegiatan, penanggung jawab, tanggal & jam, upload proposal (lengkap dengan state validasi normal dan error format PDF border-rose-300), dan status badge aktif.
   - Action Button Row: Tombol Primary CTA (`bg-[#001A72]` hover `bg-[#001250]` atau `bg-white text-[#001A72]`), Secondary (`bg-white border-slate-300 text-slate-700`), dan Destructive button (`bg-rose-600 text-white`).
   - Mobile-First Operational Readiness: Memenuhi standar hit area minimal 44x44px untuk touch target pada viewport mobile (sm: breakpoint).

Output Requirements:
- Hasilkan kode bersih, modular, semantik, dan siap pakai.
- Jika berupa file Tailwind config, sertakan modul extend `mcc` dan border radius `brand`.
- Berikan implementasi lengkap tanpa placeholder `TODO` pada komponen visual inti.