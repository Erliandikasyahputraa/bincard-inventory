<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Product;
use App\Models\StockTransaction;
use Carbon\Carbon;

use Livewire\Attributes\Title;

#[Title('Dashboard')]
class Dashboard extends Component
{
    public string $startDate = '';
    public string $endDate = '';
    public string $activeFilter = 'today';

    public function mount(): void
    {
        $this->applyFilter('today');
    }

    public function applyFilter(string $filter): void
    {
        $this->activeFilter = $filter;

        match ($filter) {
            'today'       => [$this->startDate, $this->endDate] = [
                                Carbon::today()->format('Y-m-d'),
                                Carbon::today()->format('Y-m-d'),
                             ],
            'last_7_days' => [$this->startDate, $this->endDate] = [
                                Carbon::today()->subDays(6)->format('Y-m-d'),
                                Carbon::today()->format('Y-m-d'),
                             ],
            'this_month'  => [$this->startDate, $this->endDate] = [
                                Carbon::now()->startOfMonth()->format('Y-m-d'),
                                Carbon::now()->endOfMonth()->format('Y-m-d'),
                             ],
            default       => null,
        };
    }

    public function updatedStartDate(): void
    {
        $this->activeFilter = 'custom';
    }

    public function updatedEndDate(): void
    {
        $this->activeFilter = 'custom';
    }

    public function render()
    {
        // Safely parse dates, falling back to today if empty
        $start = Carbon::parse($this->startDate ?: now())->startOfDay();
        $end   = Carbon::parse($this->endDate ?: now())->endOfDay();

        // Swap if inverted
        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        $stockService = app(\App\Services\StockService::class);

        $stats     = $stockService->getDashboardStats($start, $end);
        $chartData = $stockService->getDashboardChartData($start, $end);

        $aktivitas = StockTransaction::with(['product:id,name', 'user:id,name'])
                        ->whereBetween('created_at', [$start, $end])
                        ->latest()
                        ->take(15)
                        ->get();

        $this->dispatch('updateDashboardChart', data: $chartData);

        return view('livewire.dashboard', compact('stats', 'aktivitas', 'chartData'))
               ->title('Dashboard - BINGO')
               ->layout('layouts.app');
    }
}
