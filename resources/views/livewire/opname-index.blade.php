<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Stock Opname</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Audit kesesuaian stok fisik gudang dengan data sistem.</p>
    </div>
</x-slot:header>

<div class="w-full">

    {{-- ═══════════════════════════════ TAMPILAN SESI AKTIF ═══════════════════════════════ --}}
    @if($opname)

        {{-- Info sesi --}}
        <div class="mb-6 p-5 sm:p-6 bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl shadow-xl flex flex-col gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <p class="font-bold text-slate-900 dark:text-white text-lg">Sesi Opname</p>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                        {{ $opname->status == 'selesai' ? 'bg-emerald-500 text-white' : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400' }}">
                        {{ $opname->status }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Tanggal: <span class="text-slate-800 dark:text-slate-200">{{ $opname->tanggal_opname->format('d M Y') }}</span>
                </p>

                @if($opname->status === 'draft')
                    <div class="mt-3 flex items-start gap-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl">
                        <i data-lucide="eye-off" class="w-4 h-4 text-amber-600 dark:text-amber-400 mt-0.5 shrink-0"></i>
                        <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                            <strong>Mode Input Aktif:</strong> Stok sistem disembunyikan selama sesi berlangsung untuk menjaga objektivitas penghitungan. Masukkan jumlah stok fisik aktual. Selisih akan ditampilkan setelah rekonsiliasi dikonfirmasi.
                        </p>
                    </div>
                @else
                    <div class="mt-3 flex items-start gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-xl">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 mt-0.5 shrink-0"></i>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300 leading-relaxed">
                            <strong>Rekonsiliasi Selesai.</strong> Stok sistem telah disesuaikan. Kolom selisih menampilkan perbedaan — hijau berarti surplus, merah berarti minus dibanding catatan sistem.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Tombol aksi --}}
            <div class="flex flex-wrap gap-3">
                @if($opname->status === 'draft')
                    <button type="button" wire:click="rekonsiliasi"
                        wire:confirm="Sistem akan menyesuaikan stok untuk semua produk yang Anda input. Selisih akan ditampilkan setelah konfirmasi. Lanjutkan?"
                        class="inline-flex items-center px-4 py-2.5 bg-emerald-500 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-lg text-sm">
                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i> Konfirmasi & Rekonsiliasi
                    </button>
                @endif
                <a href="{{ route('opname.export', $opname->id) }}"
                   class="inline-flex items-center px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-medium rounded-xl transition-colors text-sm">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 mr-2 text-emerald-500"></i>
                    {{ $opname->status === 'draft' ? 'Backup Data (Excel)' : 'Unduh Laporan Hasil' }}
                </a>
                <button type="button" wire:click="tutupSesi"
                    class="inline-flex items-center px-4 py-2.5 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold border border-rose-500/20 rounded-xl transition-colors text-sm">
                    <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i> Kembali ke Riwayat
                </button>
            </div>
        </div>

        {{-- Filter & search dalam sesi - compact 1 baris --}}
        <div class="flex flex-wrap items-center gap-2 mb-4">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[160px]">
                <i data-lucide="search" wire:loading.remove wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                <i data-lucide="loader-2" wire:loading wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()"
                    wire:model.live.debounce.300ms="cariBarang"
                    placeholder="Cari produk..."
                    class="pl-9 pr-3 py-2 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white dark:placeholder-slate-500 shadow-sm transition-all">
            </div>

            {{-- Lorong Filter --}}
            @if($aisles->count() > 0)
            <div class="relative shrink-0 min-w-[110px]">
                <i data-lucide="split" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5 pointer-events-none"></i>
                <select wire:model.live="filterAisle"
                    class="pl-8 pr-7 py-2 w-full bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900/30 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 outline-none appearance-none text-emerald-700 dark:text-emerald-400 font-bold transition-all cursor-pointer shadow-sm">
                    <option value="">Lorong</option>
                    @foreach($aisles as $a)
                        <option value="{{ $a }}">Lorong {{ $a }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-emerald-400 w-3 h-3 pointer-events-none"></i>
            </div>
            @endif

            {{-- Rak Filter --}}
            <div class="relative shrink-0 min-w-[120px]">
                <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5 pointer-events-none"></i>
                <select wire:model.live="filterRak"
                    class="pl-8 pr-7 py-2 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 outline-none appearance-none text-slate-700 dark:text-slate-200 transition-all cursor-pointer shadow-sm">
                    <option value="">Semua Rak</option>
                    @foreach($raks as $rak)
                        <option value="{{ $rak }}">{{ $rak }}</option>
                    @endforeach
                </select>
                <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 w-3 h-3 pointer-events-none"></i>
            </div>

            {{-- Sort pills --}}
            @php
                $sortPills = ['name' => 'Nama', 'barcode' => 'Barcode', 'location' => 'Rak'];
                if($opname->status === 'selesai') $sortPills['selisih'] = 'Selisih';
            @endphp
            @foreach($sortPills as $field => $label)
                <button type="button" wire:click="toggleDetailSort('{{ $field }}')"
                    class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-semibold border transition-all
                        {{ $detailSortField === $field
                            ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                            : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-blue-400 hover:text-blue-600' }}">
                    {{ $label }}
                    @if($detailSortField === $field)
                        <i data-lucide="{{ $detailSortDir === 'asc' ? 'arrow-up' : 'arrow-down' }}" class="w-3.5 h-3.5"></i>
                    @else
                        <i data-lucide="arrow-up-down" class="w-2.5 h-2.5 text-slate-300 opacity-50"></i>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Tabel detail sesi --}}
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-4 w-12">Foto</th>
                            <th class="px-5 py-4">Nama Produk</th>
                            <th class="px-5 py-4">Rak / Barcode</th>

                            @if($opname->status === 'draft')
                                <th class="px-5 py-4 text-center bg-emerald-500 text-white w-40">Input Stok Fisik</th>
                            @else
                                <th class="px-5 py-4 text-center w-28">Stok Sistem</th>
                                <th class="px-5 py-4 text-center bg-emerald-500 text-white w-28">Stok Fisik</th>
                                <th class="px-5 py-4 text-center w-28">Selisih</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($details as $d)
                            <tr wire:key="detail-{{ $d->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-5 py-3">
                                    @if($d->product->image_path)
                                        <button @click="imgUrl = '{{ asset('storage/' . $d->product->image_path) }}'; showImg = true"
                                                class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden hover:ring-2 hover:ring-blue-500 transition-all">
                                            <img src="{{ asset('storage/' . $d->product->image_path) }}" class="w-full h-full object-cover" />
                                        </button>
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-950 flex items-center justify-center text-slate-300">
                                            <i data-lucide="image" class="w-5 h-5"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-800 dark:text-slate-200 text-sm font-medium">
                                    {{ $d->product->name }}
                                </td>
                                <td class="px-5 py-3 text-slate-500 dark:text-slate-400 text-xs">
                                    @if($d->product->location)
                                        <span class="text-slate-700 dark:text-slate-300 font-medium px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 rounded">{{ $d->product->location }}</span>
                                    @endif
                                    <div class="font-mono mt-1">{{ $d->product->barcode }}</div>
                                </td>

                                @if($opname->status === 'draft')
                                    <td class="px-5 py-3 text-center bg-emerald-50 dark:bg-emerald-500/5">
                                        <div x-data="{ val: '{{ $d->stok_fisik ?? '' }}' }">
                                            <input type="number" x-model="val"
                                                x-on:input.debounce.800ms="$wire.setStokFisik({{ $d->product_id }}, val)"
                                                x-on:blur="$wire.setStokFisik({{ $d->product_id }}, val)"
                                                class="w-24 text-center bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 focus:border-emerald-500 rounded-xl shadow-sm text-sm text-slate-900 dark:text-white outline-none py-2 font-bold"
                                                min="0" placeholder="—">
                                        </div>
                                    </td>
                                @else
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 font-bold font-mono text-sm shadow-inner">{{ $d->stok_sistem }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-center bg-emerald-50 dark:bg-emerald-500/5">
                                        <span class="font-bold font-mono text-sm text-slate-800 dark:text-slate-200">{{ $d->stok_fisik ?? '—' }}</span>
                                    </td>
                                    @php $sel = ($d->stok_fisik !== null) ? ($d->stok_fisik - $d->stok_sistem) : null; @endphp
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2 py-1 rounded-lg font-bold font-mono text-sm
                                            {{ $sel === null ? 'text-slate-400' : ($sel > 0 ? 'bg-emerald-100 text-emerald-700' : ($sel < 0 ? 'bg-rose-100 text-rose-700' : 'text-slate-400')) }}">
                                            {{ $sel === null ? '—' : ($sel > 0 ? '+'.$sel : $sel) }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-10 text-center text-slate-400 text-sm">Tidak ada produk ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination detail --}}
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-slate-400">
                    Menampilkan {{ $details->firstItem() ?? 0 }}–{{ $details->lastItem() ?? 0 }} dari {{ $details->total() }} produk
                    @if($opname->status === 'selesai')
                        · <span class="text-emerald-600 font-medium">{{ $details->where('stok_fisik', '!==', null)->count() }} diinput</span>
                    @endif
                </p>
                @if($details->hasPages())
                    {{ $details->links() }}
                @endif
            </div>
        </div>

    @else
    {{-- ═══════════════════════════════ HALAMAN UTAMA / RIWAYAT ═══════════════════════════════ --}}
    <div x-data="{ showImg: false, imgUrl: '' }" class="space-y-4">
        {{-- Modal Image Popup - Moved outside for global overlay --}}
        <div x-show="showImg" 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md"
             style="display: none;" @keydown.escape.window="showImg = false">
            
            <button @click="showImg = false" class="absolute top-6 right-6 w-12 h-12 bg-white/10 hover:bg-rose-600 text-white rounded-full flex items-center justify-center transition-all group z-[1000]">
                <i data-lucide="x" class="w-8 h-8 group-hover:scale-110 transition-transform"></i>
            </button>

            <div class="relative max-w-4xl w-full flex items-center justify-center" @click.away="showImg = false">
                <img :src="imgUrl" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain border-4 border-white/10" />
            </div>
        </div>

        {{-- Tombol buat sesi baru --}}
        <div class="mb-6 flex items-center justify-end gap-3">
            <span class="text-sm text-slate-500 dark:text-slate-400">Tanggal sesi:</span>
            <div class="relative shrink-0">
                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                <input type="date" wire:model.live="tanggalBaru"
                    class="pl-9 pr-4 py-2 w-44 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white" style="color-scheme: dark;">
            </div>
            <button type="button" wire:click="buatOpname" wire:loading.attr="disabled"
                class="inline-flex justify-center items-center px-5 py-2 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white font-bold rounded-xl transition-colors shadow-md text-sm whitespace-nowrap shrink-0">
                <i data-lucide="folder-plus" class="w-4 h-4 mr-2" wire:loading.remove wire:target="buatOpname"></i>
                <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin hidden" wire:loading wire:target="buatOpname"></i>
                Buat Sesi Baru
            </button>
        </div>

        {{-- Riwayat sesi --}}
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col gap-3">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-base font-bold text-slate-800 dark:text-slate-200 shrink-0">Riwayat Sesi</h2>

                    {{-- Filter row: 2 baris agar tidak terpotong --}}
                    <div class="flex flex-col gap-2 min-w-0">
                        {{-- Baris 1: Search --}}
                        <div class="flex items-center gap-2 justify-end">
                            <div class="relative">
                                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5 pointer-events-none"></i>
                                <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()"
                                    wire:model.live.debounce.300ms="historySearch"
                                    placeholder="Cari sesi..."
                                    class="pl-9 pr-3 py-1.5 w-44 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-400">
                            </div>
                        </div>
                        {{-- Baris 2: Status + Urutan --}}
                        <div class="flex items-center gap-1.5 justify-end flex-wrap">
                            <button type="button" wire:click="$set('historyStatus', '')"
                                class="px-3 py-1.5 text-[11px] font-bold rounded-md border transition-all whitespace-nowrap
                                    {{ $historyStatus === '' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:border-emerald-400' }}">
                                Semua
                            </button>
                            <button type="button" wire:click="$set('historyStatus', 'draft')"
                                class="px-3 py-1.5 text-[11px] font-semibold rounded-md border transition-colors whitespace-nowrap
                                    {{ $historyStatus === 'draft' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:border-amber-400 hover:text-amber-600' }}">
                                Draft
                            </button>
                            <button type="button" wire:click="$set('historyStatus', 'selesai')"
                                class="px-3 py-1.5 text-[11px] font-semibold rounded-md border transition-colors whitespace-nowrap
                                    {{ $historyStatus === 'selesai' ? 'bg-teal-600 text-white border-teal-600' : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:border-teal-400 hover:text-teal-600' }}">
                                Selesai
                            </button>
                            <span class="w-px h-5 bg-slate-200 dark:bg-slate-700"></span>
                            <button type="button" wire:click="toggleHistoryDir"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:border-blue-400 hover:text-blue-600 transition-all whitespace-nowrap">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    @if($historySortDir === 'desc')
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h13M3 8h9m-9 4h6m4 0l4 4m0 0l-4 4m4-4H7"/>
                                    @endif
                                </svg>
                                {{ $historySortDir === 'desc' ? 'Terbaru' : 'Terlama' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[560px]">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-4">Tanggal</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4 text-right">Opsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($daftarOpname as $o)
                            <tr wire:key="opname-{{ $o->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-5 py-4 text-slate-800 dark:text-slate-200 text-sm font-medium">
                                    {{ $o->tanggal_opname->format('d M Y') }}
                                    <span class="block text-[10px] text-slate-400 font-normal mt-0.5">ID #{{ $o->id }}</span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        {{ $o->status == 'selesai' ? 'bg-emerald-500 text-white' : 'bg-orange-100 text-orange-600 dark:bg-orange-500/20 dark:text-orange-400' }}">
                                        {{ $o->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end items-center gap-2">
                                        {{-- Lihat detail (berlaku untuk draft & selesai) --}}
                                        <button type="button" wire:click="lihatSesi({{ $o->id }})"
                                            class="p-2 text-slate-500 hover:text-blue-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                                            title="{{ $o->status === 'selesai' ? 'Lihat Hasil Opname' : 'Lanjut Input' }}">
                                            <i data-lucide="{{ $o->status === 'selesai' ? 'eye' : 'edit' }}" class="w-4 h-4"></i>
                                        </button>

                                        {{-- Download laporan --}}
                                        <a href="{{ route('opname.export', $o->id) }}"
                                           class="p-2 text-slate-500 hover:text-emerald-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
                                           title="{{ $o->status === 'selesai' ? 'Unduh Laporan Hasil' : 'Backup Data' }}">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>

                                        @if($o->status === 'draft')
                                            <button type="button" wire:click="hapusSesi({{ $o->id }})"
                                                wire:confirm="Anda yakin ingin menghapus sesi opname ini? Data yang terhapus tidak dapat dikembalikan."
                                                class="p-2 text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        @else
                                            <button type="button" wire:click="batalRekonsiliasi({{ $o->id }})"
                                                wire:confirm="PERINGATAN: Membatalkan rekonsiliasi akan me-reverse semua penyesuaian stok secara otomatis. Yakin?"
                                                class="inline-flex items-center px-3 py-1.5 text-slate-500 hover:text-orange-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors text-xs font-semibold">
                                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 mr-1"></i> Batal
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-12 text-center text-slate-400 text-sm">Belum ada rekam jejak Audit Gudang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($daftarOpname->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                    {{ $daftarOpname->links() }}
                </div>
            @endif
        </div>

    @endif
</div>
