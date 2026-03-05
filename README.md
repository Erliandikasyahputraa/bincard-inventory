<div align="center">
    <img src="public/logo-placeholder.png" alt="Logo" width="120" style="border-radius: 20px; box-shadow: 0px 10px 30px rgba(0,0,0,0.1); margin-bottom: 20px;">
    <h1>Digital Bincard & Inventory Management</h1>
    <p><b>Sistem Pencatatan Gudang, Stock Opname, dan Generator QR Code Modern</b></p>
    <a href="#fitur-utama">🚀 Fitur Utama</a> •
    <a href="#tech-stack">⚙️ Tech Stack</a> •
    <a href="#instalasi-lokal">💻 Instalasi</a>
</div>

---

## 📌 Deskripsi Sistem

**Digital Bincard** adalah solusi aplikasi berbasis Web generasi terbaru yang dirancang untuk mentransformasi sistem pencatatan stok gudang manual menjadi ekosistem digital penuh. Mengusung antarmuka **Hybrid Pro** yang dirancang secara khusus untuk kecepatan operasional (Fast-Entry) dan kenyamanan mata (Real-time Dark Mode).

Aplikasi ini dapat mencetak label *QR Code* pintar, melacak riwayat inventaris (*Audit Trail*), serta menerbitkan dokumen Surat Jalan dan Laporan PDF/Excel siap cetak dalam hitungan detik.

## 🚀 Fitur Utama (Core Modules)

### 📦 1. Manajemen Master Data (Produk, Pemasok, Pelanggan)
Modul utama untuk mendata entitas sistem dengan cepat.
- **Bulk Import via Excel:** Masukkan ribuan data produk secara instan menggunakan format `.xlsx`.
- **Intelligent Stock Tracking:** Menyimpan *SKU*, harga, unit (pcs/dus/box), serta histori perputaran barang.

### 🖨️ 2. QR Code Generator & Label Printing
Mencetak ID unik untuk menempel di barang fisik.
- **Satu-Klik Cetak (Massal):** Fitur untuk menge-print seluruh stok dalam bentuk *grid* QR Code untuk dipotong dan ditempel.
- **Cetak Tunggal:** Cetak QR spesifik per-barang ukuran penuh.
- **Decrypted Scanning:** QR memuat URL pintar, seketika discan akan langsung diarahkan ke form aksi produk tersebut.

### 🔄 3. Ledger Transaksi & Scan Cepat (Barang Masuk/Keluar)
Mencatat lalu lintas (*In/Out*) fisik barang layaknya kartu stok konvensional.
- **Retroactive Binding:** Kemampuan untuk "Backdating" / menyesuaikan tanggal riwayat ke masa lalu.
- **Barcode Scanner Optimized:** Form difokuskan untuk interaksi instan dengan *Hardware Scanner Laser*. Sekali "Tit!", barang masuk terekam.
- **Auto-Generate Surat Jalan:** Setelah log transaksi keluar tersimpan, sistem langsung menawarkan *Download Surat Jalan PDF* bermaterai (logo) perusahaan.

### 📋 4. Sesi Stock Opname (Gudang vs Sistem)
Audit integritas data secara proaktif.
- **Sesi Audit Real-time:** Menampilkan perbandingan antara jumlah *Stok Sistem* versus *Stok Fisik Aktual* yang sedang dihitung manual di lapangan.
- **Automated Reconciliation:** Jika ada *selisih* (kurang/lebih), tombol **Rekonsiliasi** akan secara otomatis membuat Log Transaksi (*Penyesuaian/Adjust*) siluman untuk menyeimbangkan angka mesin dengan angka nyata.

### 📊 5. Visualisasi Dashboard & Laporan Analitik
Pemantauan lalu lintas inventaris di ujung jari.
- **Area Spline Chart (ApexCharts):** Grafik tren stok Masuk & Keluar harian dengan gradien modern.
- **Laporan Dinamis (PDF & Excel):** Filter transaksi berdasar rentang waktu (*Mulai - Sampai*) dan ekspor ke laporan format CSV Excel atau *Cetak PDF Tabular*.

### 🎨 6. Arsitektur UX (Glassmorphic) & Master Theme
- **Livewire Seamless Nav:** Transisi halaman tanpa kedip (SPA feel).
- **Master Light/Dark Mode:** Algoritma tema UI yang bisa menukar keseluruhan warna *(Slate/Emerald/Blue)* tanpa memuat ulang layar, responsif secara reaktif ke semua input, kalender, peringatan (*SweetAlert*), dan grafik statistik.

---

## ⚙️ Tech Stack & Ekosistem Spesifikasi

Aplikasi ini dibangun menggunakan tumpukan teknologi paling progresif dan termutakhir dalam ekosistem *Fullstack PHP Framework*:

| Lapisan (Layer)         | Teknologi & Versi                                                                 |
| ----------------------- | --------------------------------------------------------------------------------- |
| **Server Framework**    | Laravel 11.x (PHP 8.2+)                                                         |
| **Frontend Renderer**   | Livewire 3 (Reaktivitas API & DOM Morphing)                                        |
| **Client Scripting**    | Alpine.js (State Management ringan di browser, *Theme Switching*)                 |
| **Styling & CSS**       | Tailwind CSS v4.0.0-alpha (Utility-first CSS dengan *Custom Variables*)            |
| **Database Engine**     | MySQL / MariaDB (Relational Database)                                              |
| **PDF Engine Engine**   | dompdf (`barryvdh/laravel-dompdf`)                                                |
| **Excel Spreadsheet**   | Maatwebsite/Laravel-Excel (`maatwebsite/excel`)                                    |
| **Data Visualization**  | ApexCharts.js (Interaktif Dashboard)                                               |
| **Iconography**         | Lucide SVG Icons (`lucide.createIcons()`)                                          |
| **Notification Engine** | SweetAlert 2 (Pop-up Dinamis)                                                      |

---

## 💻 Instalasi Lokal (Developer & Admin Setup)

Ikuti panduan ini jika Anda ingin menjalankan Digital Bincard di *Localhost* (XAMPP/Laragon/Valet).

### Prasyarat
- PHP >= 8.2 (Pastikan ekstensi dom, fileinfo, gd, curl aktif di `php.ini`)
- Node.js (Versi 18+ disarankan) & NPM
- Composer
- Database Server (MySQL/MariaDB)

### Langkah Pemasangan

1. **Clone & Masuk ke Direktori**
   ```bash
   git clone https://github.com/Erliandikasyahputraa/bincard-inventory.git
   cd bincard-inventory
   ```

2. **Install Ketergantungan Backend (PHP)**
   ```bash
   composer install
   ```

3. **Install Ketergantungan Frontend (CSS/JS)**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment Database**
   Salin file environtment *blueprint*:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env`. Ubah pengaturan kredensial database sesuai komputer Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bincard_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate App Key & Siapkan Penyimpanan**
   ```bash
   php artisan key:generate
   php artisan storage:link
   ```

6. **Bangun Struktur Database (Migrasi & Dummy Data)**
   Aplikasi menuntut *Seeder* awal untuk membuat akun Admin Pertama Pintu Masuk.
   ```bash
   php artisan migrate:fresh --seed
   ```
   *(Secara otomatis akan membuat Akun Role: Admin dan Role: Pelaksana)*

7. **Compile Asset (Live-Reload CSS & JS Tailwind)**
   ```bash
   npm run build
   ```

8. **Nyalakan Server Aplikasi**
   ```bash
   php artisan serve
   ```
   *(Akses `http://127.0.0.1:8000` di broswer Anda)*

---

### Informasi Default Login

Setelah proses `php artisan migrate:fresh --seed` selesai, sistem membuat dua jenis aktor untuk pengujian:

**1. Level Administrator (Akses Penuh):**
- **Email:** `admin@bincard.test`
- **Password:** `password`

**2. Level Staf / Pelaksana (Terbatas):**
- **Email:** `pelaksana@bincard.test`
- **Password:** `password`

---
*© 2026 Developed with Laravel & Livewire Ecosystem.*
