# 2. `anti_slop.md` (Global UI/UX & Frontend Standard)

```markdown
# UNIVERSAL ANTI-SLOP UI/UX DESIGN DIRECTIVE

Dokumen ini adalah pedoman absolut perancangan antarmuka (frontend) untuk mencegah "AI Slop" — pola desain generik, membosankan, tidak fungsional, dan penuh ornamen sia-sia yang sering digenerate oleh AI.

---

## 1. Dosa Besar "AI Slop" yang DILARANG

1. **Purple/Cyan/Indigo Gradient Addiction:**
   * DILARANG membuat background, card, atau teks yang dilapisi gradien ungu-biru neon (`from-purple-600 to-indigo-600` atau `from-cyan-400 to-blue-600`) kecuali sistem design produk tersebut secara eksplisit memintanya.
   * Gunakan palet warna produk yang solid, terukur, dan memiliki kontras aksesibilitas tinggi.

2. **Glow & Blur Berlebihan (Neon Overload):**
   * DILARANG menggunakan efek `blur-3xl`, `shadow-[0_0_50px_rgba(...)]`, atau ambient neon glow pada aplikasi fungsional, dashboard, atau form operasional.

3. **Komponen Dekoratif Nirfungsi:**
   * DILARANG memasang elemen dekorasi yang tidak membawa makna data (misal: badge `+24.5% vs last month` di card yang sebenarnya statis, atau ikon raksasa transparan yang mengambang di pojok card).
   * Setiap pixel di layar harus melayani fungsi keterbacaan (*readability*) atau interaksi (*utility*).

4. **Padding Balon (Bloated Spacing):**
   * DILARANG menggunakan margin/padding raksasa (`p-10`, `p-12`, `gap-16`) pada card data sederhana sehingga layar desktop hanya sanggup memuat 2–3 informasi.
   * Pertahankan kepadatan informasi yang wajar (*information density*): `p-4` sampai `p-6` untuk card; `px-3 py-2` untuk input/tabel.

5. **Glassmorphism Gagal (Unreadable Frosted Glass):**
   * DILARANG menggunakan `backdrop-blur` transparan tipis di atas background gambar/warna dinamis yang mengakibatkan teks di atasnya kehilangan kontras. Gunakan solid card surface (`bg-white` atau `bg-slate-900`).

---

## 2. Standar Desain Fungsional (Clean UI Principles)

### 2.1 Tipografi & Hierarki Visual
* Batasi maksimal 1–2 font keluarga dalam satu aplikasi.
* DILARANG memberi style `text-transparent bg-clip-text` pada semua judul halaman. Judul utama harus tegas dan solid (`text-slate-900` atau `text-white`).
* Skala ukuran teks wajib proporsional:
  * Page Title: `text-2xl font-bold`
  * Section / Card Title: `text-base` atau `text-lg font-semibold`
  * Body Text: `text-sm text-slate-700` (atau `slate-300` pada dark mode)
  * Meta / Helper Text: `text-xs text-slate-500`

### 2.2 Form & Input Fields
* Input field harus memiliki batas fisik yang jelas (`border border-slate-300`), bukan sekadar garis tipis samar.
* Label formulir wajib berada di luar input sebagai label statis, bukan mengandalkan placeholder yang hilang saat user mengetik.
* Error state wajib memiliki pesan teks spesifik di bawah kolom terkait (`text-xs text-rose-600`), bukan sekadar mengubah border menjadi merah tanpa penjelasan.
* Focus state wajib jelas untuk aksesibilitas keyboard: `focus:ring-2 focus:ring-offset-1`.

### 2.3 Tombol & Status Interaksi
* Setiap elemen interaktif wajib mendefinisikan seluruh status:
  * Normal
  * Hover (`:hover`)
  * Active / Pressed (`:active`)
  * Focus Accessible (`:focus-visible`)
  * Disabled (`:disabled` dengan visual pudar dan `cursor-not-allowed`)
* Gunakan transisi halus (`transition duration-150`), hindari animasi pantul berlebihan (*bouncing/pulsing*) pada tombol penting kecuali indikator darurat.

### 2.4 Tabel Data & List
* Data tabular harus memiliki:
  * Header yang tegas (`bg-slate-50 text-xs font-semibold text-slate-600 uppercase tracking-wider`).
  * Garis pembatas baris yang tipis dan rapi (`divide-y divide-slate-200`).
  * Perataan yang benar: teks rata kiri (*left-aligned*), angka/nominal rata kanan (*right-aligned*), status/badge rata tengah (*centered*).

---

## 3. Matriks Perbandingan: AI Slop vs Production Clean

| Komponen | ❌ AI Slop Pattern (Dilarang) | ✅ Production Clean Pattern (Wajib) |
| :--- | :--- | :--- |
| **Card Data** | `rounded-3xl bg-gradient-to-br from-indigo-900 to-purple-900 p-8 shadow-2xl border border-white/20` | `rounded-lg bg-white border border-slate-200 p-5 shadow-sm` |
| **Primary Button** | `bg-gradient-to-r from-cyan-400 to-indigo-600 rounded-full py-4 text-lg font-black animate-pulse` | `bg-primary-600 hover:bg-primary-700 rounded-md px-4 py-2 text-sm font-semibold text-white` |
| **Status Badge** | `px-5 py-2.5 rounded-2xl bg-emerald-500/20 text-emerald-300 backdrop-blur` | `px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200` |
| **Modal Box** | Menutupi seluruh layar dengan blur ekstrem dan padding raksasa | Dialog terpusat dengan ukuran terukur (`max-w-lg`), border solid, dan tombol aksi terstruktur di footer |

---

## 4. Checklist Uji Kelayakan Sebelum Review

- [ ] Apakah teks lulus standar kontras WCAG (mudah dibaca tanpa menyilaukan mata)?
- [ ] Apakah tidak ada gradien ungu/biru generik tanpa alasan branding yang jelas?
- [ ] Apakah layout responsif dan tidak rusak saat dibuka di viewport sempit (mobile/tablet)?
- [ ] Apakah tombol penting memiliki indikator loading dan disabled saat proses async berjalan?
- [ ] Apakah semua elemen dekorasi yang tidak membawa informasi penting sudah dihapus?