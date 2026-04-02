<div class="px-2 sm:px-0 space-y-6">

<x-slot:header>
    <h2 class="font-bold text-lg text-slate-800 dark:text-slate-200 leading-tight">Bin Card</h2>
</x-slot:header>

{{-- Back button + title --}}
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('produk.index') }}" wire:navigate
            class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Bin Card</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Riwayat & status stok per produk</p>
        </div>
    </div>

    {{-- Filter & Export --}}
    <div class="flex flex-wrap items-center gap-2">
        <div class="inline-flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 gap-0.5">
            @foreach (['this_week' => 'Minggu Ini', 'this_month' => 'Bulan Ini', 'last_3_months' => '3 Bulan', 'all' => 'Semua'] as $key => $label)
                <button type="button" wire:click="applyFilter('{{ $key }}')"
                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 {{ $activeFilter === $key ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-1.5 bg-white dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <input type="date" wire:model.live.debounce.500ms="startDate"
                class="bg-transparent border-none text-xs font-bold text-slate-700 dark:text-slate-300 focus:ring-0 outline-none p-1.5 [color-scheme:light] dark:[color-scheme:dark] w-32">
            <span class="text-slate-400 font-bold px-1 text-[10px]">TO</span>
            <input type="date" wire:model.live.debounce.500ms="endDate"
                class="bg-transparent border-none text-xs font-bold text-slate-700 dark:text-slate-300 focus:ring-0 outline-none p-1.5 [color-scheme:light] dark:[color-scheme:dark] w-32">
        </div>
        <button wire:click="exportExcel"
            class="flex items-center gap-2 px-4 py-2 text-xs font-bold bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md">
            <i data-lucide="download" class="w-4 h-4"></i> Export Excel
        </button>
    </div>
</div>

{{-- ─── Kartu Info Produk ─── --}}
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
    <div class="flex flex-col lg:flex-row gap-5">

        {{-- Left: Identitas Produk --}}
        <div class="flex items-start gap-4 flex-1">
            <div class="w-14 h-14 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="package-2" class="w-8 h-8 text-blue-500 dark:text-blue-400"></i>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-sm font-bold text-slate-500 dark:text-slate-400">
                        {{ $product->sku ?? $product->barcode ?? 'SKU-' . $product->id }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                        {{ $stockStatus === 'habis' ? 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' : ($stockStatus === 'kritis' ? 'bg-orange-100 text-orange-700 dark:bg-orange-500/20 dark:text-orange-400' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400') }}">
                        {{ strtoupper($stockStatus === 'habis' ? 'HABIS' : ($stockStatus === 'kritis' ? 'KRITIS' : 'AKTIF')) }}
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ $product->name }}</h2>
                @if($product->supplier)
                    <p class="text-xs text-slate-400 mt-0.5">Supplier: {{ $product->supplier->name }}</p>
                @endif
            </div>
        </div>

        {{-- Right: Stat Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4 flex-1">

            {{-- Lokasi Bin --}}
            <div class="bg-blue-50 dark:bg-blue-500/10 rounded-xl p-3 text-center border border-blue-100 dark:border-blue-500/20">
                <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">Lokasi Bin</p>
                <p class="text-lg font-extrabold text-blue-700 dark:text-blue-300 font-mono">
                    {{ $product->location ?? '—' }}
                </p>
            </div>

            {{-- UoM --}}
            <div class="bg-purple-50 dark:bg-purple-500/10 rounded-xl p-3 text-center border border-purple-100 dark:border-purple-500/20">
                <p class="text-[10px] font-bold text-purple-400 uppercase tracking-widest mb-1">Satuan (UoM)</p>
                <p class="text-lg font-extrabold text-purple-700 dark:text-purple-300">{{ $product->uom ?? '—' }}</p>
            </div>

            {{-- Stok On Hand --}}
            <a href="{{ route('produk.index') }}" wire:navigate
                class="block bg-emerald-50 dark:bg-emerald-500/10 rounded-xl p-3 text-center border border-emerald-100 dark:border-emerald-500/20 hover:shadow-md hover:-translate-y-0.5 transition-all cursor-pointer group">
                <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-1">Stok On Hand</p>
                <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-300">{{ number_format($product->current_stock, 0, ',', '.') }}</p>
                <p class="text-[10px] text-emerald-500 dark:text-emerald-400">{{ $product->uom ?? 'Unit' }}</p>
            </a>

            {{-- Min-Max --}}
            @if($product->min_stock || $product->max_stock)
            <div class="bg-amber-50 dark:bg-amber-500/10 rounded-xl p-3 text-center border border-amber-100 dark:border-amber-500/20">
                <p class="text-[10px] font-bold text-amber-500 uppercase tracking-widest mb-1">Min / Max</p>
                <p class="text-base font-extrabold text-amber-700 dark:text-amber-300">
                    {{ number_format($product->min_stock ?? 0) }} / {{ number_format($product->max_stock ?? 0) }}
                </p>
                <p class="text-[10px] text-amber-500">{{ $product->uom ?? 'Unit' }}</p>
            </div>
            @else
            <div class="bg-slate-50 dark:bg-slate-800 rounded-xl p-3 text-center border border-slate-200 dark:border-slate-700">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Min / Max</p>
                <p class="text-sm font-bold text-slate-400">Belum diset</p>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ─── Row bawah: Ringkasan + Riwayat Transaksi ─── --}}
<div class="flex flex-col lg:flex-row gap-5">

    {{-- Kolom kiri: Ringkasan bin card + Status peringatan --}}
    <div class="lg:w-72 flex flex-col gap-4">

        {{-- Ringkasan Bin Card --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-4 h-4 text-blue-500"></i>
                Ringkasan Periode
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-xs text-slate-500">Total Masuk</span>
                    <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">+{{ number_format($totalMasuk, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-xs text-slate-500">Total Keluar</span>
                    <span class="text-sm font-extrabold text-rose-600 dark:text-rose-400">-{{ number_format($totalKeluar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-xs text-slate-500">Jml Transaksi</span>
                    <span class="text-sm font-extrabold text-slate-800 dark:text-white">{{ $transactions->count() }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-xs text-slate-500">Transaksi Terakhir</span>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                        {{ $lastActivity ? $lastActivity->created_at->diffForHumans() : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Peringatan & Status --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
                Status Stok
            </h3>
            <div class="space-y-2">
                @if($stockStatus === 'aman')
                <div class="flex items-center gap-3 p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0"></i>
                    <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">Stok Aman</p>
                </div>
                @if($product->min_stock)
                <p class="text-[10px] text-slate-400 px-1">Stok di atas minimum ({{ $product->min_stock }} {{ $product->uom }})</p>
                @endif
                @elseif($stockStatus === 'kritis')
                <a href="{{ route('produk.index') }}?filter=kritis" wire:navigate
                    class="flex items-center gap-3 p-3 bg-orange-50 dark:bg-orange-500/10 rounded-xl border border-orange-100 dark:border-orange-500/20 hover:shadow-md transition-all">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-600 dark:text-orange-400 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-orange-700 dark:text-orange-300">⚠ Stok Kritis</p>
                        <p class="text-[10px] text-orange-500 dark:text-orange-400">Di bawah minimum {{ $product->min_stock }} {{ $product->uom }}</p>
                    </div>
                </a>
                @else
                <a href="{{ route('barang-masuk.index') }}" wire:navigate
                    class="flex items-center gap-3 p-3 bg-red-50 dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 hover:shadow-md transition-all">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-600 dark:text-red-400 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-red-700 dark:text-red-300">🔴 Stok Habis</p>
                        <p class="text-[10px] text-red-500 dark:text-red-400">Klik untuk tambah barang masuk</p>
                    </div>
                </a>
                @endif

                <a href="{{ route('barang-masuk.index') }}" wire:navigate
                    class="flex items-center justify-center gap-2 w-full mt-2 px-3 py-2 text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Stok
                </a>
            </div>
        </div>

    </div>

    {{-- Kolom kanan: Tabel riwayat transaksi --}}
    <div class="flex-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4 text-blue-500"></i>
                Riwayat Transaksi
                <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-full">{{ $transactions->count() }} data</span>
            </h3>
            <a href="{{ route('laporan.index') }}" wire:navigate
                class="text-[11px] font-semibold px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors">
                Semua Laporan →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">No</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Tanggal & Waktu</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Jenis</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Referensi</th>
                        <th class="px-4 py-3 text-right font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Masuk</th>
                        <th class="px-4 py-3 text-right font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">Keluar</th>
                        <th class="px-4 py-3 text-right font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">Saldo</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">PIC</th>
                        <th class="px-4 py-3 text-left font-bold text-slate-500 dark:text-slate-400">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($transactions as $i => $trx)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-3 text-slate-400 font-mono">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap font-mono text-[11px]">
                            {{ $trx->created_at->format('d/m/Y') }}<br>
                            <span class="text-slate-400">{{ $trx->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($trx->type === 'IN')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                    <i data-lucide="arrow-down-left" class="w-2.5 h-2.5"></i> Masuk
                                </span>
                            @elseif($trx->type === 'OUT')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30">
                                    <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i> Keluar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30">
                                    <i data-lucide="refresh-cw" class="w-2.5 h-2.5"></i> Adjust
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300 font-mono text-[11px] whitespace-nowrap">{{ $trx->reference ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold">
                            @if($trx->quantity > 0)
                                <span class="text-emerald-600 dark:text-emerald-400">+{{ number_format($trx->quantity, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-700">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold">
                            @if($trx->quantity < 0)
                                <span class="text-rose-600 dark:text-rose-400">{{ number_format(abs($trx->quantity), 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-700">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-extrabold">
                            <span class="{{ $trx->balance <= 0 ? 'text-red-600 dark:text-red-400' : ($trx->balance <= ($product->min_stock ?? 0) ? 'text-orange-600 dark:text-orange-400' : 'text-blue-600 dark:text-blue-400') }}">
                                {{ number_format($trx->balance, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center text-[8px] font-bold flex-shrink-0">
                                    {{ strtoupper(substr($trx->pic, 0, 2)) }}
                                </div>
                                <span>{{ $trx->pic }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400 max-w-[200px] truncate">{{ $trx->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-10 h-10 text-slate-300 dark:text-slate-600"></i>
                                <p class="text-sm font-semibold text-slate-400">Tidak ada transaksi di periode ini</p>
                                <p class="text-xs text-slate-400">Ubah filter tanggal untuk melihat data lainnya</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => { lucide.createIcons(); });
</script>
@endpush
