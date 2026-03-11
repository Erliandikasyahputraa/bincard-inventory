<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StockTransaction;
use Carbon\Carbon;

class DashboardChart extends Component
{
    public $filterType = 'daily';

    public function render()
    {
        $masukData = collect();
        $keluarData = collect();
        $labels = [];
        $masukArr = [];
        $keluarArr = [];
        $netArr = [];

        if ($this->filterType === 'daily') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')->pluck('total', 'date');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')->pluck('total', 'date');

            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays(29 - $i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->translatedFormat('d M');
                $in = $masukData->get($dateStr, 0);
                $out = $keluarData->get($dateStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
            }
        } elseif ($this->filterType === 'weekly') {
            $startDate = Carbon::now()->subWeeks(11)->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEARWEEK(created_at, 1) as week, SUM(quantity) as total')
                ->groupBy('week')->pluck('total', 'week');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEARWEEK(created_at, 1) as week, SUM(quantity) as total')
                ->groupBy('week')->pluck('total', 'week');

            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::now()->subWeeks(11 - $i);
                $weekStr = $date->format('oW'); 
                $labels[] = 'Mg ' . $date->format('W, M Y');
                $in = $masukData->get($weekStr, 0);
                $out = $keluarData->get($weekStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
            }
        } elseif ($this->filterType === 'monthly') {
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
                ->groupBy('month')->pluck('total', 'month');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
                ->groupBy('month')->pluck('total', 'month');

            for ($i = 0; $i < 12; $i++) {
                $date = Carbon::now()->subMonths(11 - $i);
                $monthStr = $date->format('Y-m');
                $labels[] = $date->translatedFormat('M Y');
                $in = $masukData->get($monthStr, 0);
                $out = $keluarData->get($monthStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
            }
        } elseif ($this->filterType === 'yearly') {
            $startDate = Carbon::now()->subYears(4)->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            
            $masukData = StockTransaction::where('type', 'IN')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEAR(created_at) as year, SUM(quantity) as total')
                ->groupBy('year')->pluck('total', 'year');
                
            $keluarData = StockTransaction::where('type', 'OUT')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('YEAR(created_at) as year, SUM(quantity) as total')
                ->groupBy('year')->pluck('total', 'year');

            for ($i = 0; $i < 5; $i++) {
                $date = Carbon::now()->subYears(4 - $i);
                $yearStr = $date->format('Y');
                $labels[] = $yearStr;
                $in = $masukData->get($yearStr, 0);
                $out = $keluarData->get($yearStr, 0);
                $masukArr[] = $in;
                $keluarArr[] = $out;
                $netArr[] = $in - $out;
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
