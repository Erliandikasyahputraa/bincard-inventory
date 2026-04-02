<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Models\Product;
use App\Models\StockTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BinCardExport;

#[Title('Bin Card')]
class BinCardDetail extends Component
{
    public int $id;
    public string $startDate = '';
    public string $endDate = '';
    public string $activeFilter = 'this_month';

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->applyFilter('this_month');
    }

    public function applyFilter(string $filter): void
    {
        $this->activeFilter = $filter;
        match ($filter) {
            'this_week'   => [$this->startDate, $this->endDate] = [
                                Carbon::now()->startOfWeek()->format('Y-m-d'),
                                Carbon::now()->endOfWeek()->format('Y-m-d'),
                             ],
            'this_month'  => [$this->startDate, $this->endDate] = [
                                Carbon::now()->startOfMonth()->format('Y-m-d'),
                                Carbon::now()->endOfMonth()->format('Y-m-d'),
                             ],
            'last_3_months' => [$this->startDate, $this->endDate] = [
                                Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d'),
                                Carbon::now()->endOfMonth()->format('Y-m-d'),
                             ],
            'all'         => [$this->startDate, $this->endDate] = ['', ''],
            default       => null,
        };
    }

    public function updatedStartDate(): void { $this->activeFilter = 'custom'; }
    public function updatedEndDate(): void   { $this->activeFilter = 'custom'; }

    public function exportExcel()
    {
        $product = Product::findOrFail($this->id);
        $start = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : null;
        $end   = $this->endDate   ? Carbon::parse($this->endDate)->endOfDay()     : null;
        $filename = 'bin-card-' . str($product->name)->slug() . '-' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new BinCardExport($this->id, $start, $end), $filename);
    }

    public function render()
    {
        $product = Product::with('supplier')->findOrFail($this->id);

        $query = StockTransaction::with('user')
            ->where('product_id', $this->id)
            ->orderBy('created_at', 'asc');

        if ($this->startDate) {
            $query->where('created_at', '>=', Carbon::parse($this->startDate)->startOfDay());
        }
        if ($this->endDate) {
            $query->where('created_at', '<=', Carbon::parse($this->endDate)->endOfDay());
        }

        $rawTransactions = $query->get();

        // Hitung saldo berjalan (running balance)
        // Ambil stok awal: stok sebelum periode
        if ($this->startDate) {
            $stockBefore = StockTransaction::where('product_id', $this->id)
                ->where('created_at', '<', Carbon::parse($this->startDate)->startOfDay())
                ->sum('quantity');
            $runningBalance = $stockBefore;
        } else {
            $runningBalance = 0;
        }

        $transactions = $rawTransactions->map(function ($trx) use (&$runningBalance) {
            $runningBalance += $trx->quantity;
            return (object) [
                'id'         => $trx->id,
                'created_at' => $trx->created_at,
                'type'       => $trx->type,
                'reference'  => $trx->reference,
                'quantity'   => $trx->quantity,
                'balance'    => $runningBalance,
                'pic'        => $trx->user?->name ?? 'Sistem',
                'notes'      => $trx->notes,
            ];
        });

        // Ringkasan
        $totalMasuk  = $rawTransactions->where('quantity', '>', 0)->sum('quantity');
        $totalKeluar = abs($rawTransactions->where('quantity', '<', 0)->sum('quantity'));
        $lastActivity = $rawTransactions->last();

        // Status stok — habis cek tanpa syarat min_stock
        if ($product->current_stock <= 0) {
            $stockStatus = 'habis';
        } elseif ($product->min_stock && $product->current_stock <= $product->min_stock) {
            $stockStatus = 'kritis';
        } else {
            $stockStatus = 'aman';
        }

        return view('livewire.bin-card-detail', compact(
            'product', 'transactions', 'totalMasuk', 'totalKeluar', 'lastActivity', 'stockStatus'
        ))->layout('layouts.app');
    }
}
