<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Panduan Penggunaan Sistem BINGO</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Dokumentasi teknis operasional untuk seluruh pengguna sistem.</p>
    </div>
</x-slot:header>

<div class="max-w-5xl w-full pb-12">
    <!-- Panduan Umum -->
    <div class="bg-white dark:bg-slate-900 border border-emerald-500/20 rounded-3xl shadow-lg overflow-hidden transition-colors mb-6">
        <div class="bg-emerald-50 dark:bg-emerald-900/20 p-5 md:p-6 border-b border-emerald-100 dark:border-emerald-800/30">
            <h2 class="text-lg md:text-xl font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-3">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                Panduan Operasional — Pengguna Umum
            </h2>
        </div>

        <div class="p-4 md:p-8 space-y-3">

            {{-- 1: Navigasi --}}
            <div x-data="{ open: true }" class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition-colors">
                    <span class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-3 text-sm md:text-base">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <i data-lucide="menu" class="w-4 h-4"></i>
                        </div>
                        1. Navigasi Sistem
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-5 text-sm text-slate-600 dark:text-slate-300 space-y-3 border-t border-slate-200 dark:border-slate-700 leading-relaxed">
                        <p>Sistem BINGO dapat diakses melalui perangkat handphone maupun komputer. Antarmuka sistem menyediakan panel menu yang dapat disembunyikan untuk memaksimalkan ruang layar.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Tombol Menu (ikon tiga garis):</strong> Terletak di pojok kiri atas. Ketuk tombol ini untuk membuka atau menutup panel navigasi.</li>
                            <li><strong>Mode Gelap/Terang:</strong> Ketuk ikon bulan atau matahari di pojok kanan atas untuk mengubah tema tampilan. Mode gelap disarankan untuk penggunaan malam hari.</li>
                            <li><strong>Dashboard:</strong> Halaman utama yang menampilkan ringkasan status gudang secara real-time, termasuk statistik stok dan notifikasi barang kritis.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 2: Data Barang --}}
            <div x-data="{ open: false }" class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition-colors">
                    <span class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-3 text-sm md:text-base">
                        <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <i data-lucide="package" class="w-4 h-4"></i>
                        </div>
                        2. Pengelolaan Data Barang (Master Produk)
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-5 text-sm text-slate-600 dark:text-slate-300 space-y-3 border-t border-slate-200 dark:border-slate-700 leading-relaxed">
                        <p>Menu <strong>Master Produk</strong> digunakan untuk menambah, mengubah, dan mengelola data barang gudang.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Melihat Daftar Barang:</strong> Geser tabel ke kiri untuk melihat tombol aksi (Edit, Lihat Kartu) pada layar handphone.</li>
                            <li><strong>Mengunggah Foto Barang:</strong> Pada form Tambah atau Edit Produk, ketuk tombol upload foto. Sistem mendukung pengambilan langsung dari kamera handphone atau galeri. Foto akan dikompres otomatis oleh sistem.</li>
                            <li><strong>Batas Stok Minimum:</strong> Isi kolom Batas Minimum Stok dengan angka yang sesuai. Apabila stok aktual berada di bawah nilai ini, sistem akan menandai barang tersebut sebagai <em>Stok Kritis</em> pada dashboard.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 3: Scan & Cetak --}}
            <div x-data="{ open: false }" class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition-colors">
                    <span class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-3 text-sm md:text-base">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                            <i data-lucide="scan-line" class="w-4 h-4"></i>
                        </div>
                        3. Scan dan Cetak Label Barcode (QR Code)
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-5 text-sm text-slate-600 dark:text-slate-300 space-y-3 border-t border-slate-200 dark:border-slate-700 leading-relaxed">
                        <p><strong>Cara Scan QR Code:</strong> Arahkan kamera handphone ke label QR yang tertempel di rak. Sistem akan membuka halaman Kartu Riwayat Barang (Bin Card) secara otomatis di browser. Tidak diperlukan aplikasi tambahan.</p>
                        <p><strong>Cara Cetak Label:</strong></p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Buka menu <strong>Master Data &gt; Cetak Barcode Massal</strong>.</li>
                            <li>Gunakan filter lokasi/rak untuk memilih label yang akan dicetak.</li>
                            <li>Tekan <strong>Ctrl+P</strong> (komputer) atau gunakan opsi cetak di browser.</li>
                            <li>Foto produk yang tampil di layar akan digantikan oleh QR Code saat proses cetak berlangsung, sehingga hasil cetak lebih hemat tinta.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 4: Transaksi --}}
            <div x-data="{ open: false }" class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition-colors">
                    <span class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-3 text-sm md:text-base">
                        <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <i data-lucide="truck" class="w-4 h-4"></i>
                        </div>
                        4. Pencatatan Barang Masuk dan Keluar
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-5 text-sm text-slate-600 dark:text-slate-300 space-y-3 border-t border-slate-200 dark:border-slate-700 leading-relaxed">
                        <p>Menu <strong>Barang Masuk</strong> dan <strong>Barang Keluar</strong> digunakan untuk mencatat mutasi stok.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li><strong>Pencatatan Multi-Item:</strong> Pilih produk dan masukkan jumlah, kemudian produk akan masuk ke daftar transaksi. Ulangi untuk produk berikutnya. Klik <strong>Simpan Transaksi</strong> setelah semua item selesai diinput.</li>
                            <li><strong>Surat Jalan:</strong> Setelah transaksi tersimpan, sistem menerbitkan Surat Jalan dalam format PDF yang dapat dicetak atau dikirimkan secara digital.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 5: Stock Opname --}}
            <div x-data="{ open: false }" class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                <button @click="open = !open" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition-colors">
                    <span class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-3 text-sm md:text-base">
                        <div class="w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-900/40 text-cyan-600 dark:text-cyan-400 flex items-center justify-center shrink-0">
                            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                        </div>
                        5. Stock Opname (Penghitungan Fisik Stok)
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-collapse>
                    <div class="p-5 text-sm text-slate-600 dark:text-slate-300 space-y-3 border-t border-slate-200 dark:border-slate-700 leading-relaxed">
                        <p>Menu <strong>Stock Opname</strong> digunakan untuk mencocokkan jumlah stok fisik di gudang dengan data yang tercatat di sistem.</p>
                        <ul class="list-disc pl-5 space-y-2">
                            <li>Buka menu Stock Opname, lalu klik <strong>Mulai Sesi Opname</strong>.</li>
                            <li>Masukkan jumlah stok fisik aktual pada kolom yang tersedia untuk setiap barang.</li>
                            <li>Sistem akan menghitung selisih antara stok sistem dan stok fisik secara otomatis.</li>
                            <li>Simpan hasil opname. Data stok sistem akan diperbarui sesuai hasil penghitungan fisik.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Panduan Admin --}}
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('admin')))
    <div class="bg-indigo-50 dark:bg-indigo-950/20 border-2 border-indigo-200 dark:border-indigo-800 rounded-3xl shadow-lg overflow-hidden relative mb-6">
        <div class="absolute top-0 right-0 py-1.5 px-4 bg-indigo-600 text-white text-xs font-bold rounded-bl-xl uppercase tracking-widest flex items-center gap-1.5">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Administrator
        </div>

        <div class="p-5 md:p-8 space-y-5 pt-10 md:pt-10">
            <h2 class="text-lg md:text-xl font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-3 border-b border-indigo-200 dark:border-indigo-700 pb-4">
                <i data-lucide="key" class="w-5 h-5"></i>
                Panduan Administrasi Sistem
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Import --}}
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-indigo-100 dark:border-indigo-900 shadow-sm">
                    <h3 class="font-bold text-indigo-800 dark:text-indigo-300 flex items-center gap-2 mb-3 text-sm md:text-base">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-500"></i>
                        Import Data Massal (Excel)
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">
                        Digunakan untuk memasukkan data barang dalam jumlah besar sekaligus. Sistem menangani data duplikat (barcode yang sudah ada) dengan dua opsi:
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 bg-slate-50 dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                            <i data-lucide="skip-forward" class="w-4 h-4 mt-0.5 text-blue-500 shrink-0"></i>
                            <div>
                                <strong class="text-slate-800 dark:text-slate-200 block text-xs uppercase tracking-wide mb-1">Skip (Lewati)</strong>
                                <span class="text-xs text-slate-500 leading-relaxed">Data di Excel diabaikan apabila barcode sudah terdaftar di sistem. Data yang ada tidak berubah.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 bg-rose-50 dark:bg-rose-900/10 p-3 rounded-xl border border-rose-100 dark:border-rose-900/30">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mt-0.5 text-rose-500 shrink-0"></i>
                            <div>
                                <strong class="text-rose-700 dark:text-rose-400 block text-xs uppercase tracking-wide mb-1">Overwrite (Timpa)</strong>
                                <span class="text-xs text-slate-500 leading-relaxed">Data di sistem diperbarui menggunakan data dari Excel, termasuk lokasi rak, batas stok, dan kuantitas. Riwayat transaksi tidak terhapus.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hapus Massal --}}
                <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-indigo-100 dark:border-indigo-900 shadow-sm">
                    <h3 class="font-bold text-indigo-800 dark:text-indigo-300 flex items-center gap-2 mb-3 text-sm md:text-base">
                        <i data-lucide="trash-2" class="w-4 h-4 text-red-500"></i>
                        Hapus Data Massal (Bulk Delete)
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
                        Digunakan untuk menghapus beberapa data produk sekaligus tanpa perlu akses terminal.
                    </p>
                    <ul class="text-sm text-slate-600 dark:text-slate-400 space-y-2 list-disc pl-5 leading-relaxed">
                        <li>Buka menu <strong>Master Produk</strong>.</li>
                        <li>Centang kotak pada baris produk yang akan dihapus. Tersedia opsi <em>Pilih Semua</em>.</li>
                        <li>Klik tombol <strong>Hapus Produk Terpilih</strong> yang muncul di bagian atas tabel.</li>
                    </ul>
                    <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/30 rounded-xl">
                        <p class="text-xs text-red-700 dark:text-red-400 leading-relaxed">
                            <strong>Perhatian:</strong> Menghapus data produk akan menghapus seluruh riwayat transaksi yang terkait secara permanen. Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
