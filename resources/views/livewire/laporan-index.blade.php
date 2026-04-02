<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Laporan & Export</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Export transaksi stok dan daily log ke format XLS/PDF.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <!-- Export Panels Split -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Panel 1: Laporan Umum Transaksi -->
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-2xl p-5 lg:p-6 shadow-xl transition-colors duration-300 ease-in-out flex flex-col">
            <h2 class="font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2 transition-colors duration-300 ease-in-out">
                <i data-lucide="printer" class="w-4 h-4 text-blue-500 transition-colors duration-300 ease-in-out"></i> Laporan Jurnal Transaksi
            </h2>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Mulai Tanggal</label>
                    <input type="date" wire:model.live="tanggalMulai" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 transition-all px-3 py-2 text-sm outline-none [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Sampai Tanggal</label>
                    <input type="date" wire:model.live="tanggalSelesai" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 transition-all px-3 py-2 text-sm outline-none [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div class="col-span-2">
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Filter Tipe Transaksi</label>
                    <select wire:model.live="tipeTransaksi" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 transition-all px-3 py-2 text-sm outline-none appearance-none cursor-pointer">
                        <option value="">Semua Transaksi</option>
                        <option value="IN">Barang Masuk (IN)</option>
                        <option value="OUT">Barang Keluar (OUT)</option>
                        <option value="ADJUST">Penyesuaian (ADJUST)</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-3 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/50">
                <a href="{{ route('laporan.export-transaksi', ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'tipeTransaksi' => $tipeTransaksi]) }}"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950] transition-colors duration-300 ease-in-out"></i> Unduh Excel
                </a>
                <a href="{{ route('laporan.pdf', ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'tipeTransaksi' => $tipeTransaksi]) }}"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out"></i> Cetak PDF
                </a>
            </div>
        </div>

        <!-- Panel 2: Rekap Surat Jalan Harian -->
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-2xl p-5 lg:p-6 shadow-xl transition-colors duration-300 ease-in-out flex flex-col">
            <h2 class="font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2 transition-colors duration-300 ease-in-out">
                <i data-lucide="calendar-check" class="w-4 h-4 text-indigo-500 transition-colors duration-300 ease-in-out"></i> Rekap Surat Jalan Harian
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 transition-colors duration-300 ease-in-out leading-relaxed pr-4">
                Laporan ini ditarik spesifik hanya untuk memantau data pengeluaran (Barang Keluar) beserta detail Surat Jalan yang divalidasi dan dikeluarkan sepanjang satu hari penuh. 
            </p>
            <div class="mb-5">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Pilih Tanggal Surat Jalan</label>
                <input type="date" wire:model.live="tanggalMulai" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:border-indigo-500 dark:border-indigo-400 focus:ring-1 focus:ring-indigo-500 transition-all px-3 py-2 text-sm outline-none [color-scheme:light] dark:[color-scheme:dark]">
            </div>
            
            <div class="flex gap-3 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/50">
                <a href="{{ route('laporan.export-harian', ['tanggalMulai' => $tanggalMulai]) }}"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950] transition-colors duration-300 ease-in-out"></i> Unduh Excel
                </a>
                <a href="{{ route('laporan.pdf-harian', ['tanggalMulai' => $tanggalMulai]) }}"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-rose-700 dark:text-rose-400 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out"></i> Cetak PDF
                </a>
            </div>
        </div>

        <!-- Panel 3: Data Stok Barang -->
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-2xl p-5 lg:p-6 shadow-xl transition-colors duration-300 ease-in-out flex flex-col">
            <h2 class="font-bold text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-2 transition-colors duration-300 ease-in-out">
                <i data-lucide="boxes" class="w-4 h-4 text-emerald-500 transition-colors duration-300 ease-in-out"></i> Data Stok Barang
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 transition-colors duration-300 ease-in-out leading-relaxed pr-4">
                Snapshot kondisi stok seluruh produk saat ini. Berisi Kode Material, Deskripsi, Lokasi, UoM, dan jumlah stok aktual beserta status (Normal, Kritis, Habis).
            </p>
            <div class="flex gap-3 mt-auto pt-4 border-t border-slate-100 dark:border-slate-800/50">
                <a href="{{ route('laporan.export-stok-barang') }}"
                    class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-bold rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2"></i> Unduh Excel
                </a>
            </div>
        </div>

    </div>

    <!-- Table Toolbar: Sort Control -->
    <div class="flex flex-col sm:flex-row justify-between items-end sm:items-center gap-4 mb-4">
        <h2 class="font-bold text-slate-800 dark:text-slate-200 px-1 transition-colors duration-300 ease-in-out">Log Aktivitas Tabel</h2>
        <div class="w-full sm:w-48 relative">
            <i data-lucide="arrow-down-up" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
            <select wire:model.live="sortBy" class="w-full bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all pl-9 pr-8 py-2.5 text-sm appearance-none shadow-sm cursor-pointer">
                <option value="terbaru">Terbaru (Waktu)</option>
                <option value="terlama">Terlama (Waktu)</option>
                <option value="terbanyak">Terbanyak (Jumlah)</option>
                <option value="terdikit">Sedikit (Jumlah)</option>
            </select>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 card-shadow border border-[#E2E8F0] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-colors duration-300 ease-in-out">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between transition-colors duration-300 ease-in-out">
            <h2 class="font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">Daily Log (Transaksi)</h2>
        </div>
        
        <div class="w-full overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px] transition-colors duration-300 ease-in-out">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Tanggal & Waktu</th>
                        <th class="px-6 py-4 font-semibold">Produk / Item</th>
                        <th class="px-6 py-4 font-semibold text-center w-24 transition-colors duration-300 ease-in-out">Tipe</th>
                        <th class="px-6 py-4 font-semibold text-center w-24 transition-colors duration-300 ease-in-out">Jumlah</th>
                        <th class="px-6 py-4 font-semibold text-right transition-colors duration-300 ease-in-out">Penanggung Jawab</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($transaksi as $t)
                        <tr class="hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-mono transition-colors duration-300 ease-in-out">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium transition-colors duration-300 ease-in-out">{{ $t->product->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded {{ $t->type == 'IN' ? 'bg-[#16A34A] dark:bg-emerald-500/20 text-[#3FB950] border border-[#238636]/30' : ($t->type == 'OUT' ? 'bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-blue-500/20 text-[#3B82F6] dark:text-blue-400 border border-blue-500/30') }} transition-colors duration-300 ease-in-out">
                                    {{ $t->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-900 dark:text-white font-bold transition-colors duration-300 ease-in-out">{{ $t->quantity }}</td>
                            <td class="px-6 py-4 text-right text-slate-500 dark:text-slate-400 text-sm transition-colors duration-300 ease-in-out">{{ $t->user->name ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center transition-colors duration-300 ease-in-out">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                                    <i data-lucide="clipboard-list" class="w-8 h-8 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 font-medium mb-1 transition-colors duration-300 ease-in-out">Log kosong</p>
                                <p class="text-slate-500 text-sm transition-colors duration-300 ease-in-out">Tidak ada transaksi pada periode yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transaksi->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 transition-colors duration-300 ease-in-out">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>

