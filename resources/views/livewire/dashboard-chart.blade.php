<div>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h2 class="text-md lg:text-lg font-bold text-slate-800 dark:text-slate-200 transition-colors duration-300 ease-in-out">Grafik Transaksi Harian</h2>
        
        <!-- Time Filter -->
        <select wire:model.live="filterType" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-auto p-2 dark:bg-slate-800 dark:border-slate-700 dark:placeholder-slate-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option value="daily">Harian (30 Hari Terakhir)</option>
            <option value="weekly">Mingguan (12 Minggu Terakhir)</option>
            <option value="monthly">Bulanan (12 Bulan Terakhir)</option>
            <option value="yearly">Tahunan (5 Tahun Terakhir)</option>
        </select>
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

                    // Smart starting value definition prioritizing "Present Date" focus over historical mass
                    let frameSize = 12; // default
                    const filterDropdown = document.querySelector('select[wire\\:model\\.live="filterType"]');
                    if (filterDropdown) {
                        const t = filterDropdown.value;
                        if (t === 'daily') frameSize = 7;
                        else if (t === 'weekly') frameSize = 4;
                        else if (t === 'monthly') frameSize = 6;
                    }

                    const zoomStartValue = data.labels.length > frameSize ? data.labels.length - frameSize : 0;
                    const zoomEndValue = data.labels.length - 1;

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
                            right: '5%',
                            bottom: '12%',
                            containLabel: true
                        },
                        dataZoom: [
                            {
                                type: 'inside',
                                startValue: zoomStartValue,
                                endValue: zoomEndValue
                            },
                            {
                                type: 'slider',
                                startValue: zoomStartValue,
                                endValue: zoomEndValue,
                                bottom: 0,
                                height: 15,
                                borderColor: 'transparent',
                                backgroundColor: isDark ? '#21262D' : '#f1f5f9',
                                fillerColor: isDark ? 'rgba(56, 139, 253, 0.2)' : 'rgba(59, 130, 246, 0.2)',
                                handleStyle: { color: isDark ? '#8B949E' : '#94a3b8' },
                                textStyle: { color: textColor },
                                showDetail: false
                            }
                        ],
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
