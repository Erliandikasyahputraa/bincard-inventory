@extends('layouts.app')
@section('title', 'Dashboard - BINGO')
@section('content')

<x-slot:header>
    <div class="flex flex-col">
        <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight transition-colors duration-300 ease-in-out">Overview</h1>
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5 transition-colors duration-300 ease-in-out">Status gudang dan statistik hari ini.</p>
    </div>
</x-slot:header>

<!-- Stats Row -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6 lg:mb-8">
    <!-- Total Inventory -->
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none min-w-0">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-slate-100 dark:bg-emerald-500/10 text-blue-600 dark:text-blue-400 transition-colors duration-300 ease-in-out shrink-0">
            <i data-lucide="package" stroke-width="2" class="w-4 h-4 sm:w-6 sm:h-6"></i>
        </div>
        <div class="w-full min-w-0">
            <p class="text-[9px] sm:text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5 sm:mb-1 whitespace-nowrap overflow-hidden text-ellipsis transition-colors duration-300 ease-in-out">Total Produk & Fisik</p>
            <div class="flex flex-col xl:flex-row xl:items-baseline xl:gap-2">
                <h3 class="text-lg sm:text-2xl font-bold text-slate-900 dark:text-white transition-colors duration-300 ease-in-out truncate">{{ number_format($stats['total_jenis'], 0, ',', '.') }} <span class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500 font-normal transition-colors duration-300 ease-in-out">Jenis</span></h3>
                <span class="hidden xl:inline text-slate-300 dark:text-slate-600 transition-colors duration-300 ease-in-out">&bull;</span>
                <h3 class="text-sm sm:text-lg font-bold text-blue-600 dark:text-blue-400 mt-0.5 xl:mt-0 transition-colors duration-300 ease-in-out truncate">{{ number_format($stats['total_inventory'], 0, ',', '.') }} <span class="text-[10px] sm:text-xs text-blue-600 dark:text-blue-400/70 font-normal transition-colors duration-300 ease-in-out">Fisik</span></h3>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-white dark:bg-slate-900 p-3 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200 dark:border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-none">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-500 dark:text-rose-400 transition-colors duration-300 ease-in-out">
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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Grafik Transaksi -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 lg:p-5 flex flex-col min-h-[350px] lg:min-h-[400px] shadow-sm dark:shadow-none transition-colors duration-300 ease-in-out">
        <livewire:dashboard-chart />
    </div>


    <!-- Right Column Layout -->
    <div class="flex flex-col gap-6">
        


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
    // Livewire will handle the chart script injection.
</script>
@endpush
@endsection
