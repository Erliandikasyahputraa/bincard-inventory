<div class="w-full">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white tracking-tight">Laporan & Export</h1>
        <p class="text-slate-400 text-sm mt-1">Export transaksi stok dan daily log ke berbagai format.</p>
    </div>
    
    <div class="bg-[#161B22] border border-[#30363D] rounded-2xl p-5 lg:p-6 mb-6 shadow-xl">
        <h2 class="font-bold text-slate-200 mb-4 flex items-center gap-2">
            <i data-lucide="printer" class="w-4 h-4 text-[#58A6FF]"></i> Export & Cetak
        </h2>
        <div class="flex flex-col lg:flex-row flex-wrap gap-4 items-end">
            <div class="w-full lg:w-auto">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Mulai Tanggal</label>
                <input type="date" wire:model.live="tanggalMulai" class="w-full lg:w-40 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-3 py-2 text-sm [color-scheme:dark]">
            </div>
            <div class="w-full lg:w-auto">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" wire:model.live="tanggalSelesai" class="w-full lg:w-40 bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-3 py-2 text-sm [color-scheme:dark]">
            </div>
            <div class="w-full lg:w-auto flex-1 max-w-xs">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Tipe Transaksi</label>
                <select wire:model.live="tipeTransaksi" class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-3 py-2 text-sm appearance-none">
                    <option value="">Semua Transaksi</option>
                    <option value="IN">Masuk (IN)</option>
                    <option value="OUT">Keluar (OUT)</option>
                    <option value="ADJUST">Penyesuaian (ADJUST)</option>
                </select>
            </div>
            <div class="w-full lg:w-auto flex-1">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Urutkan Log</label>
                <select wire:model.live="sortBy" class="w-full bg-[#0D1117] border border-[#30363D] hover:border-[#8B949E] rounded-xl text-slate-200 focus:bg-[#161B22] focus:border-[#58A6FF] focus:ring-1 focus:ring-[#58A6FF] outline-none transition-all px-3 py-2 text-sm appearance-none">
                    <option value="terbaru">Terbaru (Waktu)</option>
                    <option value="terlama">Terlama (Waktu)</option>
                    <option value="terbanyak">Terbanyak (Jml)</option>
                    <option value="terdikit">Sedikit (Jml)</option>
                </select>
            </div>
            
            <div class="w-full lg:w-auto flex flex-col sm:flex-row gap-2 mt-2 lg:mt-0">
                <a href="{{ route('laporan.export-transaksi', ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'tipeTransaksi' => $tipeTransaksi]) }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-[#21262D] border border-[#30363D] hover:bg-[#30363D] text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950]"></i> CSV Transaksi
                </a>
                <a href="{{ route('laporan.export-harian', ['tanggalMulai' => $tanggalMulai]) }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-[#21262D] border border-[#30363D] hover:bg-[#30363D] text-white font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-indigo-400"></i> Harian (Tgl Mulai)
                </a>
                <a href="{{ route('laporan.pdf', ['tanggalMulai' => $tanggalMulai, 'tanggalSelesai' => $tanggalSelesai, 'tipeTransaksi' => $tipeTransaksi]) }}"
                    class="inline-flex justify-center items-center px-4 py-2 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 text-rose-100 font-medium rounded-xl transition-colors text-sm whitespace-nowrap">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-rose-500"></i> Cetak PDF
                </a>
            </div>
        </div>
    </div>
    
    <div class="bg-[#161B22] border border-[#30363D] rounded-2xl overflow-hidden shadow-xl">
        <div class="px-6 py-4 border-b border-[#30363D] flex items-center justify-between">
            <h2 class="font-bold text-slate-200">Daily Log (Transaksi)</h2>
        </div>
        
        <div class="w-full overflow-x-auto no-scrollbar">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="border-b border-[#30363D] bg-[#0D1117] text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold whitespace-nowrap">Tanggal & Waktu</th>
                        <th class="px-6 py-4 font-semibold">Produk / Item</th>
                        <th class="px-6 py-4 font-semibold text-center w-24">Tipe</th>
                        <th class="px-6 py-4 font-semibold text-center w-24">Jumlah</th>
                        <th class="px-6 py-4 font-semibold text-right">Penanggung Jawab</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#30363D]">
                    @forelse($transaksi as $t)
                        <tr class="hover:bg-[#21262D] transition-colors group">
                            <td class="px-6 py-4 text-slate-400 text-xs font-mono">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-slate-200 text-sm font-medium">{{ $t->product->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded {{ $t->type == 'IN' ? 'bg-[#238636]/20 text-[#3FB950] border border-[#238636]/30' : ($t->type == 'OUT' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30') }}">
                                    {{ $t->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-white font-bold">{{ $t->quantity }}</td>
                            <td class="px-6 py-4 text-right text-slate-400 text-sm">{{ $t->user->name ?? 'Sistem' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="w-16 h-16 bg-[#21262D] rounded-full flex items-center justify-center mx-auto mb-4 border border-[#30363D]">
                                    <i data-lucide="clipboard-list" class="w-8 h-8 text-slate-500"></i>
                                </div>
                                <p class="text-slate-300 font-medium mb-1">Log kosong</p>
                                <p class="text-slate-500 text-sm">Tidak ada transaksi pada periode yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transaksi->hasPages())
        <div class="px-6 py-4 border-t border-[#30363D] bg-[#0D1117]/50">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>
