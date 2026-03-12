<div>
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 mb-4">
        <h2 class="text-md lg:text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">Grafik Transaksi Harian</h2>
        
        <!-- Time Filter: Date Range -->
        <div class="flex items-center gap-2 w-full xl:w-auto overflow-x-auto no-scrollbar pb-1">
            <div class="relative w-full sm:w-36 flex-shrink-0">
                <input type="date" wire:model.live="startDate" class="px-3 py-1.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white" style="color-scheme: dark;">
            </div>
            <span class="text-slate-400 text-xs font-medium">s/d</span>
            <div class="relative w-full sm:w-36 flex-shrink-0">
                <input type="date" wire:model.live="endDate" class="px-3 py-1.5 w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 transition-all outline-none text-slate-900 dark:text-white" style="color-scheme: dark;">
            </div>
        </div>
    </div>
    
    <div class="flex-1 w-full relative min-h-[300px] overflow-hidden" wire:ignore>
        <div id="chart-scroll-wrapper" class="w-full h-full overflow-x-auto no-scrollbar scroll-smooth">
            <div id="transaction-chart" class="min-w-[800px] lg:min-w-full h-[350px]">
                <!-- Apache ECharts will render here -->
            </div>
        </div>
    </div>

    <!-- ECharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof echarts === 'undefined') return;

            let chartData = @json($chartData);
            let myChart = null;

            const initEcharts = () => {
                let chartDom = document.getElementById('transaction-chart');
                if (!chartDom) return;
                
                myChart = echarts.init(chartDom);
                renderChart(chartData);
            }

            const renderChart = (data) => {
                if (!myChart) return;
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#c9d1d9' : '#64748b';
                const gridColor = isDark ? '#30363D' : '#e2e8f0';

                    // Smart zoom calculation: show about latest 15 bars max initially
                    let frameSize = 15;
                    const totalBars = data.labels.length;
                    const zoomStartValue = totalBars > frameSize ? totalBars - frameSize : 0;
                    const zoomEndValue = totalBars - 1;

                    const option = {
                        tooltip: {
                            trigger: 'axis',
                            backgroundColor: isDark ? '#161B22' : '#ffffff',
                            borderColor: gridColor,
                            textStyle: { color: textColor },
                            axisPointer: { type: 'shadow' },
                            formatter: function (params) {
                                let masuk = 0;
                                let keluar = 0;
                                let res = `<b style="font-size: 13px;">${params[0].axisValue}</b><br/>`;
                                params.forEach(function (item) {
                                    res += `<div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                                                <span>${item.marker} ${item.seriesName}</span>
                                                <b style="margin-left:12px;">${item.value} Unit</b>
                                            </div>`;
                                    if (item.seriesName === 'Barang Masuk') masuk = item.value;
                                    if (item.seriesName === 'Barang Keluar') keluar = item.value;
                                });
                                let net = masuk - keluar;
                                let netColor = net > 0 ? '#3FB950' : (net < 0 ? '#F43F5E' : '#8B949E');
                                let statIcon = net > 0 ? '▲' : (net < 0 ? '▼' : '−');
                                res += `<div style="margin-top:8px; padding-top:8px; border-top:1px dashed ${gridColor}; display:flex; justify-content:space-between; align-items:center;">
                                            <span style="font-size: 11px; color: ${textColor}">Pertumbuhan Net :</span>
                                            <b style="color:${netColor}">${statIcon} ${Math.abs(net)}</b>
                                        </div>`;
                                return res;
                            }
                        },
                        legend: {
                            data: ['Barang Masuk', 'Barang Keluar'],
                            textStyle: { color: textColor }
                        },
                        grid: {
                            left: '2%',
                            right: '8%',
                            bottom: '12%',
                            containLabel: true
                        },
                    xAxis: {
                        type: 'category',
                        data: data.labels,
                        axisLabel: { 
                            color: textColor,
                            interval: 0,
                            rotate: data.labels.length > 10 ? 25 : 0 
                        },
                        axisLine: { lineStyle: { color: gridColor } }
                    },
                    yAxis: {
                        type: 'value',
                        axisLabel: { color: textColor, formatter: '{value}' },
                        splitLine: { lineStyle: { color: gridColor, type: 'dashed' } }
                    },
                    series: [
                        {
                            name: 'Barang Masuk',
                            type: 'bar',
                            itemStyle: { color: '#3FB950', borderRadius: [4, 4, 0, 0] },
                            data: data.masuk
                        },
                        {
                            name: 'Barang Keluar',
                            type: 'bar',
                            itemStyle: { color: '#F43F5E', borderRadius: [4, 4, 0, 0] },
                            data: data.keluar
                        }
                    ]
                };

                myChart.setOption(option, true);
            };

            // Wait specifically to clear AlpineJS and wire:navigate glitches
            setTimeout(() => {
                initEcharts();
            }, 300);

            // Resizing Support
            window.addEventListener('resize', function() {
                if (myChart) myChart.resize();
            });

            // Re-render chart explicitly whenever the Livewire component updates (e.g. filter changes)
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('updateChart', (event) => {
                    let newData = event.data;
                    if(Array.isArray(event) && event.length > 0) {
                        newData = event[0].data || event[0]; 
                    } else if(event.detail) {
                        newData = event.detail.data || event.detail[0]?.data || event.detail[0];
                    }
                    if(!myChart) {
                        initEcharts();
                    }
                    setTimeout(() => {
                        renderChart(newData);
                    }, 50);
                });
            });

            // Watch for theme changes locally
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "class" && myChart) {
                        renderChart(chartData); 
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        });
    </script>
</div>
