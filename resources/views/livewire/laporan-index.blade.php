<div class="w-full">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Laporan & Export</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Export transaksi stok dan daily log ke berbagai format.</p>
    </div>
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 lg:p-6 mb-6 shadow-xl transition-colors duration-300 ease-in-out">
        <h2 class="font-bold text-slate-800 dark:text-slate-200 mb-4 flex items-center gap-2 transition-colors duration-300 ease-in-out">
            <i data-lucide="printer" class="w-4 h-4 text-blue-500 transition-colors duration-300 ease-in-out"></i> Export & Cetak
        </h2>
        <div class="flex flex-col lg:flex-row flex-wrap gap-4 items-end">
            <div class="w-full lg:w-auto">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Mulai Tanggal</label>
                <input type="date" wire:model.live="tanggalMulai" class="w-full lg:w-40 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-3 py-2 text-sm [color-scheme:dark]">
            </div>
            <div class="w-full lg:w-auto">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Sampai Tanggal</label>
                <input type="date" wire:model.live="tanggalSelesai" class="w-full lg:w-40 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-3 py-2 text-sm [color-scheme:dark]">
            </div>
            <div class="w-full lg:w-auto flex-1 max-w-xs">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Tipe Transaksi</label>
                <select wire:model.live="tipeTransaksi" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-3 py-2 text-sm appearance-none">
                    <option value="">Semua Transaksi</option>
                    <option value="IN">Masuk (IN)</option>
                    <option value="OUT">Keluar (OUT)</option>
                    <option value="ADJUST">Penyesuaian (ADJUST)</option>
                </select>
            </div>
            <div class="w-full lg:w-auto flex-1">
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5 transition-colors duration-300 ease-in-out">Urutkan Log</label>
                <select wire:model.live="sortBy" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all px-3 py-2 text-sm appearance-none">
                    <option value="terbaru">Terbaru (Waktu)</option>
                    <option value="terlama">Terlama (Waktu)</option>
                    <option value="terbanyak">Terbanyak (Jml)</option>
                    <option value="terdikit">Sedikit (Jml)</option>
                </select>
            </div>
            
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-2 mt-2 lg:mt-0">
                <a href="{{ route('laporan.export-transaksi', ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'tipeTransaksi' => $tipeTransaksi]) }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950] transition-colors duration-300 ease-in-out"></i> CSV Transaksi
                </a>
                <a href="{{ route('laporan.export-harian', ['tanggalMulai' => $tanggalMulai]) }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-indigo-600 dark:text-indigo-400 transition-colors duration-300 ease-in-out"></i> Harian (Tgl Mulai)
                </a>
                <a href="{{ route('laporan.pdf', ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'tipeTransaksi' => $tipeTransaksi]) }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-700 dark:text-rose-100 font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-rose-600 dark:text-rose-500 transition-colors duration-300 ease-in-out"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-colors duration-300 ease-in-out">
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
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded {{ $t->type == 'IN' ? 'bg-emerald-600 dark:bg-emerald-500/20 text-[#3FB950] border border-[#238636]/30' : ($t->type == 'OUT' ? 'bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-500/30' : 'bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-500/30') }} transition-colors duration-300 ease-in-out">
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
