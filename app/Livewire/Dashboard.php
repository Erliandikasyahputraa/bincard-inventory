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
    public $activeFilter = 'today';

    public function mount()
    {
        $this->applyFilter('today');
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
        // Process Dates securely
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        if ($start->greaterThan($end)) {
            $temp = $start;
            $start = $end->startOfDay();
            $end = $temp->endOfDay();
        }

        // Fetch analytical statistics logically separated into StockService
        $stockService = app(\App\Services\StockService::class);
        
        $stats = $stockService->getDashboardStats($start, $end);
        $chartData = $stockService->getDashboardChartData($start, $end);

        // Global lists for widgets
        $stok_kritis = Product::whereColumn('current_stock', '<=', 'min_stock')->take(5)->get();
        
        // Use eager loading to prevent N+1 Queries on relational names
        $aktivitas = StockTransaction::with(['product:id,name', 'user:id,name'])
                        ->whereBetween('created_at', [$start, $end])
                        ->latest()
                        ->take(10)
                        ->get();

        // 3. Prepare Chart Data Event Dispatching (Compatible with Livewire 3 arrays)
        $this->dispatch('updateDashboardChart', data: $chartData);

        // Calculate total net for the No-Data state illustration dynamically
        $hasData = collect($chartData['masuk'])->sum() > 0 || collect($chartData['keluar'])->sum() > 0;

        return view('livewire.dashboard', compact('stats', 'stok_kritis', 'aktivitas', 'chartData', 'hasData'))
               ->title('Dashboard - BINGO')
               ->layout('layouts.app');
    }
}
