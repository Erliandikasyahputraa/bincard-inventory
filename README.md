# Digital Bincard & Inventory System 📦

Sistem Manajemen Inventaris Modern berbasis Web yang dirancang untuk menggantikan buku bincard manual (ledger) menjadi sistem digital yang akurat, cepat, dan visual.

---

## 🚀 Overview Proyek

Proyek ini adalah solusi *Warehouse Management System* (WMS) skala menengah yang fokus pada kemudahan input, akurasi data, dan visualisasi stok secara *real-time*. Dengan fitur scanning QR Code dan integrasi laporan otomatis, sistem ini meminimalkan kesalahan manusia (human error) dalam pencatatan barang.

## 🛠️ Technology Stack (Mamma Mia Stack)

Sistem ini dibangun menggunakan teknologi mutakhir dalam ekosistem PHP untuk memastikan performa yang cepat dan pengalaman pengguna yang luar biasa:

1.  **Core Framework**: Laravel 11 (Versi terbaru dengan struktur paling ringan).
2.  **Frontend Engine**: Livewire 3 & Alpine.js (Memungkinkan interaksi *real-time* tanpa refresh halaman).
3.  **Styling**: Tailwind CSS (Desain modern, responsif, dan mendukung Dark Mode).
4.  **Database**: MySQL (Reliability dan integritas data tinggi).
5.  **Data Visualization**: ECharts 5 (Grafik interaktif untuk tren stok).
6.  **Icons**: Lucide Icons (Ikon vektor yang tajam dan elegan).
7.  **Export Engine**: Laravel Excel & DomPDF (Untuk laporan Excel dan PDF Surat Jalan).

---

## ✨ Fitur Utama & Fungsi

### 1. Dashboard "Overview" (Komando Pusat)
- **Ringkasan Metrik**: TOTAL JENIS, STOK KRITIS, BARANG MASUK & KELUAR secara instan.
- **Grafik Arus Barang**: Visualisasi tren harian/bulanan untuk memantau pergerakan gudang.
- **Aktivitas Terbaru**: Feed log transaksi secara real-time untuk pengawasan instan.
- **Smart Filter**: Filter cepat (Harian, Mingguan, Bulanan) dengan reaktivitas instan.

### 2. Master Data (Pondasi Sistem)
- **Manajemen Produk**: Input detail barang dengan kategori dan satuan.
- **Pemasok & Pelanggan**: Database rekanan bisnis untuk mempermudah tracking asal dan tujuan barang.
- **Bulk Import**: Mendukung impor data besar dalam hitungan detik menggunakan template Excel.

### 3. Inventarisasi & Operasi (Input Cepat)
- **Barang Masuk (IN)**: Pencatatan stok masuk dari pemasok dengan validasi otomatis.
- **Barang Keluar (OUT)**: Pencatatan pengiriman barang ke pelanggan.
- **Scan Barcode/QR**: Dukungan scanner via kamera browser untuk pencarian barang kilat.
- **Surat Jalan Otomatis**: Generate PDF Surat Jalan secara profesional langsung dari transaksi.

### 4. Pelaporan & Audit (Transparansi)
- **Stock Opname**: Fitur sinkronisasi stok fisik dengan sistem untuk audit berkala.
- **Laporan Transaksi**: Filter laporan mendalam yang dapat diekspor ke Excel.
- **Audit Logs**: Rekaman jejak setiap perubahan data (siapa melakukan apa, kapan) untuk keamanan tingkat tinggi.

### 5. Keamanan & Personalisasi
- **User Management**: Pengaturan hak akses (Administrator vs Pelaksana).
- **Dark Mode Support**: UI yang nyaman di mata untuk penggunaan jangka panjang di gudang.
- **Pengaturan Perusahaan**: Kustomisasi nama, logo, dan alamat perusahaan pada invoice/surat jalan.

---

## 📖 Cara Penggunaan (Panduan Singkat)

1.  **Persiapan**: Login ke sistem dan isi **Master Produk** (atau impor via Excel).
2.  **Transaksi**: Gunakan menu **Barang Masuk** saat stok tiba, atau **Barang Keluar** untuk pengiriman.
3.  **Monitoring**: Pantau pergerakan di **Dashboard** setiap pagi untuk melihat stok yang kritis (hampir habis).
4.  **Audit**: Lakukan **Stock Opname** setiap akhir bulan untuk memastikan data sistem 100% akurat dengan fisik.
5.  **Pelaporan**: Ekspor laporan dari menu **Laporan** untuk kebutuhan manajemen atau cetak Surat Jalan di detail transaksi.

---

**Dikerjakan Oleh**: [Nama Anda/Perusahaan Anda]  
**Status**: Production Ready 🚀
