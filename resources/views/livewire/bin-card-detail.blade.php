<div class="px-2 sm:px-0 space-y-5 max-w-screen-2xl mx-auto">

<x-slot:header>
    <h2 class="font-bold text-lg text-slate-800 dark:text-slate-200 leading-tight">Bin Card</h2>
</x-slot:header>

{{-- ─── Page header ─── --}}
<div class="flex flex-col gap-4">

    {{-- Row 1: Back + judul + Export --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('produk.index') }}" wire:navigate
                class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-[#D1D5DB] dark:border-slate-800 hover:bg-[#F0FDF4] dark:hover:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-slate-200 transition-all shadow-sm flex-shrink-0"
                style="box-shadow: 0 1px 2px rgba(0,0,0,0.04)">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="min-w-0">
                <h1 class="text-xl font-extrabold text-[#064E3B] dark:text-white tracking-tight truncate">
                    Bin Card — <span class="text-[#10B981]">{{ $product->name }}</span>
                </h1>
                <p class="text-xs text-[#94A3B8] dark:text-slate-400">Riwayat &amp; status stok per produk</p>
            </div>
        </div>

        <button wire:click="exportExcel" wire:loading.attr="disabled"
            class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold bg-[#10B981] hover:bg-[#059669] text-white rounded-xl transition-all hover:-translate-y-0.5 disabled:opacity-60"
            style="box-shadow: 0 1px 2px rgba(0,0,0,0.06), 0 4px 12px rgba(22,163,74,0.2)">
            <i data-lucide="download" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Export Excel</span>
            <span class="sm:hidden">Excel</span>
        </button>
    </div>

    {{-- Row 2: Filter periode (full width, compact) --}}
    <div class="flex flex-wrap items-center gap-2">
        <div class="inline-flex items-center bg-[#F0FDF4] dark:bg-slate-800 p-1 rounded-xl border border-[#D1D5DB] dark:border-slate-700 gap-0.5">
            @foreach (['this_week' => 'Minggu', 'this_month' => 'Bulan Ini', 'last_3_months' => '3 Bulan', 'all' => 'Semua'] as $key => $label)
                <button type="button" wire:click="applyFilter('{{ $key }}')"
                    class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200
                        {{ $activeFilter === $key
                            ? 'bg-white dark:bg-slate-900 text-[#10B981] dark:text-emerald-400 shadow-sm border border-[#D1D5DB] dark:border-slate-700'
                            : 'text-[#334155] dark:text-slate-400 hover:text-[#064E3B] dark:hover:text-slate-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex items-center gap-1.5 bg-white dark:bg-slate-800 px-2 py-1 rounded-xl border border-[#D1D5DB] dark:border-slate-700"
            style="box-shadow: 0 1px 2px rgba(0,0,0,0.04)">
            <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#94A3B8]"></i>
            <input type="date" wire:model.live.debounce.500ms="startDate"
                class="bg-transparent border-none text-xs font-medium text-[#334155] dark:text-slate-300 focus:ring-0 outline-none p-0.5 [color-scheme:light] dark:[color-scheme:dark] w-28">
            <span class="text-[#94A3B8] text-[10px] font-bold">—</span>
            <input type="date" wire:model.live.debounce.500ms="endDate"
                class="bg-transparent border-none text-xs font-medium text-[#334155] dark:text-slate-300 focus:ring-0 outline-none p-0.5 [color-scheme:light] dark:[color-scheme:dark] w-28">
        </div>
    </div>
</div>

{{-- ─── Kartu Info Produk ─── --}}
<div class="bg-white dark:bg-slate-900 border border-[#D1D5DB] dark:border-slate-800 rounded-2xl p-4"
    style="box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.06)">
    <div class="flex flex-col lg:flex-row gap-4">

        {{-- Left: Identitas Produk --}}
        <div class="flex items-start gap-4 flex-1 min-w-0">
            <div class="w-14 h-14 rounded-2xl bg-[#D1FAE5] dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                <i data-lucide="package-2" class="w-8 h-8 text-[#10B981] dark:text-emerald-400"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="font-mono text-sm font-bold text-[#94A3B8] dark:text-slate-400">
                        {{ $product->sku ?? $product->barcode ?? 'ID-' . $product->id }}
                    </span>
                    @if($stockStatus === 'habis')
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#FEE2E2] text-[#DC2626] dark:bg-red-500/20 dark:text-red-400">🔴 HABIS</span>
                    @elseif($stockStatus === 'kritis')
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#D97706] dark:bg-amber-500/20 dark:text-amber-400">⚠ KRITIS</span>
                    @else
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#DCFCE7] text-[#16A34A] dark:bg-emerald-500/20 dark:text-emerald-400">✓ AKTIF</span>
                    @endif
                </div>
                <h2 class="text-xl font-extrabold text-[#064E3B] dark:text-white leading-tight truncate">{{ $product->name }}</h2>
                @if($product->supplier)
                    <p class="text-xs text-[#94A3B8] mt-0.5">Supplier: {{ $product->supplier->name }}</p>
                @endif
            </div>
        </div>

        {{-- Right: Stat Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:max-w-sm w-full">

            {{-- Lokasi Bin --}}
            <div class="bg-[#D1FAE5] dark:bg-emerald-500/10 rounded-xl p-3 text-center border border-emerald-100 dark:border-emerald-500/20">
                <p class="text-[9px] font-bold text-[#10B981] uppercase tracking-widest mb-1">Lokasi Bin</p>
                <p class="text-base font-extrabold text-[#064E3B] dark:text-emerald-300 font-mono leading-tight">{{ $product->location ?? '—' }}</p>
            </div>

            {{-- UoM --}}
            <div class="bg-[#F0FDF4] dark:bg-slate-800 rounded-xl p-3 text-center border border-[#D1D5DB] dark:border-slate-700">
                <p class="text-[9px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Satuan</p>
                <p class="text-base font-extrabold text-[#334155] dark:text-slate-200">{{ $product->uom ?? '—' }}</p>
            </div>

            {{-- Stok On Hand --}}
            <a href="{{ route('produk.index') }}" wire:navigate
                class="block bg-[#DCFCE7] dark:bg-emerald-500/10 rounded-xl p-3 text-center border border-green-100 dark:border-emerald-500/20 hover:shadow-md hover:-translate-y-0.5 transition-all">
                <p class="text-[9px] font-bold text-[#16A34A] uppercase tracking-widest mb-1">Stok On Hand</p>
                <p class="text-2xl font-extrabold text-[#16A34A] dark:text-emerald-300">{{ number_format($product->current_stock, 0, ',', '.') }}</p>
                <p class="text-[9px] text-[#16A34A]/70">{{ $product->uom ?? 'Unit' }}</p>
            </a>

            {{-- Min-Max --}}
            @if($product->min_stock || $product->max_stock)
            <div class="bg-[#FEF3C7] dark:bg-amber-500/10 rounded-xl p-3 text-center border border-amber-100 dark:border-amber-500/20">
                <p class="text-[9px] font-bold text-[#D97706] uppercase tracking-widest mb-1">Min / Max</p>
                <p class="text-base font-extrabold text-[#D97706] dark:text-amber-300">{{ number_format($product->min_stock ?? 0) }} / {{ number_format($product->max_stock ?? 0) }}</p>
                <p class="text-[9px] text-[#D97706]/70">{{ $product->uom ?? 'Unit' }}</p>
            </div>
            @else
            <div class="bg-[#F0FDF4] dark:bg-slate-800 rounded-xl p-3 text-center border border-[#D1D5DB] dark:border-slate-700">
                <p class="text-[9px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Min / Max</p>
                <p class="text-xs font-semibold text-[#94A3B8]">Belum diset</p>
            </div>
            @endif

        </div>
    </div>
</div>

{{-- ─── Row bawah: Ringkasan + Riwayat Transaksi ─── --}}
<div class="flex flex-col lg:flex-row gap-4">

    {{-- Kolom kiri: Ringkasan + Status --}}
    <div class="lg:w-64 flex flex-col gap-4 flex-shrink-0">

        {{-- Ringkasan Periode --}}
        <div class="bg-white dark:bg-slate-900 border border-[#D1D5DB] dark:border-slate-800 rounded-2xl p-4"
            style="box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.06)">
            <h3 class="text-xs font-bold text-[#334155] dark:text-slate-200 mb-3 flex items-center gap-1.5">
                <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-[#10B981]"></i>
                Ringkasan Periode
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between items-center py-1.5 border-b border-[#D1D5DB] dark:border-slate-800">
                    <span class="text-[11px] text-[#94A3B8]">Total Masuk</span>
                    <span class="text-sm font-extrabold text-[#16A34A] dark:text-emerald-400">+{{ number_format($totalMasuk, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-[#D1D5DB] dark:border-slate-800">
                    <span class="text-[11px] text-[#94A3B8]">Total Keluar</span>
                    <span class="text-sm font-extrabold text-[#DC2626] dark:text-rose-400">-{{ number_format($totalKeluar, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-[#D1D5DB] dark:border-slate-800">
                    <span class="text-[11px] text-[#94A3B8]">Jml Transaksi</span>
                    <span class="text-sm font-extrabold text-[#064E3B] dark:text-white">{{ $transactions->count() }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-[11px] text-[#94A3B8]">Trx Terakhir</span>
                    <span class="text-[10px] font-bold text-[#334155] dark:text-slate-300">
                        {{ $lastActivity ? $lastActivity->created_at->diffForHumans() : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Status Stok --}}
        <div class="bg-white dark:bg-slate-900 border border-[#D1D5DB] dark:border-slate-800 rounded-2xl p-4"
            style="box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.06)">
            <h3 class="text-xs font-bold text-[#334155] dark:text-slate-200 mb-3 flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-[#16A34A]"></i>
                Status Stok
            </h3>
            <div class="space-y-2">
                @if($stockStatus === 'aman')
                <div class="flex items-center gap-2 p-3 bg-[#DCFCE7] dark:bg-emerald-500/10 rounded-xl border border-green-100 dark:border-emerald-500/20">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#16A34A] dark:text-emerald-400 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-[#16A34A] dark:text-emerald-300">Stok Aman</p>
                        @if($product->min_stock)
                        <p class="text-[9px] text-[#16A34A]/70">Di atas min {{ $product->min_stock }} {{ $product->uom }}</p>
                        @endif
                    </div>
                </div>
                @elseif($stockStatus === 'kritis')
                <a href="{{ route('produk.index') }}?filter=kritis" wire:navigate
                    class="flex items-center gap-2 p-3 bg-[#FEF3C7] dark:bg-amber-500/10 rounded-xl border border-amber-100 dark:border-amber-500/20 hover:shadow-md transition-all">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-[#D97706] dark:text-amber-400 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-[#D97706] dark:text-amber-300">⚠ Stok Kritis</p>
                        <p class="text-[9px] text-[#D97706]/70">Bawah min {{ $product->min_stock }} {{ $product->uom }}</p>
                    </div>
                </a>
                @else
                <a href="{{ route('barang-masuk.index') }}" wire:navigate
                    class="flex items-center gap-2 p-3 bg-[#FEE2E2] dark:bg-red-500/10 rounded-xl border border-red-100 dark:border-red-500/20 hover:shadow-md transition-all">
                    <i data-lucide="x-circle" class="w-4 h-4 text-[#DC2626] dark:text-red-400 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-[#DC2626] dark:text-red-300">🔴 Stok Habis</p>
                        <p class="text-[9px] text-[#DC2626]/70">Klik untuk tambah stok</p>
                    </div>
                </a>
                @endif

                <a href="{{ route('barang-masuk.index') }}" wire:navigate
                    class="flex items-center justify-center gap-2 w-full mt-1 px-3 py-2 text-[11px] font-bold text-[#10B981] dark:text-emerald-400 bg-[#D1FAE5] dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 rounded-xl hover:bg-emerald-200/60 dark:hover:bg-emerald-500/20 transition-colors">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Stok
                </a>
            </div>
        </div>

    </div>

    {{-- Kolom kanan: Tabel riwayat transaksi --}}
    <div class="flex-1 bg-white dark:bg-slate-900 border border-[#D1D5DB] dark:border-slate-800 rounded-2xl overflow-hidden min-w-0"
        style="box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.06)">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#D1D5DB] dark:border-slate-800">
            <h3 class="text-sm font-bold text-[#334155] dark:text-slate-200 flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4 text-[#10B981]"></i>
                Riwayat Transaksi
                <span class="text-[10px] font-bold px-2 py-0.5 bg-[#D1FAE5] dark:bg-emerald-500/10 text-[#10B981] dark:text-emerald-400 rounded-full">{{ $transactions->count() }}</span>
            </h3>
            <a href="{{ route('laporan.index') }}" wire:navigate
                class="text-[11px] font-semibold px-3 py-1.5 rounded-lg bg-[#F0FDF4] hover:bg-[#E2E8F0] dark:bg-slate-800 dark:hover:bg-slate-700 text-[#334155] dark:text-slate-300 transition-colors">
                Semua →
            </a>
        </div>

        <!-- Mobile Transaction Cards -->
        <div class="md:hidden flex flex-col divide-y divide-[#E2E8F0] dark:divide-slate-800">
            @forelse($transactions as $i => $trx)
                <div class="px-4 py-3 {{ $trx->type === 'IN' ? 'border-l-4 border-l-emerald-400' : ($trx->type === 'OUT' ? 'border-l-4 border-l-rose-400' : 'border-l-4 border-l-amber-400') }}">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            @if($trx->type === 'IN')
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#DCFCE7] text-[#16A34A]">Masuk</span>
                            @elseif($trx->type === 'OUT')
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#FEE2E2] text-[#DC2626]">Keluar</span>
                            @else
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#FEF3C7] text-[#D97706]">Adjust</span>
                            @endif
                            <span class="text-xs font-mono text-[#334155] dark:text-slate-300">{{ $trx->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            @if($trx->quantity > 0)
                                <span class="font-bold text-[#16A34A]">+{{ $trx->quantity }}</span>
                            @else
                                <span class="font-bold text-[#DC2626]">{{ $trx->quantity }}</span>
                            @endif
                            <span class="text-[#94A3B8]">→</span>
                            <span class="font-extrabold {{ $trx->balance <= 0 ? 'text-[#DC2626]' : ($trx->balance <= ($product->min_stock ?? 0) ? 'text-[#D97706]' : 'text-[#10B981]') }}">{{ $trx->balance }}</span>
                        </div>
                    </div>
                    @if($trx->reference || $trx->notes)
                        <p class="text-[10px] text-[#94A3B8] mt-1 truncate">{{ $trx->reference ?? '' }}{{ $trx->reference && $trx->notes ? ' · ' : '' }}{{ $trx->notes ?? '' }}</p>
                    @endif
                </div>
            @empty
                <div class="py-12 flex flex-col items-center gap-2">
                    <i data-lucide="inbox" class="w-8 h-8 text-[#E2E8F0] dark:text-slate-600"></i>
                    <p class="text-sm text-[#94A3B8]">Tidak ada transaksi di periode ini</p>
                </div>
            @endforelse
        </div>

        <!-- Desktop Transaction Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-[#F6F8FB] dark:bg-slate-800/50 border-b border-[#D1D5DB] dark:border-slate-800">
                        <th class="px-4 py-3 text-left font-semibold text-[#94A3B8] whitespace-nowrap">#</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#94A3B8] whitespace-nowrap">Tanggal & Waktu</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#94A3B8] whitespace-nowrap">Jenis</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#94A3B8] whitespace-nowrap">Referensi</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#16A34A] whitespace-nowrap">Masuk</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#DC2626] whitespace-nowrap">Keluar</th>
                        <th class="px-4 py-3 text-right font-semibold text-[#10B981] whitespace-nowrap">Saldo</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#94A3B8] whitespace-nowrap">PIC</th>
                        <th class="px-4 py-3 text-left font-semibold text-[#94A3B8]">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E2E8F0] dark:divide-slate-800">
                    @forelse($transactions as $i => $trx)
                    <tr class="hover:bg-[#F6F8FB] dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-3 text-[#94A3B8] font-mono">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <p class="font-mono text-[11px] text-[#334155] dark:text-slate-300">{{ $trx->created_at->format('d/m/Y') }}</p>
                            <p class="font-mono text-[10px] text-[#94A3B8]">{{ $trx->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($trx->type === 'IN')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#DCFCE7] text-[#16A34A] border border-green-100 dark:bg-emerald-500/15 dark:text-emerald-400">
                                    <i data-lucide="arrow-down-left" class="w-2.5 h-2.5"></i> Masuk
                                </span>
                            @elseif($trx->type === 'OUT')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#FEE2E2] text-[#DC2626] border border-red-100 dark:bg-rose-500/15 dark:text-rose-400">
                                    <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i> Keluar
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-[#FEF3C7] text-[#D97706] border border-amber-100 dark:bg-amber-500/15 dark:text-amber-400">
                                    <i data-lucide="refresh-cw" class="w-2.5 h-2.5"></i> Adjust
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-[#334155] dark:text-slate-300 whitespace-nowrap">{{ $trx->reference ?? '—' }}</td>
                        <td class="px-4 py-3 text-right font-bold">
                            @if($trx->quantity > 0)
                                <span class="text-[#16A34A] dark:text-emerald-400">+{{ number_format($trx->quantity, 0, ',', '.') }}</span>
                            @else
                                <span class="text-[#E2E8F0] dark:text-slate-700">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold">
                            @if($trx->quantity < 0)
                                <span class="text-[#DC2626] dark:text-rose-400">{{ number_format(abs($trx->quantity), 0, ',', '.') }}</span>
                            @else
                                <span class="text-[#E2E8F0] dark:text-slate-700">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-extrabold">
                            <span class="{{ $trx->balance <= 0 ? 'text-[#DC2626] dark:text-red-400' : ($trx->balance <= ($product->min_stock ?? 0) ? 'text-[#D97706] dark:text-amber-400' : 'text-[#10B981] dark:text-emerald-400') }}">
                                {{ number_format($trx->balance, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-[#D1FAE5] dark:bg-emerald-500/20 text-[#10B981] dark:text-emerald-400 flex items-center justify-center text-[8px] font-extrabold flex-shrink-0">
                                    {{ strtoupper(substr($trx->pic, 0, 2)) }}
                                </div>
                                <span class="text-[11px] text-[#334155] dark:text-slate-300">{{ $trx->pic }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-[11px] text-[#94A3B8] dark:text-slate-400 max-w-[200px] truncate">{{ $trx->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="inbox" class="w-10 h-10 text-[#E2E8F0] dark:text-slate-600"></i>
                                <p class="text-sm font-semibold text-[#94A3B8]">Tidak ada transaksi di periode ini</p>
                                <p class="text-xs text-[#94A3B8]">Ubah filter tanggal untuk melihat data lainnya</p>
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
