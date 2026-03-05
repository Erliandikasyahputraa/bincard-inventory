<div class="w-full">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Stock Opname</h1>
            <p class="text-slate-400 text-sm mt-1">Audit kesesuaian sistem dengan fisik gudang.</p>
        </div>
        @if(!$opname)
            <button type="button" wire:click="buatOpname" class="inline-flex justify-center items-center px-4 py-2.5 bg-[#1F6FEB] hover:bg-[#388BFD] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 text-sm whitespace-nowrap">
                <i data-lucide="folder-plus" class="w-4 h-4 mr-2"></i> Buat Sesi Baru
            </button>
        @endif
    </div>

    @if($opname)
        <div class="mb-6 p-5 sm:p-6 bg-[#161B22] border border-[#30363D] rounded-2xl shadow-xl flex flex-col gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <p class="font-bold text-white text-lg">Sesi Opname</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $opname->status == 'selesai' ? 'bg-[#238636]/20 text-[#3FB950]' : 'bg-orange-500/20 text-orange-400' }}">
                        {{ $opname->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-400 mb-1">Tanggal: <span class="text-slate-200">{{ $opname->tanggal_opname->format('d M Y') }}</span></p>
                <p class="text-xs text-slate-500">Mulai input angka fisik nyata pada kolom <span class="font-bold">Stok Fisik</span>, lalu tekan tombol Rekonsiliasi di bawah.</p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button type="button" wire:click="rekonsiliasi" wire:confirm="Sistem akan otomatis membuat log In/Out untuk menyamakan stok sistem dengan nilai fisik yang anda masukkan. Lanjutkan?"
                    class="inline-flex justify-center items-center px-4 py-2.5 bg-[#238636] hover:bg-[#2EA043] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#238636]/20 text-sm">
                    <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Rekonsiliasi Hasil
                </button>
                <a href="{{ route('opname.export', $opname->id) }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-[#21262D] border border-[#30363D] hover:bg-[#30363D] text-white font-medium rounded-xl transition-colors text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950]"></i> Export Data
                </a>
                <a href="{{ route('opname.index') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-500 font-bold border border-rose-500/20 rounded-xl transition-colors text-sm">
                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="bg-[#161B22] border border-[#30363D] rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px]">
                    <thead>
                        <tr class="border-b border-[#30363D] bg-[#0D1117] text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 font-semibold">SKU / Barcode</th>
                            <th class="px-6 py-4 font-semibold text-center">Sistem Saat Ini</th>
                            <th class="px-6 py-4 font-semibold text-center bg-[#238636]/5">Masukan Aktual (Fisik)</th>
                            <th class="px-6 py-4 font-semibold text-center">Estimasi Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#30363D]">
                        @foreach($opname->details as $d)
                            <tr class="hover:bg-[#21262D] transition-colors group">
                                <td class="px-6 py-4 text-slate-200 text-sm font-medium">{{ $d->product->name }}</td>
                                <td class="px-6 py-4 text-slate-400 text-xs font-mono">{{ $d->product->barcode }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-block px-3 py-1 bg-[#0D1117] border border-[#30363D] rounded-lg text-slate-300 font-bold font-mono">{{ $d->stok_sistem }}</span>
                                </td>
                                <td class="px-6 py-4 text-center bg-[#238636]/5">
                                    <input type="number" wire:model.live="stokFisik.{{ $d->product_id }}"
                                        class="w-24 text-center bg-[#0D1117] border border-[#30363D] focus:border-[#58A6FF] rounded-lg shadow-sm text-sm text-white focus:ring-1 focus:ring-[#58A6FF] placeholder-slate-600 outline-none transition-colors border-2 focus:border-2" min="0" placeholder="0">
                                </td>
                                @php $fisik = (int) ($stokFisik[$d->product_id] ?? $d->stok_fisik ?? 0); $sel = $fisik - $d->stok_sistem; @endphp
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold font-mono text-sm {{ $sel != 0 ? ($sel > 0 ? 'text-[#3FB950]' : 'text-rose-500') : 'text-slate-500 opacity-50' }}">
                                        {{ $fisik > 0 || $d->stok_fisik !== null ? ($sel > 0 ? '+'.$sel : $sel) : '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-[#161B22] border border-[#30363D] rounded-2xl overflow-hidden shadow-xl mt-6">
            <div class="px-6 py-5 border-b border-[#30363D]">
                <h2 class="text-sm font-bold text-slate-200">Riwayat Sesi Audit / Opname</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-[#30363D] bg-[#0D1117] text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Tanggal Berlangsung</th>
                            <th class="px-6 py-4 font-semibold text-center">Status Audit</th>
                            <th class="px-6 py-4 font-semibold text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#30363D]">
                        @forelse($daftarOpname as $o)
                            <tr class="hover:bg-[#21262D] transition-colors group">
                                <td class="px-6 py-4 text-slate-200 text-sm font-medium">{{ $o->tanggal_opname->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $o->status == 'selesai' ? 'bg-[#238636]/20 text-[#3FB950]' : 'bg-orange-500/20 text-orange-400' }}">
                                        {{ $o->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($o->status === 'draft')
                                            <a href="{{ route('opname.index') }}?opname={{ $o->id }}" class="inline-flex items-center text-[#58A6FF] hover:text-[#79C0FF] text-sm font-bold bg-[#1F6FEB]/10 px-3 py-1.5 rounded-lg transition-colors" title="Lanjut Audit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                        @endif
                                        <button type="button" wire:click="hapusSesi({{ $o->id }})" wire:confirm="Anda yakin ingin menghapus catatan sesi opname ini? Data yang terhapus tidak dapat dikembalikan." class="inline-flex items-center text-rose-500 hover:text-rose-400 text-sm font-bold bg-rose-500/10 px-3 py-1.5 rounded-lg transition-colors" title="Hapus Riwayat">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-slate-500">Belum ada rekam jejak Audit Gudang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($daftarOpname->hasPages())
                <div class="px-6 py-4 border-t border-[#30363D] bg-[#0D1117]/50">
                    {{ $daftarOpname->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
