
<x-slot:header>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex flex-col">
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Overview</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Status dan statistik pergerakan barang gudang.</p>
        </div>

        <!-- Global Date Filter for Dashboard -->
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="flex bg-slate-100 dark:bg-slate-900/50 p-1 rounded-lg border border-slate-200 dark:border-slate-800">
                <button wire:click="applyFilter('today')" class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ $activeFilter === 'today' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">Hari Ini</button>
                <button wire:click="applyFilter('last_7_days')" class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ $activeFilter === 'last_7_days' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">7 Hari</button>
                <button wire:click="applyFilter('this_month')" class="px-3 py-1 text-xs font-medium rounded-md transition-colors {{ $activeFilter === 'this_month' ? 'bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300' }}">Bulan Ini</button>
            </div>
        </div>
    </div>
</x-slot:header>

<div>
    <!-- Stats Row (Responsive to Date Filter where applicable) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6 lg:mb-8" wire:loading.class="opacity-50 pointer-events-none transition-opacity duration-300">
        <!-- Total Inventory -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none min-w-0">
            <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-emerald-500/10 text-blue-600 dark:text-blue-400 transition-colors duration-300 ease-in-out shrink-0">
                <i data-lucide="package" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0">
                <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider whitespace-nowrap overflow-hidden text-ellipsis transition-colors duration-300 ease-in-out">Total Produk & Fisik</p>
                    <span class="text-[8px] bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded font-medium">Global</span>
                </div>
                <div class="flex flex-col xl:flex-row xl:items-baseline xl:gap-2">
                    <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out truncate">{{ number_format($stats['total_jenis'], 0, ',', '.') }} <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 font-normal transition-colors duration-300 ease-in-out">Jenis</span></h3>
                    <span class="hidden xl:inline text-slate-300 dark:text-slate-600 transition-colors duration-300 ease-in-out">&bull;</span>
                    <h3 class="text-sm sm:text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 xl:mt-0 transition-colors duration-300 ease-in-out truncate">{{ number_format($stats['total_inventory'], 0, ',', '.') }} <span class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400/70 font-normal transition-colors duration-300 ease-in-out">Fisik</span></h3>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none min-w-0">
            <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-500 dark:text-rose-400 transition-colors duration-300 ease-in-out shrink-0">
                <i data-lucide="alert-circle" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0">
                <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider line-clamp-1 transition-colors duration-300 ease-in-out">Stok Kritis</p>
                    <span class="text-[8px] bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 px-1.5 py-0.5 rounded font-medium">Global</span>
                </div>
                <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['low_stock'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Masuk Range -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none min-w-0">
            <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-600 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-500 transition-colors duration-300 ease-in-out shrink-0">
                <i data-lucide="arrow-down-left" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0">
                <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider line-clamp-1 transition-colors duration-300 ease-in-out">Stok Masuk</p>
                    <span class="text-[8px] bg-blue-50 dark:bg-blue-500/10 text-blue-500 dark:text-blue-400 px-1.5 py-0.5 rounded font-medium">Rentang</span>
                </div>
                <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['masuk_range'], 0, ',', '.') }}</h3>
            </div>
        </div>

        <!-- Keluar Range -->
        <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none min-w-0">
            <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 dark:text-rose-400 transition-colors duration-300 ease-in-out shrink-0">
                <i data-lucide="arrow-up-right" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
            </div>
            <div class="w-full min-w-0">
                <div class="flex items-center justify-between mb-0.5 sm:mb-1">
                    <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider line-clamp-1 transition-colors duration-300 ease-in-out">Stok Keluar</p>
                    <span class="text-[8px] bg-blue-50 dark:bg-blue-500/10 text-blue-500 dark:text-blue-400 px-1.5 py-0.5 rounded font-medium">Rentang</span>
                </div>
                <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['keluar_range'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>

    <!-- Chart & Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Transaksi -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 lg:p-5 flex flex-col h-[300px] lg:h-[400px] shadow-sm dark:shadow-none transition-colors duration-300 ease-in-out relative">
            
            <!-- Filter Loading Overlay -->
            <div wire:loading.flex wire:target="startDate, endDate, applyFilter" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>

            <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-800 pb-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Arus Barang</h3>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5">Perbandingan barang masuk dan keluar.</p>
                </div>
            </div>

            <!-- Empty State Illustration -->
            <div id="chartEmptyState" class="hidden absolute inset-0 pt-16 flex flex-col items-center justify-center gap-3">
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-full">
                    <i data-lucide="bar-chart-2" class="w-8 h-8 text-slate-300 dark:text-slate-600"></i>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Tidak Ada Transaksi</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-[200px]">Pilih rentang tanggal lain atau catat barang masuk/keluar.</p>
                </div>
            </div>

            <!-- ECharts Container -->
            <div id="dashboardEcharts" wire:ignore class="flex-1 w-full h-full min-h-[220px] relative z-0"></div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 flex-1 shadow-sm dark:shadow-none transition-colors duration-300 ease-in-out relative">
            <div wire:loading.flex wire:target="startDate, endDate, applyFilter" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
            </div>

            <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-800 pb-3 transition-colors duration-300 ease-in-out">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">Transaksi Terbaru</h3>
                <a href="{{ route('laporan.index') }}" class="text-[10px] sm:text-xs font-semibold px-2 sm:px-3 py-1 sm:py-1.5 bg-blue-50 hover:bg-blue-100 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded-lg transition-colors duration-300 ease-in-out">Semua Histori</a>
            </div>
            
            <div class="space-y-4">
                @forelse($aktivitas as $act)
                    <div class="flex gap-3 items-start group">
                        <div class="mt-0.5">
                            <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded {{ $act->type == 'IN' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-500 border border-emerald-200 dark:border-emerald-500/20' : ($act->type == 'OUT' ? 'bg-rose-100 dark:bg-rose-500/10 text-rose-600 dark:text-rose-500 border border-rose-200 dark:border-rose-500/20' : 'bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20') }} transition-colors duration-300 ease-in-out">
                                {{ $act->type == 'IN' ? 'Masuk' : ($act->type == 'OUT' ? 'Keluar' : 'Adjust') }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline mb-0.5">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate transition-colors duration-300 ease-in-out">{{ $act->product->name }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 whitespace-nowrap ml-2 transition-colors duration-300 ease-in-out">{{ $act->created_at->format('d/m') }}</p>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate transition-colors duration-300 ease-in-out">
                                <span class="text-slate-800 dark:text-white transition-colors duration-300 ease-in-out">{{ $act->quantity }} Unit</span> &bull; {{ $act->user?->name ?? 'Sistem' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 transition-colors duration-300 ease-in-out bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                        <p class="text-xs text-slate-400 dark:text-slate-500 transition-colors duration-300 ease-in-out">Tidak ada aktivitas pada rentang tanggal ini.</p>
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
                    left: '0%',
                    right: '2%',
                    top: '10%',
                    bottom: '12%',
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
                        interval: 'auto', // Auto-skip labels to prevent overlap
                        rotate: 45,       // Angled labels for better X-axis fitting
                        margin: 12
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
                series: [
                    {
                        name: 'Barang Masuk',
                        type: 'bar',
                        data: data.masuk,
                        itemStyle: { 
                            color: isDark ? '#10b981' : '#34d399', // Emerald
                            borderRadius: [4, 4, 0, 0] // Rounded tops
                        },
                        barMaxWidth: 30
                    },
                    {
                        name: 'Barang Keluar',
                        type: 'bar',
                        data: data.keluar,
                        itemStyle: { 
                            color: isDark ? '#f43f5e' : '#fb7185', // Rose
                            borderRadius: [4, 4, 0, 0] // Rounded tops
                        },
                        barMaxWidth: 30
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
