
<x-slot:header>
    <h2 class="font-bold text-lg text-slate-800 dark:text-slate-200 leading-tight">
        Dashboard
    </h2>
</x-slot:header>

<div class="px-2 sm:px-0 space-y-6">

    {{-- ─── Header + Filter Row ───────────────────────────── --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Overview Gudang</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Status, metrik, dan statistik pergerakan barang.</p>
        </div>

        {{-- Filter Pill + Date Pickers --}}
        <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
            {{-- Quick-period pill --}}
            <div class="inline-flex items-center bg-slate-100 dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 gap-0.5">
                @foreach(['today' => 'Hari Ini', 'last_7_days' => '7 Hari', 'this_month' => 'Bulan Ini'] as $key => $label)
                    <button wire:click="applyFilter('{{ $key }}')"
                        class="px-3 py-1.5 text-[11px] font-bold rounded-lg transition-all duration-200
                            {{ $activeFilter === $key
                                ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-sm border border-slate-200 dark:border-slate-700'
                                : 'text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            {{-- Custom date range --}}
            <div class="flex items-center gap-1.5">
                <input type="date" wire:model.live="startDate"
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-blue-400 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark] w-32 sm:w-36">
                <span class="text-slate-400 text-sm font-bold">→</span>
                <input type="date" wire:model.live="endDate"
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 hover:border-blue-400 rounded-lg px-2.5 py-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition-all [color-scheme:light] dark:[color-scheme:dark] w-32 sm:w-36">
            </div>
        </div>
    </div>

    {{-- ─── Stats Cards ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 lg:gap-4" wire:loading.class="opacity-60 pointer-events-none">

        {{-- Katalog --}}
        <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-4 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="shrink-0 w-11 h-11 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500 dark:text-blue-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-500/20 transition-colors">
                <i data-lucide="package-2" class="w-5 h-5" stroke-width="2"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Katalog</p>
                <div class="flex items-baseline gap-1.5 flex-wrap">
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['total_jenis'], 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-400">Jenis</span>
                    <span class="text-slate-300 dark:text-slate-600">·</span>
                    <span class="text-base font-bold text-blue-500 dark:text-blue-400">{{ number_format($stats['total_inventory'], 0, ',', '.') }}</span>
                    <span class="text-[10px] text-blue-400/70">Fisik</span>
                </div>
            </div>
        </div>

        {{-- Stok Kritis --}}
        <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-4 hover:border-orange-300 dark:hover:border-orange-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="shrink-0 w-11 h-11 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-500 dark:text-orange-400 group-hover:bg-orange-100 dark:group-hover:bg-orange-500/20 transition-colors">
                <i data-lucide="alert-triangle" class="w-5 h-5" stroke-width="2"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Stok Kritis</p>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['low_stock'], 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-400">Barang</span>
                </div>
            </div>
        </div>

        {{-- Masuk --}}
        <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-4 hover:border-emerald-300 dark:hover:border-emerald-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="shrink-0 w-11 h-11 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-100 dark:group-hover:bg-emerald-500/20 transition-colors">
                <i data-lucide="arrow-down-left" class="w-5 h-5" stroke-width="2.5"></i>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Masuk</p>
                    <span class="text-[9px] bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-1.5 py-0.5 rounded-md font-semibold">Rentang</span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['masuk_range'], 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-400">Unit</span>
                </div>
            </div>
        </div>

        {{-- Keluar --}}
        <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 flex items-center gap-4 hover:border-rose-300 dark:hover:border-rose-700 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            <div class="shrink-0 w-11 h-11 rounded-xl bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center text-rose-500 dark:text-rose-400 group-hover:bg-rose-100 dark:group-hover:bg-rose-500/20 transition-colors">
                <i data-lucide="arrow-up-right" class="w-5 h-5" stroke-width="2.5"></i>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5 mb-1">
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Keluar</p>
                    <span class="text-[9px] bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 px-1.5 py-0.5 rounded-md font-semibold">Rentang</span>
                </div>
                <div class="flex items-baseline gap-1.5">
                    <span class="text-xl font-extrabold text-slate-900 dark:text-white">{{ number_format($stats['keluar_range'], 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-400">Unit</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ─── Chart + Activity Feed ─────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">

        {{-- Chart Panel (2/3) --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col shadow-sm relative" style="height: 440px;">

            {{-- Loading overlay --}}
            <div wire:loading.flex wire:target="startDate, endDate, applyFilter"
                class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
            </div>

            {{-- Chart header --}}
            <div class="flex items-start justify-between mb-3 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Arus Barang</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Distribusi masuk & keluar pada rentang terpilih</p>
                </div>
                {{-- Live dot --}}
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium">Live</span>
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

            {{-- ECharts canvas --}}
            <div id="dashboardEcharts" wire:ignore class="flex-1 w-full min-h-0"></div>
        </div>

        {{-- Activity Feed (1/3) --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col shadow-sm relative" style="height: 440px;">

            <div wire:loading.flex wire:target="startDate, endDate, applyFilter"
                class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
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
                        {{-- Type badge --}}
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
                        {{-- Info --}}
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
        let myChart = echarts.init(chartDom, null, { renderer: 'svg' });

        const getChartOptions = (data, isDark) => {
            const textColor      = isDark ? '#94a3b8' : '#64748b';
            const splitLineColor = isDark ? '#1e293b' : '#f1f5f9';
            const axisLineColor  = isDark ? '#334155' : '#e2e8f0';

            const totalSum = data.masuk.reduce((a, b) => a + Number(b), 0)
                           + data.keluar.reduce((a, b) => a + Number(b), 0);

            if (totalSum === 0) {
                chartDom.style.opacity = '0';
                emptyState.classList.remove('hidden');
                return {};
            }
            chartDom.style.opacity = '1';
            emptyState.classList.add('hidden');

            const masukColor  = isDark ? '#10b981' : '#10b981';
            const keluarColor = isDark ? '#f43f5e' : '#f43f5e';

            return {
                backgroundColor: 'transparent',
                animation: true,
                animationDuration: 500,
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
                        let html = `<div style="font-weight:700;margin-bottom:8px;font-size:11px;color:${textColor};letter-spacing:0.05em;text-transform:uppercase">${params[0].name}</div>`;
                        params.forEach(p => {
                            if (p.value > 0) {
                                html += `<div style="display:flex;align-items:center;gap:8px;margin-bottom:5px">
                                    <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:${p.color}"></span>
                                    <span style="flex:1">${p.seriesName}</span>
                                    <b>${p.value.toLocaleString('id-ID')} Unit</b>
                                </div>`;
                            }
                        });
                        return html;
                    }
                },

                legend: {
                    data: ['Barang Masuk', 'Barang Keluar'],
                    bottom: 2,
                    textStyle: { color: textColor, fontSize: 11, fontWeight: 500 },
                    icon: 'roundRect',
                    itemWidth: 12, itemHeight: 8, itemGap: 20,
                    borderRadius: 4
                },

                grid: {
                    left: '2%',
                    right: '2%',
                    top: '8%',
                    bottom: '18%',
                    containLabel: true
                },

                xAxis: {
                    type: 'category',
                    data: data.labels,
                    // boundaryGap ensures first & last bars are wrapped by half a slot on each side
                    boundaryGap: true,
                    axisLine: { lineStyle: { color: axisLineColor } },
                    axisTick: { show: false },
                    axisLabel: {
                        color: textColor,
                        fontSize: 10,
                        interval: 'auto',
                        hideOverlap: true
                    }
                },

                yAxis: {
                    type: 'value',
                    splitLine: { lineStyle: { type: 'dashed', color: splitLineColor } },
                    axisLabel: {
                        color: textColor,
                        fontSize: 10,
                        formatter: v => v >= 1000 ? (v / 1000).toFixed(1) + 'k' : v
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
                            color: {
                                type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                                colorStops: [
                                    { offset: 0, color: isDark ? '#34d399' : '#10b981' },
                                    { offset: 1, color: isDark ? '#10b981cc' : '#10b98180' }
                                ]
                            },
                            borderRadius: [5, 5, 0, 0]
                        },
                        barMaxWidth: 32,
                        emphasis: { itemStyle: { opacity: 1 } }
                    },
                    {
                        name: 'Barang Keluar',
                        type: 'bar',
                        data: data.keluar,
                        itemStyle: {
                            color: {
                                type: 'linear', x: 0, y: 0, x2: 0, y2: 1,
                                colorStops: [
                                    { offset: 0, color: isDark ? '#fb7185' : '#f43f5e' },
                                    { offset: 1, color: isDark ? '#f43f5ecc' : '#f43f5e80' }
                                ]
                            },
                            borderRadius: [5, 5, 0, 0]
                        },
                        barMaxWidth: 32,
                        emphasis: { itemStyle: { opacity: 1 } }
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

        // Theme change
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark');
            const opts   = myChart.getOption();
            if (opts && Object.keys(opts).length > 0) renderChart(opts._payload || @json($chartData));
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        window.addEventListener('resize', () => myChart.resize());
    });
</script>
@endpush
