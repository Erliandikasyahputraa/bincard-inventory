@extends('layouts.app')
@section('title', 'Dashboard - Bincard Pro')
@section('content')

<div class="mb-4 lg:mb-8">
    <h1 class="text-xl lg:text-2xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Overview</h1>
    <p class="text-slate-500 dark:text-slate-400 text-xs lg:text-sm mt-1 transition-colors duration-300 ease-in-out">Status gudang dan statistik hari ini.</p>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6 lg:mb-8">
    <!-- Total Inventory -->
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-emerald-500/10 text-blue-600 dark:text-blue-400 transition-colors duration-300 ease-in-out">
            <i data-lucide="package" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
        </div>
        <div>
            <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5 sm:mb-1 line-clamp-1 transition-colors duration-300 ease-in-out">Total Produk & Stok</p>
            <div class="flex items-baseline gap-2">
                <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['total_jenis'], 0, ',', '.') }} <span class="text-xs text-slate-400 dark:text-slate-500 font-normal transition-colors duration-300 ease-in-out">Jenis</span></h3>
                <span class="hidden sm:inline text-sm font-bold text-blue-600 dark:text-blue-400 transition-colors duration-300 ease-in-out">&bull; {{ number_format($stats['total_inventory'], 0, ',', '.') }} <span class="text-[10px] text-blue-400 dark:text-blue-400/70 font-normal transition-colors duration-300 ease-in-out">Fisik</span></span>
            </div>
            <p class="sm:hidden text-xs font-bold text-blue-600 dark:text-blue-400 mt-1 transition-colors duration-300 ease-in-out">{{ number_format($stats['total_inventory'], 0, ',', '.') }} <span class="text-[10px] text-blue-400 dark:text-blue-400/70 font-normal transition-colors duration-300 ease-in-out">Fisik</span></p>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 dark:text-rose-400 transition-colors duration-300 ease-in-out">
            <i data-lucide="alert-circle" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
        </div>
        <div>
            <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5 sm:mb-1 line-clamp-1 transition-colors duration-300 ease-in-out">Stok Kritis</p>
            <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['low_stock'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Masuk 24h -->
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-emerald-50 dark:bg-emerald-600 dark:bg-emerald-500/10 text-emerald-600 dark:text-[#3FB950] transition-colors duration-300 ease-in-out">
            <i data-lucide="arrow-down-left" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
        </div>
        <div>
            <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5 sm:mb-1 line-clamp-1 transition-colors duration-300 ease-in-out">Stok Masuk</p>
            <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['masuk_24h'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Keluar 24h -->
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-orange-50 dark:bg-orange-500/10 text-orange-500 dark:text-orange-400 transition-colors duration-300 ease-in-out">
            <i data-lucide="arrow-up-right" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
        </div>
        <div>
            <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5 sm:mb-1 line-clamp-1 transition-colors duration-300 ease-in-out">Stok Keluar</p>
            <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out">{{ number_format($stats['keluar_24h'], 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Grafik Transaksi -->
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 p-4 lg:p-5 flex flex-col min-h-[350px] lg:min-h-[400px] shadow-sm dark:shadow-none transition-colors duration-300 ease-in-out">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h2 class="text-md lg:text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">Grafik Transaksi (6 Bulan Terakhir)</h2>
        </div>
        
        <div class="flex-1 w-full relative min-h-[300px] overflow-hidden">
            <div id="chart-scroll-wrapper" class="w-full h-full overflow-x-auto no-scrollbar scroll-smooth">
                <div id="transaction-chart" class="min-w-[500px] lg:min-w-full h-full">
                    <!-- ApexCharts will render here -->
                </div>
            </div>
        </div>
    </div>


    <!-- Right Column Layout -->
    <div class="flex flex-col gap-6">
        
        <!-- Quick Scan Widget -->
        <div class="bg-blue-600 dark:bg-blue-500 rounded-2xl p-5 lg:p-6 flex flex-col text-white relative overflow-hidden group border border-blue-500 dark:border-blue-400 transition-colors duration-300 ease-in-out">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl transition-colors duration-300 ease-in-out"></div>
            
            <div class="relative z-10 flex flex-row lg:flex-col justify-between items-center lg:items-start gap-4 lg:gap-0">
                <div class="flex-1">
                    <div class="flex items-center gap-2 lg:gap-3 mb-1 lg:mb-2">
                        <div class="p-1.5 lg:p-2 bg-white/20 rounded-lg backdrop-blur-sm hidden sm:block transition-colors duration-300 ease-in-out">
                            <i data-lucide="qr-code" class="w-4 h-4 lg:w-5 lg:h-5"></i>
                        </div>
                        <h3 class="text-base lg:text-lg font-bold tracking-tight transition-colors duration-300 ease-in-out">Quick Scan QR</h3>
                    </div>
                    <p class="text-blue-100 text-[10px] lg:text-xs mb-0 lg:mb-6 opacity-80 leading-tight transition-colors duration-300 ease-in-out">Pindai barang via kamera secara instan.</p>
                </div>
                
                <a href="{{ route('scan.index') }}" class="block p-3 lg:p-4 lg:w-full border border-white/20 rounded-xl bg-white/10 hover:bg-white dark:hover:bg-slate-800/20 transition-colors text-center group cursor-pointer mb-0 lg:mb-4 backdrop-blur-sm shrink-0">
                    <i data-lucide="camera" class="w-6 h-6 lg:w-8 h-8 text-white lg:mx-auto mb-0 lg:mb-2 opacity-80 group-hover:scale-110 transition-transform inline-block lg:block"></i>
                    <span class="font-bold tracking-widest text-[9px] lg:text-[10px] uppercase text-white hidden lg:block transition-colors duration-300 ease-in-out">Buka Kamera</span>
                </a>
            </div>
        </div>

        <!-- Aktivitas Terbaru -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 p-5 flex-1 shadow-sm dark:shadow-none transition-colors duration-300 ease-in-out">
            <div class="flex items-center justify-between mb-4 border-b border-slate-200 dark:border-slate-200 dark:border-slate-800 pb-3 transition-colors duration-300 ease-in-out">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">10 Transaksi Terakhir</h3>
                <a href="{{ route('laporan.index') }}" class="text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-[#79C0FF] transition-colors duration-300 ease-in-out">Lihat</a>
            </div>
            
            <div class="space-y-4">
                @forelse($aktivitas as $act)
                    <div class="flex gap-3 items-start group">
                        <div class="mt-0.5">
                            <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold rounded {{ $act->type == 'IN' ? 'bg-emerald-100 dark:bg-emerald-600 dark:bg-emerald-500/20 text-emerald-600 dark:text-[#3FB950] border border-emerald-200 dark:border-[#238636]/30' : ($act->type == 'OUT' ? 'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30' : 'bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30') }} transition-colors duration-300 ease-in-out">
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
                    <div class="text-center py-4 transition-colors duration-300 ease-in-out">
                        <p class="text-xs text-slate-400 dark:text-slate-500 transition-colors duration-300 ease-in-out">Belum ada aktivitas.</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData ?? ['labels' => [], 'masuk' => [], 'keluar' => []]);
        
        const options = {
            series: [
                { name: 'Barang Masuk', data: chartData.masuk },
                { name: 'Barang Keluar', data: chartData.keluar }
            ],
            chart: {
                parentHeightOffset: 0,
                type: 'area', // Changed to area
                height: '100%',
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'inherit'
            },
            colors: ['#3FB950', '#F43F5E'], // Vibrant Green and Rose
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { 
                curve: 'smooth', 
                width: 3 
            },
            markers: {
                size: 0,
                strokeColors: '#161B22',
                strokeWidth: 2,
                hover: { size: 6 }
            },
            xaxis: {
                categories: chartData.labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#8B949E', fontSize: '11px', fontWeight: 500 } },
                crosshairs: {
                    stroke: { color: '#30363D', width: 1, dashArray: 4 }
                }
            },
            yaxis: {
                labels: { style: { colors: '#8B949E', fontSize: '11px' } },
            },
            grid: {
                borderColor: document.documentElement.classList.contains('dark') ? '#30363D' : '#e2e8f0',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } },
                xaxis: { lines: { show: true } },
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
            },
            theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
            legend: {
                position: 'top',
                horizontalAlign: 'right', // Moved to right for a cleaner look
                offsetY: -20,
                labels: { colors: document.documentElement.classList.contains('dark') ? '#c9d1d9' : '#1e293b' },
                itemMargin: { horizontal: 10, vertical: 0 },
                markers: { offsetX: -2 }
            },
            tooltip: {
                theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                shared: true, // Show both IN and OUT values at once
                intersect: false,
                style: { fontSize: '12px' },
                y: { formatter: function (val) { return val + " Unit" } }
            }
        };

        const chart = new ApexCharts(document.querySelector("#transaction-chart"), options);
        chart.render().then(() => {
            // Auto scroll container to the right (latest month) on mobile
            const wrapper = document.getElementById('chart-scroll-wrapper');
            if (wrapper) {
                wrapper.scrollLeft = wrapper.scrollWidth;
            }
        });

        // Watch for theme changes on html tag to update chart colors dynamically
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === "class") {
                    const isDark = document.documentElement.classList.contains('dark');
                    chart.updateOptions({
                        theme: { mode: isDark ? 'dark' : 'light' },
                        tooltip: { theme: isDark ? 'dark' : 'light' },
                        grid: { borderColor: isDark ? '#30363D' : '#e2e8f0' },
                        legend: { labels: { colors: isDark ? '#c9d1d9' : '#1e293b' } }
                    });
                }
            });
        });
        observer.observe(document.documentElement, { attributes: true });
    });
</script>
@endpush
@endsection
