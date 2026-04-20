<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Stock Opname</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Audit kesesuaian stok fisik gudang dengan data sistem.</p>
    </div>
</x-slot:header>

<div class="w-full">
    <div class="mb-6 flex flex-col sm:flex-row justify-end items-start sm:items-center gap-4">
        @if(!$opname)
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto mt-4 sm:mt-0">
                <div class="relative w-full sm:w-40 flex-shrink-0">
                    <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                    <input type="date" wire:model.live="tanggalBaru" class="pl-9 pr-4 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white" style="color-scheme: dark;">
                </div>
                <button type="button" wire:click="buatOpname" wire:loading.attr="disabled"
                    class="inline-flex justify-center items-center px-4 py-2.5 bg-[#10B981] dark:bg-blue-500 hover:bg-[#388BFD] disabled:opacity-50 text-white font-bold rounded-xl transition-colors shadow-lg shadow-[#1F6FEB]/20 text-sm whitespace-nowrap flex-shrink-0">
                    <i data-lucide="folder-plus" class="w-4 h-4 mr-2" wire:loading.remove wire:target="buatOpname"></i>
                    <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="buatOpname" style="display:none;"></i>
                    Buat Sesi Opname Baru
                </button>
            </div>
        @endif
    </div>

    @if($opname)
        {{-- Info sesi aktif --}}
        <div class="mb-6 p-5 sm:p-6 bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl shadow-xl flex flex-col gap-4 transition-colors duration-300 ease-in-out">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <p class="font-bold text-slate-900 dark:text-white text-lg">Sesi Opname</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $opname->status == 'selesai' ? 'bg-[#10B981] text-white' : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400' }}">
                        {{ $opname->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">Tanggal: <span class="text-slate-800 dark:text-slate-200">{{ $opname->tanggal_opname->format('d M Y') }}</span></p>

                @if($opname->status === 'draft')
                    {{-- B3: Pesan blind input --}}
                    <div class="mt-2 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl">
                        <i data-lucide="eye-off" class="w-4 h-4 text-amber-600 dark:text-amber-400 mt-0.5 shrink-0"></i>
                        <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                            <strong>Mode Input Aktif:</strong> Stok sistem tidak ditampilkan selama sesi berlangsung untuk menjaga objektivitas penghitungan. Masukkan jumlah stok fisik aktual yang Anda hitung langsung di lapangan. Selisih akan ditampilkan setelah rekonsiliasi dikonfirmasi.
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex flex-wrap gap-3">
                @if($opname->status === 'draft')
                    <button type="button" wire:click="rekonsiliasi"
                        wire:confirm="Sistem akan menyesuaikan stok berdasarkan nilai fisik yang Anda input. Selisih akan ditampilkan setelah konfirmasi. Lanjutkan?"
                        class="inline-flex justify-center items-center px-4 py-2.5 bg-[#10B981] hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-lg text-sm">
                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Konfirmasi & Rekonsiliasi
                    </button>
                @endif
                <a href="{{ route('opname.export', $opname->id) }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-[#3FB950]"></i> Export Data
                </a>
                <a href="{{ route('opname.index') }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold border border-rose-500/20 rounded-xl transition-colors text-sm">
                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Search --}}
        <div class="mb-4 relative max-w-md">
            <i data-lucide="search" wire:loading.remove wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
            <i data-lucide="loader-2" wire:loading wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
            <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()"
                wire:model.live.debounce.300ms="cariBarang"
                placeholder="Cari barcode / nama produk..."
                class="pl-9 pr-4 py-2.5 w-full bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white dark:placeholder-slate-500 shadow-sm">
        </div>

        {{-- Tabel opname --}}
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl transition-colors duration-300 ease-in-out">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Nama Produk</th>
                            <th class="px-6 py-4 font-semibold">SKU / Barcode</th>

                            @if($opname->status === 'draft')
                                {{-- B3: Blind mode — sembunyikan stok sistem & selisih --}}
                                <th class="px-6 py-4 font-semibold text-center bg-[#10B981] text-white">Input Stok Fisik (Aktual)</th>
                            @else
                                {{-- Setelah rekonsiliasi — tampilkan semua kolom --}}
                                <th class="px-6 py-4 font-semibold text-center">Stok Sistem</th>
                                <th class="px-6 py-4 font-semibold text-center bg-[#10B981] text-white">Stok Fisik (Input)</th>
                                <th class="px-6 py-4 font-semibold text-center">Selisih</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($details as $d)
                            <tr wire:key="detail-{{ $d->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3.5 text-slate-800 dark:text-slate-200 text-sm font-medium">{{ $d->product->name }}</td>
                                <td class="px-6 py-3.5 text-slate-500 dark:text-slate-400 text-xs font-mono">{{ $d->product->barcode }}</td>

                                @if($opname->status === 'draft')
                                    {{-- B3: Hanya input fisik, tanpa stok sistem & selisih --}}
                                    <td class="px-6 py-3 text-center bg-emerald-50 dark:bg-emerald-500/5">
                                        {{-- B1: Alpine menyimpan nilai lokal, server dipanggil on-blur/debounce --}}
                                        <div x-data="{ val: '{{ $d->stok_fisik ?? '' }}' }">
                                            <input type="number" x-model="val"
                                                x-on:input.debounce.800ms="$wire.setStokFisik({{ $d->product_id }}, val)"
                                                x-on:blur="$wire.setStokFisik({{ $d->product_id }}, val)"
                                                class="w-24 text-center bg-white dark:bg-slate-950 border-2 border-transparent focus:border-emerald-400 focus:ring-0 rounded-lg shadow-sm text-sm text-slate-900 dark:text-white outline-none transition-colors"
                                                min="0" placeholder="—">
                                        </div>
                                    </td>
                                @else
                                    {{-- Tampilkan semua kolom setelah rekonsiliasi --}}
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="inline-block px-3 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 font-bold font-mono text-sm">{{ $d->stok_sistem }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-center bg-emerald-50 dark:bg-emerald-500/5">
                                        <span class="font-bold font-mono text-sm text-slate-700 dark:text-slate-300">{{ $d->stok_fisik ?? '—' }}</span>
                                    </td>
                                    @php $sel = ($d->stok_fisik ?? 0) - $d->stok_sistem; @endphp
                                    <td class="px-6 py-3.5 text-center">
                                        <span class="font-bold font-mono text-sm {{ $d->stok_fisik !== null ? ($sel > 0 ? 'text-emerald-600' : ($sel < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400')) : 'text-slate-400' }}">
                                            {{ $d->stok_fisik !== null ? ($sel > 0 ? '+'.$sel : $sel) : '—' }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- B2: Pagination detail --}}
            @if($details->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">Menampilkan {{ $details->firstItem() }}–{{ $details->lastItem() }} dari {{ $details->total() }} produk</p>
                    {{ $details->links() }}
                </div>
            @else
                <div class="px-6 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <p class="text-xs text-slate-400">{{ $details->total() }} produk dalam sesi ini</p>
                </div>
            @endif
        </div>

    @else
        {{-- Riwayat sesi opname --}}
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl mt-6 transition-colors duration-300 ease-in-out">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200">Riwayat Sesi Audit / Opname</h2>
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <i data-lucide="search" wire:loading.remove wire:target="historySearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <i data-lucide="loader-2" wire:loading wire:target="historySearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                        <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()"
                            wire:model.live.debounce.300ms="historySearch"
                            placeholder="Cari ID atau nama validator..."
                            class="pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full sm:w-64 text-slate-900 dark:text-white dark:placeholder-slate-500">
                    </div>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                        <input type="date" wire:model.live="historyDate"
                            class="pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full sm:w-40 text-slate-900 dark:text-white" style="color-scheme: dark;">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold text-center">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($daftarOpname as $o)
                            <tr wire:key="opname-{{ $o->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium">{{ $o->tanggal_opname->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $o->status == 'selesai' ? 'bg-[#10B981] text-white' : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400' }}">
                                        {{ $o->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end items-center gap-2">
                                        @if($o->status === 'draft')
                                            <a href="{{ route('opname.index') }}?opname={{ $o->id }}" class="p-2 text-slate-500 hover:text-blue-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Lanjut Audit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <button type="button" wire:click="hapusSesi({{ $o->id }})"
                                                wire:confirm="Anda yakin ingin menghapus sesi opname ini? Data yang terhapus tidak dapat dikembalikan."
                                                class="p-2 text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button type="button" wire:click="batalRekonsiliasi({{ $o->id }})"
                                                wire:confirm="PERINGATAN: Membatalkan rekonsiliasi akan me-reverse semua penyesuaian stok secara otomatis. Yakin melanjutkan?"
                                                class="inline-flex items-center px-3 py-1.5 text-slate-500 hover:text-orange-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors text-xs font-semibold">
                                                <i data-lucide="rotate-ccw" class="w-4 h-4 mr-1"></i> Batal Rekonsiliasi
                                            </button>
                                        @endif
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
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                    {{ $daftarOpname->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
