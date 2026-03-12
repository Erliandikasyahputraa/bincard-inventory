<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockTransaction;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $startDate;
    public $endDate;
    
    // Quick Filter identifier
    public $activeFilter = 'this_month';

    public function mount()
    {
        $this->applyFilter('this_month');
    }

    public function applyFilter($filter)
    {
        $this->activeFilter = $filter;
        
        switch ($filter) {
            case 'today':
                $this->startDate = Carbon::today()->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'last_7_days':
                $this->startDate = Carbon::today()->subDays(6)->format('Y-m-d');
                $this->endDate = Carbon::today()->format('Y-m-d');
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'custom':
                // Do not auto-set dates, let the inputs dictate
                break;
        }
    }

    public function updatedStartDate()
    {
        $this->activeFilter = 'custom';
    }

    public function updatedEndDate()
    {
        $this->activeFilter = 'custom';
    }

    public function render()
    {
        // 1. Process Dates
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        if ($start->greaterThan($end)) {
            $temp = $start;
            $start = $end->startOfDay();
            $end = $temp->endOfDay();
        }

        // 2. Fetch Aggregated Statistics for Top Cards based on selected Date Range
        $stats = [
            'total_jenis' => Product::count(), // Physical reality doesn't change based on date filter (typically)
            'total_inventory' => Product::sum('current_stock') ?? 0,
            'low_stock' => Product::whereColumn('current_stock', '<=', 'min_stock')->count(),
            'masuk_range' => StockTransaction::where('type', 'IN')->whereBetween('created_at', [$start, $end])->sum('quantity') ?? 0,
            'keluar_range' => StockTransaction::where('type', 'OUT')->whereBetween('created_at', [$start, $end])->sum('quantity') ?? 0,
        ];

        // Global list dependencies
        $stok_kritis = Product::whereColumn('current_stock', '<=', 'min_stock')->take(5)->get();
        $aktivitas = StockTransaction::with(['product', 'user'])
                        ->whereBetween('created_at', [$start, $end])
                        ->latest()
                        ->take(10)
                        ->get();

        // 3. Prepare Chart Data (Time Series Grouping based on duration)
        $diffDays = $start->diffInDays($end) + 1;
        
        $masukData = collect();
        $keluarData = collect();
        $labels = [];
        $masukArr = [];
        $keluarArr = [];

        if ($diffDays <= 90) { // DAILY (Max 3 months)
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')->pluck('total', 'date');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')->pluck('total', 'date');

            for ($i = 0; $i < $diffDays; $i++) {
                $date = $start->copy()->addDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->translatedFormat('d M'); // Short date e.g., '04 Feb'
                $masukArr[] = $masukData->get($dateStr, 0);
                $keluarArr[] = $keluarData->get($dateStr, 0);
            }
        } elseif ($diffDays <= 365) { // WEEKLY (Max 1 year)
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEARWEEK(created_at, 1) as week, SUM(quantity) as total')
                ->groupBy('week')->pluck('total', 'week');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEARWEEK(created_at, 1) as week, SUM(quantity) as total')
                ->groupBy('week')->pluck('total', 'week');

            $currentPeriod = $start->copy()->startOfWeek();
            while ($currentPeriod->lessThanOrEqualTo($end)) {
                $weekStr = $currentPeriod->format('oW'); 
                $labels[] = 'Mg ' . $currentPeriod->format('W, M Y');
                $masukArr[] = $masukData->get($weekStr, 0);
                $keluarArr[] = $keluarData->get($weekStr, 0);
                $currentPeriod->addWeek();
            }
        } elseif ($diffDays <= 1825) { // MONTHLY (Max 5 years)
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
                ->groupBy('month')->pluck('total', 'month');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
                ->groupBy('month')->pluck('total', 'month');

            $currentPeriod = $start->copy()->startOfMonth();
            while ($currentPeriod->lessThanOrEqualTo($end)) {
                $monthStr = $currentPeriod->format('Y-m');
                $labels[] = $currentPeriod->translatedFormat('M Y');
                $masukArr[] = $masukData->get($monthStr, 0);
                $keluarArr[] = $keluarData->get($monthStr, 0);
                $currentPeriod->addMonth();
            }
        } else { // YEARLY
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as year, SUM(quantity) as total')
                ->groupBy('year')->pluck('total', 'year');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as year, SUM(quantity) as total')
                ->groupBy('year')->pluck('total', 'year');

            $currentPeriod = $start->copy()->startOfYear();
            while ($currentPeriod->lessThanOrEqualTo($end)) {
                $yearStr = $currentPeriod->format('Y');
                $labels[] = $yearStr;
                $masukArr[] = $masukData->get($yearStr, 0);
                $keluarArr[] = $keluarData->get($yearStr, 0);
                $currentPeriod->addYear();
            }
        }

        $chartData = [
            'labels' => $labels,
            'masuk' => $masukArr,
            'keluar' => $keluarArr,
        ];

        // Tell Echarts to render the new data structure via browser event
        $this->dispatch('updateDashboardChart', data: $chartData);

        // Calculate total net for the No-Data state illustration dynamically
        $hasData = collect($masukArr)->sum() > 0 || collect($keluarArr)->sum() > 0;

        return view('livewire.dashboard', compact('stats', 'stok_kritis', 'aktivitas', 'chartData', 'hasData'))
               ->title('Dashboard - BINGO')
               ->layout('layouts.app');
    }
}
