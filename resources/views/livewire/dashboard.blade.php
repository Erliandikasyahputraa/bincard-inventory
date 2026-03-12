
<x-slot:header>
    <h2 class="font-bold text-lg text-slate-800 dark:text-slate-200 leading-tight">
        Dashboard
    </h2>
</x-slot:header>

<div class="px-2 sm:px-0">
    <!-- Header Title & Filters -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div class="flex flex-col">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300">Overview Gudang</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 transition-colors duration-300">Status, metrik, dan statistik pergerakan barang saat ini.</p>
        </div>

        <!-- Global Date Filter for Dashboard -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
            <div class="flex items-center justify-between sm:justify-start bg-slate-100 dark:bg-slate-900/50 p-1.5 rounded-xl border border-slate-200 dark:border-slate-800/80">
                <button wire:click="applyFilter('today')" class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 shadow-sm {{ $activeFilter === 'today' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-slate-200/50 dark:border-slate-700/50' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">Hari Ini</button>
                <button wire:click="applyFilter('last_7_days')" class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 shadow-sm {{ $activeFilter === 'last_7_days' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-slate-200/50 dark:border-slate-700/50' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">7 Hari</button>
                <button wire:click="applyFilter('this_month')" class="flex-1 sm:flex-none px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 shadow-sm {{ $activeFilter === 'this_month' ? 'bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 border border-slate-200/50 dark:border-slate-700/50' : 'text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200' }}">Bulan Ini</button>
            </div>
            
            <div class="flex items-center gap-2 w-full sm:w-auto mt-1 sm:mt-0">
                <div class="w-full sm:w-36">
                    <input type="date" wire:model.live="startDate" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 transition-all px-3 py-2 text-sm outline-none [color-scheme:light] dark:[color-scheme:dark]" title="Mulai Tanggal">
                </div>
                <div class="hidden sm:block text-slate-400 font-bold">-</div>
                <div class="w-full sm:w-36">
                    <input type="date" wire:model.live="endDate" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-600 rounded-xl text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-900 focus:border-blue-500 dark:border-blue-400 focus:ring-1 focus:ring-blue-500 transition-all px-3 py-2 text-sm outline-none [color-scheme:light] dark:[color-scheme:dark]" title="Sampai Tanggal">
                </div>
            </div>
        </div>
    </div>
    <!-- Stats Row (Responsive to Date Filter where applicable) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6 lg:mb-8" wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300">
        <!-- Total Inventory -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 hover:shadow-md transition-all shadow-sm dark:shadow-none min-w-0">
            <div class="flex-shrink-0 w-8 h-8 sm:w-12 sm:h-12 flex items-center justify-center rounded-lg sm:rounded-xl bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                <i data-lucide="package" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0 flex flex-col justify-center">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Katalog</p>
                    <span class="text-[8px] bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded font-medium whitespace-nowrap">Global</span>
                </div>
                <div class="flex flex-col xl:flex-row xl:items-baseline xl:gap-2">
                    <div class="flex items-baseline gap-1">
                        <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white truncate">{{ number_format($stats['total_jenis'], 0, ',', '.') }}</h3>
                        <span class="text-[9px] sm:text-[10px] text-slate-400">Jenis</span>
                    </div>
                    <span class="hidden xl:inline text-slate-300 dark:text-slate-600">&bull;</span>
                    <div class="flex items-baseline gap-1 mt-0.5 xl:mt-0">
                        <h3 class="text-sm sm:text-lg font-bold text-blue-600 dark:text-blue-400 truncate">{{ number_format($stats['total_inventory'], 0, ',', '.') }}</h3>
                        <span class="text-[9px] sm:text-[10px] text-blue-400/70">Fisik</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 hover:shadow-md transition-all shadow-sm dark:shadow-none min-w-0">
            <div class="flex-shrink-0 w-8 h-8 sm:w-12 sm:h-12 flex items-center justify-center rounded-lg sm:rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">
                <i data-lucide="alert-circle" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0 flex flex-col justify-center">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Kritis</p>
                    <span class="text-[8px] bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded font-medium whitespace-nowrap">Global</span>
                </div>
                <div class="flex items-baseline gap-1 mt-auto sm:mt-0">
                    <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white truncate">{{ number_format($stats['low_stock'], 0, ',', '.') }}</h3>
                    <span class="text-[9px] sm:text-[10px] text-slate-400">Brg</span>
                </div>
            </div>
        </div>

        <!-- Masuk Range -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 hover:shadow-md transition-all shadow-sm dark:shadow-none min-w-0">
            <div class="flex-shrink-0 w-8 h-8 sm:w-12 sm:h-12 flex items-center justify-center rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-500">
                <i data-lucide="arrow-down-left" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0 flex flex-col justify-center">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Masuk</p>
                    <span class="text-[8px] bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 px-1.5 py-0.5 rounded font-medium whitespace-nowrap">Rentang</span>
                </div>
                <div class="flex items-baseline gap-1 mt-auto sm:mt-0">
                    <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white truncate">{{ number_format($stats['masuk_range'], 0, ',', '.') }}</h3>
                    <span class="text-[9px] sm:text-[10px] text-slate-400">Unit</span>
                </div>
            </div>
        </div>

        <!-- Keluar Range -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-4 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 hover:shadow-md transition-all shadow-sm dark:shadow-none min-w-0">
            <div class="flex-shrink-0 w-8 h-8 sm:w-12 sm:h-12 flex items-center justify-center rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 dark:text-rose-400">
                <i data-lucide="arrow-up-right" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0 flex flex-col justify-center">
                <div class="flex items-center justify-between gap-1 mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">Keluar</p>
                    <span class="text-[8px] bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 px-1.5 py-0.5 rounded font-medium whitespace-nowrap">Rentang</span>
                </div>
                <div class="flex items-baseline gap-1 mt-auto sm:mt-0">
                    <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white truncate">{{ number_format($stats['keluar_range'], 0, ',', '.') }}</h3>
                    <span class="text-[9px] sm:text-[10px] text-slate-400">Unit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart & Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
        <!-- Grafik Transaksi (Takes 2/3 width on large screens) -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 lg:p-5 flex flex-col h-[400px] lg:h-[450px] min-w-0 min-h-0 shadow-sm dark:shadow-none relative">
            
            <!-- Filter Loading Overlay -->
            <div wire:loading.flex wire:target="startDate, endDate, applyFilter" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>

            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Arus Barang</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Distribusi jumlah transaksi per rentang waktu.</p>
                </div>
            </div>

            <!-- Empty State Illustration -->
            <div id="chartEmptyState" class="hidden absolute inset-0 flex flex-col items-center justify-center gap-3">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-full">
                    <i data-lucide="bar-chart-2" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Tidak Ada Transaksi</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-[200px] mx-auto">Pilih rentang tanggal lain atau catat barang masuk/keluar.</p>
                </div>
            </div>

            <!-- ECharts Container -->
            <div id="dashboardEcharts" wire:ignore class="flex-1 w-full relative z-0 mt-4 min-h-[250px] sm:min-h-[300px]"></div>
        </div>

        <!-- Aktivitas Terbaru (Takes 1/3 width on large screens) -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col h-[400px] lg:h-[450px] shadow-sm dark:shadow-none relative">
            <div wire:loading.flex wire:target="startDate, endDate, applyFilter" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
            </div>

            <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-800 pb-3 flex-shrink-0">
                <h3 class="text-lg font-bold text-slate-800 dark:text-slate-200">Transaksi Terbaru</h3>
                <a href="{{ route('laporan.index') }}" class="text-xs font-semibold px-3 py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-lg transition-colors">Semua Histori</a>
            </div>
            
            <div class="space-y-4 overflow-y-auto pr-2 no-scrollbar flex-1 relative min-h-0">
                @forelse($aktivitas as $act)
                    <div class="flex gap-3 items-start group">
                        <div class="mt-0.5">
                            <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded {{ $act->type == 'IN' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-500 border border-emerald-200 dark:border-emerald-500/20' : ($act->type == 'OUT' ? 'bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-500 border border-rose-200 dark:border-rose-500/20' : 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20') }} transition-colors">
                                {{ $act->type == 'IN' ? 'Masuk' : ($act->type == 'OUT' ? 'Keluar' : 'Adjust') }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $act->product->name }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap ml-2">{{ $act->created_at->format('d M') }}</p>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate">
                                <span class="text-slate-800 dark:text-white font-semibold">{{ $act->quantity }} Unit</span> &bull; {{ $act->user?->name ?? 'Sistem' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 absolute inset-0 flex flex-col justify-center items-center">
                        <p class="text-xs text-slate-400 dark:text-slate-500 mb-2">Tidak ada aktivitas pada rentang tanggal ini.</p>
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
        const chartDom = document.getElementById('dashboardEcharts');
        const emptyState = document.getElementById('chartEmptyState');
        let myChart = echarts.init(chartDom);
        
        // Polished ECharts Configuration
        const getChartOptions = (data, isDark) => {
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const splitLineColor = isDark ? '#334155' : '#e2e8f0';
            
            const totalSum = data.masuk.reduce((a, b) => a + Number(b), 0) + data.keluar.reduce((a, b) => a + Number(b), 0);
            if (totalSum === 0) {
                chartDom.style.opacity = '0';
                emptyState.classList.remove('hidden');
                return {};
            } else {
                chartDom.style.opacity = '1';
                emptyState.classList.add('hidden');
            }

            return {
                backgroundColor: 'transparent',
                tooltip: {
                    trigger: 'axis',
                    axisPointer: { type: 'shadow' },
                    backgroundColor: isDark ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                    borderColor: isDark ? '#334155' : '#e2e8f0',
                    textStyle: { color: isDark ? '#f8fafc' : '#0f172a' },
                    padding: [8, 12],
                    formatter: function(params) {
                        let result = `<div style="font-weight:bold;margin-bottom:6px;font-size:12px;color:${textColor}">${params[0].name}</div>`;
                        params.forEach(param => {
                            if (param.value > 0) {
                                const circle = `<span style="display:inline-block;margin-right:5px;border-radius:50%;width:8px;height:8px;background-color:${param.color}"></span>`;
                                result += `<div style="font-size:12px;margin-bottom:3px">${circle} ${param.seriesName}: <b>${param.value.toLocaleString()} Unit</b></div>`;
                            }
                        });
                        return result;
                    }
                },
                legend: {
                    data: ['Barang Masuk', 'Barang Keluar'],
                    bottom: '0',
                    textStyle: { color: textColor, fontSize: 11 },
                    icon: 'circle',
                    itemWidth: 8,
                    itemHeight: 8,
                    itemGap: 15
                },
                grid: {
                    left: '1%',
                    right: '3%',
                    top: '5%',
                    bottom: '32%',
                    containLabel: true
                },
                xAxis: {
                    type: 'category',
                    data: data.labels,
                    axisLine: { lineStyle: { color: splitLineColor } },
                    axisTick: { show: false },
                    axisLabel: { 
                        color: textColor,
                        fontSize: 10,
                        interval: 'auto',    // Auto-skip labels to prevent overlap
                        rotate: 0,           // No rotation keeps bars within boundary
                        hideOverlap: true,   // Automatically hide labels that would overlap
                        overflow: 'truncate',
                        width: 60
                    }
                },
                yAxis: {
                    type: 'value',
                    splitLine: { 
                        lineStyle: { 
                            type: 'dashed',
                            color: splitLineColor,
                            width: 1
                        } 
                    },
                    axisLabel: { color: textColor, fontSize: 10 },
                    // Dynamic Scaling handling
                    scale: true, 
                    min: 0,
                    // Suggested Max so bars look fuller, but leave room for large spikes automatically
                    max: function (value) {
                         // Always create roof of +20% above the single highest spike
                         return value.max === 0 ? 10 : Math.ceil(value.max * 1.2);
                    }
                },
                // Dual dataZoom: inside (mouse-wheel/touch) + slider (visible drag bar)
                // Use percentage-based start/end so slider handle never goes out of bounds
                dataZoom: (function() {
                    const total = data.labels.length;
                    const show = Math.min(10, total);                         // Max 10 columns visible
                    const startPct = total > 0 ? Math.round(((total - show) / total) * 100) : 0;
                    const shared = { start: startPct, end: 100, zoomLock: true };
                    return [
                        Object.assign({ type: 'inside', moveOnMouseWheel: true, moveOnMouseMove: true }, shared),
                        Object.assign({
                            type: 'slider',
                            height: 20,
                            bottom: '13%',
                            borderColor: isDark ? '#334155' : '#e2e8f0',
                            backgroundColor: isDark ? '#1e293b' : '#f1f5f9',
                            fillerColor: isDark ? 'rgba(59,130,246,0.2)' : 'rgba(59,130,246,0.12)',
                            handleStyle: { color: '#3b82f6', borderColor: '#3b82f6' },
                            textStyle: { color: textColor, fontSize: 9 },
                            showDetail: false,
                            showDataShadow: true,
                            dataBackground: {
                                lineStyle: { color: '#3b82f6', opacity: 0.3 },
                                areaStyle: { color: '#3b82f6', opacity: 0.08 }
                            }
                        }, shared)
                    ];
                })(),
                series: [
                    {
                        name: 'Barang Masuk',
                        type: 'bar',
                        data: data.masuk,
                        itemStyle: { 
                            color: isDark ? '#10b981' : '#34d399', // Emerald
                            borderRadius: [4, 4, 0, 0] // Rounded tops
                        },
                        barMaxWidth: 28,
                        barGap: '10%'
                    },
                    {
                        name: 'Barang Keluar',
                        type: 'bar',
                        data: data.keluar,
                        itemStyle: { 
                            color: isDark ? '#f43f5e' : '#fb7185', // Rose
                            borderRadius: [4, 4, 0, 0] // Rounded tops
                        },
                        barMaxWidth: 28,
                        barGap: '10%'
                    }
                ]
            };
        };

        const renderChart = (data) => {
            const isDark = document.documentElement.classList.contains('dark');
            const options = getChartOptions(data, isDark);
            if (Object.keys(options).length > 0) {
                myChart.setOption(options, true);
            } else {
                myChart.clear();
            }
        };

        // Initial render pulling from the backend mounted prop
        renderChart(@json($chartData));

        // Listen for updates from Livewire when date filter changes
        Livewire.on('updateDashboardChart', (event) => {
            // Livewire 3 returns event as an object standardly when named named-args dispatch is used
            let payload = event[0] ? event[0].data : event.data;
            if(!payload && event) payload = event;
            renderChart(payload);
        });

        // Handle Theme Changes seamlessly
        const observer = new MutationObserver(() => {
            const isDark = document.documentElement.classList.contains('dark');
            let currentOptions = myChart.getOption();
            if (currentOptions && Object.keys(currentOptions).length > 0) {
                // If it was already rendered, just update the colors based on theme
                const textColor = isDark ? '#94a3b8' : '#64748b';
                const splitLineColor = isDark ? '#334155' : '#e2e8f0';
                
                myChart.setOption({
                    tooltip: {
                        backgroundColor: isDark ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        textStyle: { color: isDark ? '#f8fafc' : '#0f172a' }
                    },
                    legend: { textStyle: { color: textColor } },
                    xAxis: {
                        axisLine: { lineStyle: { color: splitLineColor } },
                        axisLabel: { color: textColor }
                    },
                    yAxis: {
                        splitLine: { lineStyle: { color: splitLineColor } },
                        axisLabel: { color: textColor }
                    },
                    series: [
                        { itemStyle: { color: isDark ? '#10b981' : '#34d399' } },
                        { itemStyle: { color: isDark ? '#f43f5e' : '#fb7185' } }
                    ]
                });
            }
        });

        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        // Responsive Resize
        window.addEventListener('resize', () => myChart.resize());
    });
</script>
@endpush
