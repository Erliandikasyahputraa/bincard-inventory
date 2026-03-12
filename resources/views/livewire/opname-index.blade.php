<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Stock Opname</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Audit kesesuaian sistem dengan fisik gudang.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <div class="mb-6 flex flex-col sm:flex-row justify-end items-start sm:items-center gap-4">
        @if(!$opname)
            <div class="flex items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                <div class="relative w-full sm:w-40">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                    <input type="date" wire:model.live="tanggalBaru" class="pl-9 pr-4 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white" style="color-scheme: dark;">
                </div>
                <button type="button" wire:click="buatOpname" class="inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 dark:bg-blue-500 hover:bg-[#388BFD] text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 text-sm whitespace-nowrap">
                    <i data-lucide="folder-plus" class="w-4 h-4 mr-2"></i> Buat Sesi Opname Baru
                </button>
            </div>
        @endif
    </div>

    @if($opname)
        <div class="mb-6 p-5 sm:p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl flex flex-col gap-4 transition-colors duration-300 ease-in-out">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <p class="font-bold text-slate-900 dark:text-white text-lg transition-colors duration-300 ease-in-out">Sesi Opname</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $opname->status == 'selesai' ? 'bg-emerald-600 text-white dark:bg-emerald-500/20 dark:text-[#3FB950]' : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400' }} transition-colors duration-300 ease-in-out">
                        {{ $opname->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1 transition-colors duration-300 ease-in-out">Tanggal: <span class="text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">{{ $opname->tanggal_opname->format('d M Y') }}</span></p>
                <p class="text-xs text-slate-500 transition-colors duration-300 ease-in-out">Mulai input angka fisik nyata pada kolom <span class="font-bold">Stok Fisik</span>, lalu tekan tombol Rekonsiliasi di bawah.</p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button type="button" wire:click="rekonsiliasi" wire:confirm="Sistem akan otomatis membuat log In/Out untuk menyamakan stok sistem dengan nilai fisik yang anda masukkan. Lanjutkan?"
                    class="inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 dark:bg-emerald-500 hover:bg-emerald-700 dark:hover:bg-emerald-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-emerald-600/20 dark:shadow-emerald-500/20 text-sm">
                    <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Rekonsiliasi Hasil
                </button>
                <a href="{{ route('opname.export', $opname->id) }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950] transition-colors duration-300 ease-in-out"></i> Export Data
                </a>
                <a href="{{ route('opname.index') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-500 font-bold border border-rose-500/20 rounded-xl transition-colors text-sm">
                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="mb-4 relative max-w-md">
            <i data-lucide="search" wire:loading.remove wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
            <i data-lucide="loader-2" wire:loading wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
            <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()" wire:model.live.debounce.300ms="cariBarang" placeholder="Cari barcode / nama produk dalam sesi ini..." 
                class="pl-9 pr-4 py-2.5 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white dark:placeholder-slate-500 shadow-sm">
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-colors duration-300 ease-in-out">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[800px] transition-colors duration-300 ease-in-out">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">
                            <th class="px-6 py-4 font-semibold whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 font-semibold">SKU / Barcode</th>
                            <th class="px-6 py-4 font-semibold text-center transition-colors duration-300 ease-in-out">Sistem Saat Ini</th>
                            <th class="px-6 py-4 font-semibold text-center bg-emerald-600 text-white dark:bg-emerald-500/5 dark:text-slate-400 transition-colors duration-300 ease-in-out">Masukan Aktual (Fisik)</th>
                            <th class="px-6 py-4 font-semibold text-center transition-colors duration-300 ease-in-out">Estimasi Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($details as $d)
                            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                                <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium transition-colors duration-300 ease-in-out">{{ $d->product->name }}</td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-mono transition-colors duration-300 ease-in-out">{{ $d->product->barcode }}</td>
                                <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                    <span class="inline-block px-3 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-slate-600 dark:text-slate-300 font-bold font-mono transition-colors duration-300 ease-in-out">{{ $d->stok_sistem }}</span>
                                </td>
                                <td class="px-6 py-4 text-center bg-emerald-600 dark:bg-emerald-500/5 transition-colors duration-300 ease-in-out">
                                    <input type="number" wire:model.live="stokFisik.{{ $d->product_id }}"
                                        class="w-24 text-center bg-white dark:bg-slate-950 border-transparent dark:border-slate-800 focus:border-white focus:ring-2 focus:ring-white dark:focus:ring-blue-500 rounded-lg shadow-sm text-sm text-slate-900 dark:text-white placeholder-slate-400 outline-none transition-colors border-2" min="0" placeholder="0">
                                </td>
                                @php $fisik = (int) ($stokFisik[$d->product_id] ?? $d->stok_fisik ?? 0); $sel = $fisik - $d->stok_sistem; @endphp
                                <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                    <span class="font-bold font-mono text-sm {{ $sel != 0 ? ($sel > 0 ? 'text-[#3FB950]' : 'text-rose-600 dark:text-rose-500') : 'text-slate-500 opacity-50' }} transition-colors duration-300 ease-in-out">
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
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl mt-6 transition-colors duration-300 ease-in-out">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 transition-colors duration-300 ease-in-out flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">Riwayat Sesi Audit / Opname</h2>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <i data-lucide="search" wire:loading.remove wire:target="historySearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <i data-lucide="loader-2" wire:loading wire:target="historySearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                        <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()" wire:model.live.debounce.300ms="historySearch" placeholder="Cari Sesi ID atau Nama Validator..." 
                            class="pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none w-full sm:w-64 text-slate-900 dark:text-white dark:placeholder-slate-500">
                    </div>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="date" wire:model.live="historyDate" 
                            class="pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none w-full sm:w-40 text-slate-900 dark:text-white" style="color-scheme: dark;">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px] transition-colors duration-300 ease-in-out">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider transition-colors duration-300 ease-in-out">
                            <th class="px-6 py-4 font-semibold">Tanggal Berlangsung</th>
                            <th class="px-6 py-4 font-semibold text-center transition-colors duration-300 ease-in-out">Status Audit</th>
                            <th class="px-6 py-4 font-semibold text-right transition-colors duration-300 ease-in-out">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($daftarOpname as $o)
                            <tr class="hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors group">
                                <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium transition-colors duration-300 ease-in-out">{{ $o->tanggal_opname->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center transition-colors duration-300 ease-in-out">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $o->status == 'selesai' ? 'bg-emerald-600 text-white dark:bg-emerald-500/20 dark:text-[#3FB950]' : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400' }} transition-colors duration-300 ease-in-out">
                                        {{ $o->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right transition-colors duration-300 ease-in-out">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($o->status === 'draft')
                                            <a href="{{ route('opname.index') }}?opname={{ $o->id }}" class="inline-flex items-center text-blue-500 hover:text-[#79C0FF] text-sm font-bold bg-blue-600 dark:bg-blue-500/10 px-3 py-1.5 rounded-lg transition-colors" title="Lanjut Audit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <button type="button" wire:click="hapusSesi({{ $o->id }})" wire:confirm="Anda yakin ingin menghapus catatan sesi opname ini? Data yang terhapus tidak dapat dikembalikan." class="inline-flex items-center text-rose-600 dark:text-rose-500 hover:text-rose-600 dark:text-rose-400 text-sm font-bold bg-rose-500/10 px-3 py-1.5 rounded-lg transition-colors" title="Hapus Riwayat">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button type="button" wire:click="batalRekonsiliasi({{ $o->id }})" wire:confirm="PERINGATAN! Membatalkan rekonsiliasi akan DENGAN SEGERA menarik kembali/mereverse semua penyesuaian stok yang terjadi di sesi ini secara otomatis dari sistem. Apakah anda yakin?" class="inline-flex items-center text-orange-600 dark:text-orange-400 font-bold bg-orange-500/10 px-3 py-1.5 rounded-lg transition-colors text-xs" title="Batal Rekonsiliasi">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-1"></i> Batal Rekonsiliasi
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-slate-500 transition-colors duration-300 ease-in-out">Belum ada rekam jejak Audit Gudang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($daftarOpname->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 transition-colors duration-300 ease-in-out">
                    {{ $daftarOpname->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
