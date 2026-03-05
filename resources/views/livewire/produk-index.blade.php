<div class="w-full">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Data Produk</h1>
            <p class="text-slate-400 text-sm mt-1">Kelola daftar seluruh inventaris gudang.</p>
        </div>
        
        <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3">
            <div class="relative group flex-1 md:w-64">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 w-4 h-4"></i>
                <input type="text" wire:model.live.debounce.300ms="cari" placeholder="Cari nama, barcode, SKU..."
                    class="w-full pl-10 pr-4 py-2.5 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 placeholder-slate-500 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all duration-300 text-sm">
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('produk.import') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-[#21262D] border border-[#30363D] hover:bg-[#30363D] text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-slate-400"></i> Import
                </a>
                <a href="{{ route('produk.tambah') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-[#238636] hover:bg-[#2EA043] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#238636]/20 text-sm whitespace-nowrap">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah
                </a>
            </div>
        </div>
    </div>
    <!-- Table Area -->
    <div class="bg-[#161B22] border border-[#30363D] rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr class="border-b border-[#30363D] bg-[#0D1117] text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Barcode / SKU</th>
                        <th class="px-6 py-4 font-semibold">Nama Produk</th>
                        <th class="px-6 py-4 font-semibold text-center whitespace-nowrap">Stok Saat Ini</th>
                        <th class="px-6 py-4 font-semibold text-center">Lokasi Rak</th>
                        <th class="px-6 py-4 font-semibold text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#30363D]">
                    @forelse($produk as $p)
                        <tr class="hover:bg-[#21262D] transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-slate-200 font-mono text-xs">{{ $p->barcode }}</span>
                                    <span class="text-slate-500 font-mono text-[10px] mt-0.5">{{ $p->sku }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-white font-medium text-sm block line-clamp-2">{{ $p->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-lg font-bold {{ $p->current_stock <= $p->min_stock ? 'text-rose-400' : 'text-[#58A6FF]' }}">{{ $p->current_stock }}</span>
                                    @if($p->current_stock <= $p->min_stock)
                                        <span class="text-[9px] text-rose-500 font-bold uppercase tracking-wider">Kritis</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-2 py-1 bg-[#0D1117] border border-[#30363D] rounded-md text-slate-400 text-xs font-mono">{{ $p->location ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('produk.edit', $p->id) }}" class="p-2 text-slate-400 hover:text-[#58A6FF] hover:bg-[#1F6FEB]/10 rounded-lg transition-colors" title="Edit Data">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <button type="button" wire:click="hapus({{ $p->id }})" wire:confirm="Seluruh riwayat transaksi produk ini (ledger) mungkin akan terpengaruh. Lanjutkan menghapus?"
                                        class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors" title="Hapus Permanen">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="w-16 h-16 bg-[#21262D] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#30363D]">
                                    <i data-lucide="package-search" class="w-8 h-8 text-slate-500"></i>
                                </div>
                                <p class="text-slate-300 font-medium mb-1">Tidak ada produk ditemukan</p>
                                <p class="text-slate-500 text-sm">Tambahkan produk baru atau import dari Excel.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($produk->hasPages())
        <div class="px-6 py-4 border-t border-[#30363D] bg-[#0D1117]/50">
            {{ $produk->links(data: ['scrollTo' => false]) }}
        </div>
        @endif
    </div>
</div>
