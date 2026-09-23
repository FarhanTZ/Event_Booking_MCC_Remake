# Panduan Lengkap Keamanan Produksi & Hosting (Pilar 2 Security)
**Proyek:** Malang Creative Center (MCC) Room Booking System  
**Dokumen:** Security & Hardening Guide untuk Tahap Deployment / Go-Live  

---

## 📌 Ringkasan Arsitektur Keamanan
Keamanan sistem MCC dibagi menjadi 2 pilar utama:
1. **Pilar 1 (Aplikasi & Kode - Internal Laravel)**: *Sudah terimplementasi aktif di codebase*.
   - Security Response Headers (Anti-Clickjacking, Anti-MIME Sniffing, XSS Filter, Referrer Policy).
   - Rate Limiting (Login throttle 5x/menit, Global Web Request 120 req/menit).
   - Honeypot Anti-Bot Trap (`mcc_security_trap`).
   - Anti-IDOR Authorization Policies (`BookingPolicy`, `UserPolicy`).
   - Strong Password Policy (Min 8 karakter, wajib kombinasi huruf & angka).
   - Strict File Upload Validation (MIME spoof check, real-extension enforcement).

2. **Pilar 2 (Infrastruktur & Cloudflare - Eksternal Hosting)**: *Diterapkan saat website sudah di-hosting/deploy ke server VPS / Cloud*.

---

## 🛡️ Langkah Demi Langkah Pilar 2 (Saat Sudah di Hosting)

### 1. Integrasi Cloudflare (Lapisan Terdepan / Edge WAF & Anti-DDoS)
Cloudflare berfungsi sebagai perisai pertama sebelum lalu lintas internet menyentuh server hosting MCC.

- [ ] **DNS Proxy Mode**:
  - Ubah Nameserver domain MCC ke Nameserver Cloudflare.
  - Pastikan icon awan (Cloud Icon) menyala **Orange (Proxied)** untuk record `A` (`@` dan `www`). Ini menyembunyikan IP Asli server VPS agar penyerang tidak bisa langsung menembak IP server.
- [ ] **SSL / TLS Encryption**:
  - Masuk menu **SSL/TLS** -> Pilih mode **Full (Strict)**.
  - Aktifkan **Always Use HTTPS** dan **Automatic HTTPS Rewrites**.
  - Aktifkan **HSTS (HTTP Strict Transport Security)** (Max-Age: 12 months, Include Subdomains, Preload).
- [ ] **Web Application Firewall (WAF) & Bot Mitigation**:
  - Masuk menu **Security** -> **Bots** -> Aktifkan **Bot Fight Mode** (Memblokir bot scraper otomatis dan brute-force botnet secara global).
  - Masuk menu **Security** -> **WAF** -> **Rate Limiting Rules**: Buat aturan mitigasi DDoS layer 7 (contoh: batasi IP yang melakukan >50 request/detik untuk memicu Managed Challenge / Captcha).
- [ ] **Under Attack Mode (Fitur Darurat)**:
  - Jika website mengalami serangan DDoS skala besar yang memperlambat sistem, aktifkan toggle **"Under Attack Mode"** di dashboard Cloudflare. Seluruh pengunjung akan melewati verifikasi JS challenge Cloudflare sebelum masuk ke MCC.

---

### 2. Hardening Server Linux / VPS (Nginx & OS)

- [ ] **Konfigurasi Firewall (UFW)**:
  Hanya buka port yang benar-benar dibutuhkan:
  ```bash
  sudo ufw default deny incoming
  sudo ufw default allow outgoing
  sudo ufw allow 22/tcp    # SSH (atau ganti port SSH custom untuk lebih aman)
  sudo ufw allow 80/tcp    # HTTP (dialihkan ke HTTPS)
  sudo ufw allow 443/tcp   # HTTPS
  sudo ufw enable
  ```
  *(Opsional: Jika menggunakan Cloudflare Full Proxy, izinkan port 80/443 hanya dari daftar IP Cloudflare)*.

- [ ] **Install & Aktifkan Fail2ban (Anti Brute Force Server)**:
  ```bash
  sudo apt install fail2ban -y
  sudo systemctl enable --now fail2ban
  ```
  Fail2ban akan otomatis memblokir IP penyerang yang mencoba brute-force password SSH atau spamming Nginx.

- [ ] **Hardening Nginx Web Server**:
  Tambahkan rate limiting dan proteksi file tersembunyi pada konfigurasi virtual host Nginx:
  ```nginx
  # Batasi request rate di Nginx level
  limit_req_zone $binary_remote_addr zone=mcc_limit:10m rate=30r/s;

  server {
      listen 443 ssl http2;
      server_name booking.mcc.or.id;

      # Terapkan rate limiting
      limit_req zone=mcc_limit burst=50 nodelay;

      # Blokir akses ke file sensitif dan file titik (.)
      location ~ /\.(?!well-known).* {
          deny all;
      }

      location ~ \.(env|git|yml|ini|sql|log)$ {
          deny all;
          return 404;
      }
  }
  ```

---

### 3. Hardening Konfigurasi Lingkungan Laravel (`.env` Production)

Saat deploy ke production, pastikan file `.env` di server memiliki nilai konfigurasi berikut:

- [ ] **Matikan Debug Mode**:
  ```env
  APP_ENV=production
  APP_DEBUG=false
  APP_URL=https://booking.mcc.or.id
  ```
  *(SANGAT KRUSIAL: `APP_DEBUG=false` mencegah bocornya database credentials atau API keys saat terjadi error sistem)*.

- [ ] **Enkripsi & Sesi yang Aman**:
  ```env
  SESSION_DRIVER=database       # atau redis
  SESSION_LIFETIME=120
  SESSION_ENCRYPT=true
  SESSION_SECURE_COOKIE=true   # Wajib HTTPS
  SESSION_HTTP_ONLY=true
  SESSION_SAME_SITE=lax
  ```

- [ ] **Optimasi Cache Laravel**:
  Jalankan perintah berikut di server produksi setiap kali update kode:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
  ```

---

### 4. Database Security Hardening (PostgreSQL)

- [ ] **Tutup Port Database ke Publik**:
  Pastikan port PostgreSQL `5432` **TIDAK DIBUKA** di UFW Firewall publik. Hanya izinkan koneksi internal (`127.0.0.1` / `localhost` atau private VPC subnet).
- [ ] **Gunakan Akun Database Khusus Aplikasi**:
  Jangan gunakan user `postgres` superadmin untuk koneksi `.env`. Buat dedicated user dengan hak akses khusus ke `mcc_booking_db`.
- [ ] **Automated Backup & Disaster Recovery**:
  Setup Cron job harian untuk dump database otomatis ke cloud storage / cold storage:
  ```bash
  pg_dump -U mcc_user -d mcc_booking_db | gzip > /backup/mcc_db_$(date +\%Y\%m\%d).sql.gz
  ```

---

## 📋 Checklist Ringkas Sebelum Go-Live / Launching

| Kategori | Item Pemeriksaan | Status |
| :--- | :--- | :--- |
| **Cloudflare** | Domain terhubung ke Cloudflare & Proxy Status ON (Awan Orange) | [ ] |
| **Cloudflare** | SSL/TLS Mode diset ke **Full (Strict)** | [ ] |
| **Cloudflare** | Bot Fight Mode diaktifkan | [ ] |
| **Server VPS** | Firewall UFW aktif (Port 22, 80, 443 saja) | [ ] |
| **Server VPS** | Fail2ban terinstall dan aktif | [ ] |
| **Laravel .env**| `APP_DEBUG=false` dan `APP_ENV=production` | [ ] |
| **Laravel .env**| `SESSION_SECURE_COOKIE=true` | [ ] |
| **Database** | Port PostgreSQL 5432 tertutup dari internet publik | [ ] |
| **Database** | Script backup berkala harian aktif | [ ] |

---
*Dokumen ini dibuat otomatis sebagai panduan standar operasional prosedur (SOP) keamanan infrastruktur MCC Booking System.*
