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

        {{-- Filter & search dalam sesi --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1 max-w-md">
                <i data-lucide="search" wire:loading.remove wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                <i data-lucide="loader-2" wire:loading wire:target="cariBarang" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-4 h-4 animate-spin"></i>
                <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()"
                    wire:model.live.debounce.300ms="cariBarang"
                    placeholder="Cari nama, barcode, atau rak..."
                    class="pl-9 pr-4 py-2.5 w-full bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white dark:placeholder-slate-500 shadow-sm">
            </div>
            <div class="relative w-full sm:w-52 shrink-0">
                <i data-lucide="arrow-up-down" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4"></i>
                <select wire:model.live="detailSort"
                    class="pl-9 pr-8 py-2.5 w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-800 dark:text-slate-200 appearance-none">
                    <option value="name_asc">Nama (A–Z)</option>
                    <option value="name_desc">Nama (Z–A)</option>
                    <option value="barcode">Barcode</option>
                    <option value="location">Lokasi / Rak</option>
                    <option value="selisih_besar">Selisih Terbesar</option>
                </select>
                <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
            </div>
        </div>

        {{-- Tabel detail sesi --}}
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-4">Nama Produk</th>
                            <th class="px-5 py-4">Rak / Barcode</th>

                            @if($opname->status === 'draft')
                                <th class="px-5 py-4 text-center bg-emerald-500 text-white">Input Stok Fisik (Aktual)</th>
                            @else
                                <th class="px-5 py-4 text-center">Stok Sistem</th>
                                <th class="px-5 py-4 text-center bg-emerald-500 text-white">Stok Fisik</th>
                                <th class="px-5 py-4 text-center">Selisih</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($details as $d)
                            <tr wire:key="detail-{{ $d->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-5 py-3 text-slate-800 dark:text-slate-200 text-sm font-medium">{{ $d->product->name }}</td>
                                <td class="px-5 py-3 text-slate-500 dark:text-slate-400 text-xs">
                                    @if($d->product->location)
                                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $d->product->location }}</span>
                                        <span class="text-slate-400 mx-1">·</span>
                                    @endif
                                    <span class="font-mono">{{ $d->product->barcode }}</span>
                                </td>

                                @if($opname->status === 'draft')
                                    <td class="px-5 py-3 text-center bg-emerald-50 dark:bg-emerald-500/5">
                                        <div x-data="{ val: '{{ $d->stok_fisik ?? '' }}' }">
                                            <input type="number" x-model="val"
                                                x-on:input.debounce.800ms="$wire.setStokFisik({{ $d->product_id }}, val)"
                                                x-on:blur="$wire.setStokFisik({{ $d->product_id }}, val)"
                                                class="w-24 text-center bg-white dark:bg-slate-950 border-2 border-transparent focus:border-emerald-400 rounded-lg shadow-sm text-sm text-slate-900 dark:text-white outline-none"
                                                min="0" placeholder="—">
                                        </div>
                                    </td>
                                @else
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-2 py-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 font-bold font-mono text-sm">{{ $d->stok_sistem }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-center bg-emerald-50 dark:bg-emerald-500/5">
                                        <span class="font-bold font-mono text-sm text-slate-700 dark:text-slate-300">{{ $d->stok_fisik ?? '—' }}</span>
                                    </td>
                                    @php $sel = ($d->stok_fisik !== null) ? ($d->stok_fisik - $d->stok_sistem) : null; @endphp
                                    <td class="px-5 py-3 text-center">
                                        <span class="font-bold font-mono text-sm
                                            {{ $sel === null ? 'text-slate-400' : ($sel > 0 ? 'text-emerald-600' : ($sel < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400')) }}">
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

        {{-- Tombol buat sesi baru --}}
        <div class="mb-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:justify-end">
            <div class="relative w-full sm:w-44 shrink-0">
                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-4 h-4 pointer-events-none"></i>
                <input type="date" wire:model.live="tanggalBaru"
                    class="pl-9 pr-4 py-2.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none text-slate-900 dark:text-white" style="color-scheme: dark;">
            </div>
            <button type="button" wire:click="buatOpname" wire:loading.attr="disabled"
                class="inline-flex justify-center items-center px-4 py-2.5 bg-emerald-500 hover:bg-emerald-700 disabled:opacity-50 text-white font-bold rounded-xl transition-colors shadow-lg text-sm whitespace-nowrap shrink-0">
                <i data-lucide="folder-plus" class="w-4 h-4 mr-2" wire:loading.remove wire:target="buatOpname"></i>
                <i data-lucide="loader-2" class="w-4 h-4 mr-2 animate-spin hidden" wire:loading wire:target="buatOpname"></i>
                Buat Sesi Opname Baru
            </button>
        </div>

        {{-- Riwayat sesi --}}
        <div class="bg-white dark:bg-slate-900 card-shadow border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col gap-3">
                <h2 class="text-base font-bold text-slate-800 dark:text-slate-200">Riwayat Sesi Audit / Opname</h2>

                {{-- Filter riwayat --}}
                <div class="flex flex-col sm:flex-row gap-2 flex-wrap">
                    {{-- Search --}}
                    <div class="relative flex-1 min-w-[180px]">
                        <i data-lucide="search" wire:loading.remove wire:target="historySearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5"></i>
                        <i data-lucide="loader-2" wire:loading wire:target="historySearch" class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-500 w-3.5 h-3.5 animate-spin"></i>
                        <input type="text" enterkeyhint="search" x-data x-on:keydown.enter="$el.blur()"
                            wire:model.live.debounce.300ms="historySearch"
                            placeholder="Cari ID atau nama validator..."
                            class="pl-8 pr-4 py-2 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white dark:placeholder-slate-500">
                    </div>
                    {{-- Filter tanggal --}}
                    <div class="relative w-full sm:w-36 shrink-0">
                        <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5"></i>
                        <input type="date" wire:model.live="historyDate"
                            class="pl-8 pr-3 py-2 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white" style="color-scheme: dark;">
                    </div>
                    {{-- Filter Status --}}
                    <div class="relative w-full sm:w-36 shrink-0">
                        <i data-lucide="tag" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5"></i>
                        <select wire:model.live="historyStatus"
                            class="pl-8 pr-7 py-2 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 appearance-none">
                            <option value="">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="selesai">Selesai</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5 pointer-events-none"></i>
                    </div>
                    {{-- Sort urutan --}}
                    <div class="relative w-full sm:w-36 shrink-0">
                        <i data-lucide="arrow-up-down" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5"></i>
                        <select wire:model.live="historySort"
                            class="pl-8 pr-7 py-2 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 dark:text-slate-200 appearance-none">
                            <option value="terbaru">Terbaru</option>
                            <option value="terlama">Terlama</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 w-3.5 h-3.5 pointer-events-none"></i>
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
