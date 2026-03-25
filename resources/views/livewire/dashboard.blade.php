<div class="px-2 sm:px-0 space-y-6">

{{-- Skeleton shimmer CSS — inside single root div (Livewire 3 needs ONE root element as first node) --}}
<style>
@keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
@keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.skeleton{background:linear-gradient(90deg,rgba(148,163,184,.08) 25%,rgba(148,163,184,.15) 50%,rgba(148,163,184,.08) 75%);background-size:800px 100%;animation:shimmer 2s infinite linear;border-radius:8px}
.dark .skeleton{background:linear-gradient(90deg,rgba(71,85,105,.15) 25%,rgba(71,85,105,.3) 50%,rgba(71,85,105,.15) 75%);background-size:800px 100%}
.loading-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
.animate-in { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both; }
.animation-delay-100 { animation-delay: 0.1s; }
.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-300 { animation-delay: 0.3s; }
.animate-pulse-slow { animation: pulse-soft 3s infinite; }
</style>

<x-slot:header>
    <h2 class="font-bold text-lg text-slate-800 dark:text-slate-200 leading-tight">Dashboard</h2>
</x-slot:header>

{{-- ─── Header + Filter ────────────────────────────────── --}}
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Overview Gudang</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Status, metrik, dan statistik pergerakan barang.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
        <div wire:key="filter-pill" class="inline-flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 gap-0.5">
            <button wire:key="btn-today" type="button" wire:click="applyFilter('today')"
                class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 {{ $activeFilter === 'today' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                Hari Ini
            </button>
            <button wire:key="btn-7days" type="button" wire:click="applyFilter('last_7_days')"
                class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 {{ $activeFilter === 'last_7_days' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                7 Hari
            </button>
            <button wire:key="btn-month" type="button" wire:click="applyFilter('this_month')"
                class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200 {{ $activeFilter === 'this_month' ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-700' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                Bulan Ini
            </button>
        </div>
        <div wire:key="date-inputs" class="flex items-center gap-1.5 bg-white dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
            <input type="date" wire:key="input-start" wire:model.live.debounce.500ms="startDate"
                class="bg-transparent border-none text-xs font-bold text-slate-700 dark:text-slate-300 focus:ring-0 outline-none p-1.5 [color-scheme:light] dark:[color-scheme:dark] w-32">
            <span class="text-slate-400 font-bold px-1 text-[10px]">TO</span>
            <input type="date" wire:key="input-end" wire:model.live.debounce.500ms="endDate"
                class="bg-transparent border-none text-xs font-bold text-slate-700 dark:text-slate-300 focus:ring-0 outline-none p-1.5 [color-scheme:light] dark:[color-scheme:dark] w-32">
        </div>
    </div>
</div>

{{-- ─── Stats Cards ────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-4">

    {{-- Katalog --}}
    <a href="{{ route('produk.index') }}" wire:navigate
       wire:loading.class="opacity-40 scale-[0.98] grayscale-[0.5]" wire:target="startDate,endDate,applyFilter"
       class="animate-in group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-3 hover:border-blue-400 dark:hover:border-blue-600 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 loading-transition cursor-pointer">
        <div class="shrink-0 w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500 dark:text-blue-400 group-hover:scale-110 transition-transform">
            <i data-lucide="package-2" class="w-5 h-5" stroke-width="2"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Katalog</p>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ number_format($stats['total_jenis'], 0, ',', '.') }} <span class="text-[11px] font-normal text-slate-400">Jenis</span></p>
            <p class="text-sm font-bold text-blue-500 dark:text-blue-400 mt-0.5">{{ number_format($stats['total_inventory'], 0, ',', '.') }} <span class="text-[10px] font-normal text-blue-400/70">Fisik</span></p>
        </div>
        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-700 group-hover:text-blue-400 transition-colors ml-auto shrink-0"></i>
    </a>

    {{-- Stok Kritis --}}
    <a href="{{ route('produk.index') }}?filter=kritis" wire:navigate
       wire:loading.class="opacity-40 scale-[0.98] grayscale-[0.5]" wire:target="startDate,endDate,applyFilter"
       class="animate-in animation-delay-100 group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-3 hover:border-orange-400 dark:hover:border-orange-600 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 loading-transition cursor-pointer">
        <div class="shrink-0 w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-500 dark:text-orange-400 group-hover:scale-110 transition-transform">
            <i data-lucide="alert-triangle" class="w-5 h-5" stroke-width="2"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Stok Kritis</p>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ number_format($stats['low_stock'], 0, ',', '.') }} <span class="text-[11px] font-normal text-slate-400">Barang</span></p>
        </div>
        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-700 group-hover:text-orange-400 transition-colors ml-auto shrink-0"></i>
    </a>

    {{-- Masuk --}}
    <a href="{{ route('laporan.index') }}?tipe=IN" wire:navigate
       wire:loading.class="opacity-40 scale-[0.98] grayscale-[0.5]" wire:target="startDate,endDate,applyFilter"
       class="animate-in animation-delay-200 group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-3 hover:border-emerald-400 dark:hover:border-emerald-600 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 loading-transition cursor-pointer">
        <div class="shrink-0 w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
            <i data-lucide="arrow-down-left" class="w-5 h-5" stroke-width="2.5"></i>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1.5 mb-1">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Masuk</p>
                @if($stats['trend_masuk'] !== null)
                    @php $pct = $stats['trend_masuk']; @endphp
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $pct >= 0 ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10' : 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10' }}">{{ $pct >= 0 ? '↑' : '↓' }} {{ abs($pct) }}%</span>
                @else
                    <span class="text-[9px] bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-1.5 py-0.5 rounded-md font-semibold">Rentang</span>
                @endif
            </div>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ number_format($stats['masuk_range'], 0, ',', '.') }} <span class="text-[11px] font-normal text-slate-400">Unit</span></p>
        </div>
        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-700 group-hover:text-emerald-400 transition-colors ml-auto shrink-0"></i>
    </a>

    {{-- Keluar --}}
    <a href="{{ route('laporan.index') }}?tipe=OUT" wire:navigate
       wire:loading.class="opacity-40 scale-[0.98] grayscale-[0.5]" wire:target="startDate,endDate,applyFilter"
       class="animate-in animation-delay-300 group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-3 hover:border-rose-400 dark:hover:border-rose-600 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 loading-transition cursor-pointer">
        <div class="shrink-0 w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-500 dark:text-rose-400 group-hover:scale-110 transition-transform">
            <i data-lucide="arrow-up-right" class="w-5 h-5" stroke-width="2.5"></i>
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-1.5 mb-1">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Keluar</p>
                @if($stats['trend_keluar'] !== null)
                    @php $pct = $stats['trend_keluar']; @endphp
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-md {{ $pct >= 0 ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10' : 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10' }}">{{ $pct >= 0 ? '↑' : '↓' }} {{ abs($pct) }}%</span>
                @else
                    <span class="text-[9px] bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 px-1.5 py-0.5 rounded-md font-semibold">Rentang</span>
                @endif
            </div>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white leading-tight">{{ number_format($stats['keluar_range'], 0, ',', '.') }} <span class="text-[11px] font-normal text-slate-400">Unit</span></p>
        </div>
        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 dark:text-slate-700 group-hover:text-rose-400 transition-colors ml-auto shrink-0"></i>
    </a>

</div>

{{-- ─── Chart + Activity Feed ─────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch animate-in animation-delay-300">

    {{-- Chart Panel --}}
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col shadow-sm relative loading-transition" style="height:420px"
         wire:loading.class="opacity-60 grayscale-[0.3]" wire:target="startDate,endDate,applyFilter">

        {{-- Skeleton overlay while loading —— pointer-events-none prevents blocking filter buttons --}}
        <div wire:loading.flex wire:target="startDate,endDate,applyFilter"
            class="absolute inset-0 rounded-2xl z-20 flex flex-col gap-3 p-5 pointer-events-none">
            <div class="skeleton h-5 w-40 rounded-lg"></div>
            <div class="skeleton h-3 w-56 rounded-md"></div>
            <div class="flex-1 flex items-end gap-2 pt-4">
                @for($i = 0; $i < 10; $i++)
                    <div class="skeleton flex-1 rounded-t-md" style="height: {{ rand(20,90) }}%"></div>
                @endfor
            </div>
        </div>

        {{-- Chart header --}}
        <div class="flex items-start justify-between mb-3 shrink-0">
            <div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Arus Barang</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Distribusi masuk & keluar pada rentang terpilih</p>
            </div>
            <div class="flex items-center gap-2 px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 rounded-full border border-emerald-100 dark:border-emerald-500/20 animate-pulse-slow">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]"></span>
                </span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-wider">Live</span>
            </div>
        </div>

        {{-- Empty State --}}
        <div id="chartEmptyState" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-3 z-5">
            <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl">
                <i data-lucide="bar-chart-2" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
            </div>
            <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Tidak Ada Transaksi</p>
            <p class="text-xs text-slate-400 dark:text-slate-600 max-w-[180px] text-center">Pilih rentang tanggal berbeda atau tambah data.</p>
        </div>

        <div id="dashboardEcharts" wire:ignore class="flex-1 w-full min-h-0"></div>
    </div>

    {{-- Activity Feed --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col shadow-sm relative loading-transition" style="height:440px"
         wire:loading.class="opacity-60 grayscale-[0.3]" wire:target="startDate,endDate,applyFilter">

        {{-- Skeleton overlay --}}
        <div wire:loading.flex wire:target="startDate,endDate,applyFilter"
            class="absolute inset-0 rounded-2xl z-20 flex flex-col gap-4 p-5 pointer-events-none">
            <div class="skeleton h-5 w-36 rounded-lg"></div>
            @for($i = 0; $i < 7; $i++)
                <div class="flex gap-3 items-start">
                    <div class="skeleton w-14 h-5 rounded-md shrink-0"></div>
                    <div class="flex flex-col gap-1.5 flex-1">
                        <div class="skeleton h-3 w-full rounded"></div>
                        <div class="skeleton h-2.5 w-2/3 rounded"></div>
                    </div>
                </div>
            @endfor
        </div>

        <div class="flex items-center justify-between mb-4 shrink-0">
            <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Transaksi Terbaru</h3>
            <a href="{{ route('laporan.index') }}"
                class="text-[11px] font-semibold px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 transition-colors">
                Semua →
            </a>
        </div>

        <div class="flex flex-col gap-3 overflow-y-auto no-scrollbar flex-1 min-h-0">
            @forelse($aktivitas as $act)
                <div class="flex items-start gap-3 group">
                    <div class="shrink-0 mt-0.5">
                        @if($act->type == 'IN')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold rounded-md bg-emerald-100 dark:bg-emerald-500/15 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                <i data-lucide="arrow-down-left" class="w-2.5 h-2.5"></i> Masuk
                            </span>
                        @elseif($act->type == 'OUT')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold rounded-md bg-rose-100 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30">
                                <i data-lucide="arrow-up-right" class="w-2.5 h-2.5"></i> Keluar
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-100 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30">
                                <i data-lucide="refresh-cw" class="w-2.5 h-2.5"></i> Adjust
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-1">
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate leading-tight">{{ $act->product->name }}</p>
                            <p class="text-[10px] text-slate-400 whitespace-nowrap">{{ $act->created_at->format('d M') }}</p>
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">
                            <span class="{{ $act->quantity < 0 ? 'text-rose-500 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} font-bold">
                                {{ $act->quantity > 0 ? '+' : '' }}{{ $act->quantity }}
                            </span>
                            <span class="mx-1 text-slate-300 dark:text-slate-600">·</span>
                            {{ $act->user?->name ?? 'Sistem' }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center flex-1 gap-2">
                    <i data-lucide="inbox" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                    <p class="text-xs text-slate-400">Tidak ada aktivitas di periode ini.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/echarts@5.5.0/dist/echarts.min.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    const chartDom   = document.getElementById('dashboardEcharts');
    const emptyState = document.getElementById('chartEmptyState');
    // Use Canvas renderer — more reliable clip behavior than SVG
    let myChart = echarts.init(chartDom, null, { renderer: 'canvas' });

    const getChartOptions = (data, isDark) => {
        const textColor      = isDark ? '#94a3b8' : '#64748b';
        const splitLineColor = isDark ? '#1e293b' : '#f1f5f9';
        const axisLineColor  = isDark ? '#334155' : '#e2e8f0';

        // Check sum (exclude the trailing empty padding entry)
        const realMasuk  = data.masuk.slice(0, -1);
        const realKeluar = data.keluar.slice(0, -1);
        const totalSum   = realMasuk.reduce((a,b) => a+Number(b), 0) + realKeluar.reduce((a,b) => a+Number(b), 0);

        if (totalSum === 0) {
            chartDom.style.opacity = '0';
            emptyState.classList.remove('hidden');
            return {};
        }
        chartDom.style.opacity = '1';
        emptyState.classList.add('hidden');

        return {
            backgroundColor: 'transparent',
            animation: true,
            animationDuration: 400,
            animationEasing: 'cubicOut',

            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'none' },
                backgroundColor: isDark ? 'rgba(15,23,42,0.95)' : 'rgba(255,255,255,0.97)',
                borderColor: isDark ? '#334155' : '#e2e8f0',
                borderWidth: 1,
                borderRadius: 10,
                textStyle: { color: isDark ? '#f1f5f9' : '#0f172a', fontSize: 12 },
                padding: [10, 14],
                formatter: function(params) {
                    if (!params[0].name) return '';
                    let html = `<div style="font-weight:700;margin-bottom:8px;font-size:11px;color:${textColor};letter-spacing:0.04em;text-transform:uppercase">${params[0].name}</div>`;
                    params.forEach(p => {
                        // Display value with series name
                        html += `<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:${p.color}"></span>
                            <span style="flex:1;font-size:12px">${p.seriesName}</span>
                            <b>${Math.abs(p.value).toLocaleString('id-ID')} Unit</b>
                        </div>`;
                    });
                    return html;
                }
            },

            legend: {
                data: ['Barang Masuk', 'Barang Keluar'],
                bottom: 8,
                textStyle: { color: textColor, fontSize: 11, fontWeight: 600 },
                icon: 'roundRect',
                itemWidth: 12, itemHeight: 8, itemGap: 20
            },

            // Mousewheel/pinch zoom only — no slider bar
            dataZoom: [{ type: 'inside', start: 0, end: 100 }],

            grid: {
                left: '25',
                right: '40',
                top: '45',
                bottom: '55',
                containLabel: true
            },

            xAxis: {
                type: 'category',
                data: data.labels,
                boundaryGap: true,  // Half-slot padding on both sides — permanent last-bar fix
                axisLine: { lineStyle: { color: axisLineColor } },
                axisTick: { show: false },
                axisLabel: {
                    color: textColor,
                    fontSize: 9,
                    interval: 'auto',
                    hideOverlap: true,
                    rotate: 0,
                    // Don't render label for the empty trailing slot
                    formatter: v => v || ''
                }
            },

            yAxis: {
                type: 'value',
                splitLine: { lineStyle: { type: 'dashed', color: splitLineColor } },
                axisLabel: {
                    color: textColor, fontSize: 10,
                    formatter: v => v >= 1000 ? (v/1000).toFixed(1)+'k' : v
                },
                min: 0,
                max: v => v.max === 0 ? 10 : Math.ceil(v.max * 1.25)
            },

            series: [
                {
                    name: 'Barang Masuk',
                    type: 'bar',
                    data: data.masuk,
                    itemStyle: {
                        color: { type: 'linear', x:0,y:0,x2:0,y2:1,
                            colorStops: [
                                { offset: 0, color: isDark ? '#34d399' : '#10b981' },
                                { offset: 1, color: isDark ? '#10b98199' : '#10b98177' }
                            ]
                        },
                        borderRadius: [5, 5, 0, 0]
                    },
                    barMaxWidth: 32,
                    emphasis: { itemStyle: { opacity: 0.85 } }
                },
                {
                    name: 'Barang Keluar',
                    type: 'bar',
                    data: data.keluar,
                    itemStyle: {
                        color: { type: 'linear', x:0,y:0,x2:0,y2:1,
                            colorStops: [
                                { offset: 0, color: isDark ? '#fb7185' : '#f43f5e' },
                                { offset: 1, color: isDark ? '#f43f5e99' : '#f43f5e77' }
                            ]
                        },
                        borderRadius: [5, 5, 0, 0]
                    },
                    barMaxWidth: 32,
                    emphasis: { itemStyle: { opacity: 0.85 } }
                }
            ]
        };
    };

    const renderChart = (data) => {
        const isDark = document.documentElement.classList.contains('dark');
        const opts   = getChartOptions(data, isDark);
        if (Object.keys(opts).length > 0) {
            myChart.setOption(opts, true);
        } else {
            myChart.clear();
        }
    };

    renderChart(@json($chartData));

    Livewire.on('updateDashboardChart', (event) => {
        let payload = event[0]?.data ?? event?.data ?? event;
        renderChart(payload);
    });

    new MutationObserver(() => renderChart(@json($chartData)))
        .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

    window.addEventListener('resize', () => myChart.resize());
});
</script>
@endpush
