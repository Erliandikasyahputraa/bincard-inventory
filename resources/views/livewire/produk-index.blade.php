<div class="w-full">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Data Produk</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300 ease-in-out">Kelola daftar seluruh inventaris gudang.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3">
            <div class="relative group flex-1 md:w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 w-4 h-4 transition-colors duration-300 ease-in-out"></i>
                <input type="text" wire:model.live.debounce.300ms="cari" placeholder="Cari nama, barcode, SKU..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 dark:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 outline-none transition-all duration-300 text-sm">
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('produk.import') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-slate-500 dark:text-slate-400 transition-colors duration-300 ease-in-out"></i> Import
                </a>
                <a href="{{ route('produk.tambah') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 text-sm whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah
                </a>
            </div>
        </div>
    </div>
    <!-- Table Area -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-colors duration-300 ease-in-out">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px] transition-colors duration-300 ease-in-out">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Barcode / SKU</th>
                        <th class="px-6 py-4 font-semibold">Nama Produk</th>
                        <th class="px-6 py-4 font-semibold text-center whitespace-nowrap transition-colors duration-300 ease-in-out">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-semibold text-center transition-colors duration-300 ease-in-out">Lokasi Rak</th>
                        <th class="px-6 py-4 font-semibold text-right transition-colors duration-300 ease-in-out">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($produk as $p)
                        <tr class="hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-800 dark:text-slate-200 font-mono text-xs transition-colors duration-300 ease-in-out">{{ $p->barcode }}</span>
                                    <span class="text-slate-500 font-mono text-[10px] mt-0.5 transition-colors duration-300 ease-in-out">{{ $p->sku }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-900 dark:text-white font-medium text-sm block line-clamp-2 transition-colors duration-300 ease-in-out">{{ $p->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-lg font-bold {{ $p->current_stock <= $p->min_stock ? 'text-rose-600 dark:text-rose-400' : 'text-blue-500' }} transition-colors duration-300 ease-in-out">{{ $p->current_stock }}</span>
                                    @if($p->current_stock <= $p->min_stock)
                                        <span class="text-[9px] text-rose-600 dark:text-rose-500 font-bold uppercase tracking-wider transition-colors duration-300 ease-in-out">Kritis</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                <span class="inline-block px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-md text-slate-500 dark:text-slate-400 text-xs font-mono transition-colors duration-300 ease-in-out">{{ $p->location ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right transition-colors duration-300 ease-in-out">
                                <div class="flex justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('produk.edit', $p->id) }}" class="p-2 text-slate-500 dark:text-slate-400 hover:text-blue-500 hover:bg-blue-600 dark:bg-blue-500/10 rounded-lg transition-colors" title="Edit Data">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" wire:click="hapus({{ $p->id }})" wire:confirm="Seluruh riwayat transaksi produk ini (ledger) mungkin akan terpengaruh. Lanjutkan menghapus?"
                                        class="p-2 text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center transition-colors duration-300 ease-in-out">
                                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out">
                                    <i data-lucide="package-search" class="w-8 h-8 text-slate-500 transition-colors duration-300 ease-in-out"></i>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 font-medium mb-1 transition-colors duration-300 ease-in-out">Tidak ada produk ditemukan</p>
                                <p class="text-slate-500 text-sm transition-colors duration-300 ease-in-out">Tambahkan produk baru atau import dari Excel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($produk->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 transition-colors duration-300 ease-in-out">
            {{ $produk->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </div>
</div>
