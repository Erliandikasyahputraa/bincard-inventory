# Digital Bincard & Inventory System

Sistem manajemen inventaris (Bincard) digital berbasis web untuk melacak stok masuk, stok keluar, opname, dan pelaporan dengan antarmuka yang modern, cepat, dan responsif. Dirancang dengan Dark-Theme "Hybrid Pro".

## Fitur Utama
- **Manajemen Master Data:** Produk, Pemasok, Pelanggan lengkap dengan fitur Bulk Import via Excel.
- **Transaksi Inventaris:** Pencatatan Barang Masuk, Barang Keluar, dan Penyesuaian (Stock Opname) yang terintegrasi pada riwayat Ledger.
- **Pemindai Barcode/QR:** Pencarian cepat berbasis kode SKU/Barcode, mendukung input via device scanner fisik maupun kamera.
- **Pelaporan & Export:** Cetak Laporan transaksi (PDF, Excel/CSV) dengan fitur _Sorting_ (Terbaru, Terlama, Terbanyak, Paling Sedikit).
- **Cetak Label QR Code:** Generate dan cetak QR Code massal maupun tunggal (full kertas HVS/A4) untuk penempelan pada fisik rak gudang atau item.
- **Role-Based Access (RBAC):** Pemisahan hak antara **Admin** (pengaturan perusahaan, master data) dan **Pelaksana** (operasional inventaris rutin).
- **Dashboard Interaktif:** Pantau statistik masuk/keluar harian, grafik batang aktivitas dalam periode 6 bulan terakhir, dan pengingat stok kritis.

## Teknologi Utama
- **Framework Utama:** Laravel 11.x
- **Frontend / Styling:** Livewire 3, Alpine.js, Vanilla CSS, Tailwind CSS (via Vite)
- **Database:** Relasional (MySQL / MariaDB)
- **Library Pendukung:** ApexCharts (Grafik), DomPDF (Ekspor Surat Jalan & PDF Laporan), Maatwebsite/Laravel-Excel (Import/Export Laporan).

## Panduan Instalasi (Development)
Sistem ini membutuhkan PHP versi 8.2+ dan Node.js (untuk kompilasi aset frontend).

1. Clone repositori ini:
   ```bash
   git clone https://github.com/Erliandikasyahputraa/bincard-inventory.git
   cd bincard-inventory
   ```
2. Salin dan konfigurasi file environment:
   ```bash
   cp .env.example .env
   ```
   *Atur koneksi database Anda di dalam file `.env` yang merujuk kepada database lokal MySQL Anda.*
3. Install semua *dependency* Backend dan Frontend:
   ```bash
   composer install
   npm install
   ```
4. Lakukan Migrasi Schema (dan Seeding bila tersedia):
   ```bash
   php artisan migrate
   ```
5. Kompilasi aset Vite:
   ```bash
   npm run build
   ```
   Atau untuk fase development yang interaktif: `npm run dev`
6. Jalankan Local Server Laravel:
   ```bash
   php artisan serve
   ```
Sistem dapat diakses di `http://localhost:8000`.

## Catatan Rilis
- Versi saat ini belum termasuk otentikasi hardware langsung untuk scanner, segala input barcode akan difokuskan pada kolom yang merespon input HID (seperti mengetik di keyboard biasa) dari scanner eksternal.

---
_Dibuat dengan bantuan Antigravity - Advanced Agentic Coding Assistant._
