<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StockTransaction;
use Carbon\Carbon;

class DashboardChart extends Component
{
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->subDays(29)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $masukData = collect();
        $keluarData = collect();
        $labels = [];
        $masukArr = [];
        $keluarArr = [];
        $netArr = [];

        // Parsing dates securely
        $start = Carbon::parse($this->startDate)->startOfDay();
        $end = Carbon::parse($this->endDate)->endOfDay();

        // Fallback if user selects start > end
        if ($start->greaterThan($end)) {
            $temp = $start;
            $start = $end->startOfDay();
            $end = $temp->endOfDay();
        }

        $diffDays = $start->diffInDays($end) + 1; // inclusive

        // Auto-Scale Logic
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
                $labels[] = $date->translatedFormat('d M');
                $in = $masukData->get($dateStr, 0);
                $out = $keluarData->get($dateStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
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

            // Find all weeks between start and end
            $currentPeriod = $start->copy()->startOfWeek();
            while ($currentPeriod->lessThanOrEqualTo($end)) {
                $weekStr = $currentPeriod->format('oW'); 
                $labels[] = 'Mg ' . $currentPeriod->format('W, M Y');
                $in = $masukData->get($weekStr, 0);
                $out = $keluarData->get($weekStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
                
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
                $in = $masukData->get($monthStr, 0);
                $out = $keluarData->get($monthStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
                
                $currentPeriod->addMonth();
            }
        } else { // YEARLY (More than 5 years)
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
                $in = $masukData->get($yearStr, 0);
                $out = $keluarData->get($yearStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
                
                $currentPeriod->addYear();
            }
        }

        $chartData = [
            'labels' => $labels,
            'masuk' => $masukArr,
            'keluar' => $keluarArr,
            'net' => $netArr,
        ];

        $this->dispatch('updateChart', data: $chartData);

        return view('livewire.dashboard-chart', compact('chartData'));
    }
}
