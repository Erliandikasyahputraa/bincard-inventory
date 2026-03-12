<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Catat transaksi stok (ledger). Semua perubahan stok HARUS lewat method ini.
     * IN/ADJUST positif = tambah stok; OUT/ADJUST negatif = kurangi stok.
     */
    public function catatTransaksiStok(
        int $productId,
        string $type,
        int $quantity,
        int $userId,
        ?string $referenceId = null,
        ?string $note = null,
        ?string $tanggal = null
    ): StockTransaction {
        $product = Product::findOrFail($productId);

        if ($type === StockTransaction::TIPE_OUT && $quantity > 0) {
            $quantity = -$quantity;
        }
        if ($quantity < 0) {
            $jumlahKurang = abs($quantity);
            if ($product->current_stock < $jumlahKurang) {
                throw new InsufficientStockException(
                    $product->current_stock,
                    $jumlahKurang
                );
            }
        }

        return DB::transaction(function () use ($productId, $type, $quantity, $userId, $referenceId, $note, $tanggal) {
            // Lock product to avoid race conditions when recording stock_before / stock_after
            $product = Product::lockForUpdate()->findOrFail($productId);
            
            $stockBefore = $product->current_stock;
            $stockAfter = $stockBefore + $quantity;

            $transaksi = new StockTransaction([
                'product_id' => $productId,
                'type' => $type,
                'quantity' => $quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_id' => $referenceId,
                'user_id' => $userId,
                'note' => $note,
            ]);
            
            if ($tanggal) {
                // Combine provided date with current time so it orders correctly if multiple on same day
                $transaksi->created_at = \Carbon\Carbon::parse($tanggal)->format('Y-m-d H:i:s');
                $transaksi->updated_at = \Carbon\Carbon::parse($tanggal)->format('Y-m-d H:i:s');
            }
            $transaksi->save();

            $product->current_stock = $stockAfter;
            $product->save();

            return $transaksi;
        });
    }

    /** Barang masuk: tambah stok (type IN, quantity positif). */
    public function barangMasuk(
        int $productId,
        int $jumlah,
        int $userId,
        ?string $referensiId = null,
        ?string $catatan = null,
        ?string $tanggal = null
    ): StockTransaction {
        return $this->catatTransaksiStok(
            $productId,
            StockTransaction::TIPE_IN,
            abs($jumlah),
            $userId,
            $referensiId,
            $catatan,
            $tanggal
        );
    }

    /** Barang keluar: kurangi stok (type OUT, quantity positif akan diubah jadi negatif di dalam). */
    public function barangKeluar(
        int $productId,
        int $jumlah,
        int $userId,
        ?string $referensiId = null,
        ?string $catatan = null,
        ?string $tanggal = null
    ): StockTransaction {
        return $this->catatTransaksiStok(
            $productId,
            StockTransaction::TIPE_OUT,
            $jumlah,
            $userId,
            $referensiId,
            $catatan,
            $tanggal
        );
    }

    /** Penyesuaian stok (opname/koreksi). quantity = selisih (boleh negatif). */
    public function penyesuaianStok(
        int $productId,
        int $selisih,
        int $userId,
        ?string $referensiId = null,
        ?string $catatan = null,
        ?string $tanggal = null
    ): StockTransaction {
        return $this->catatTransaksiStok(
            $productId,
            StockTransaction::TIPE_ADJUST,
            $selisih,
            $userId,
            $referensiId,
            $catatan,
            $tanggal
        );
    }

    /** 
     * Get Aggregated Dashboard Global Stats 
     */
    public function getDashboardStats(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        return [
            'total_jenis' => Product::count(), // Total unique product barcodes
            'total_inventory' => Product::sum('current_stock') ?? 0, // Sum of physical items
            'low_stock' => Product::whereColumn('current_stock', '<=', 'min_stock')->count(), // Requires reorder
            'masuk_range' => StockTransaction::where('type', StockTransaction::TIPE_IN)->whereBetween('created_at', [$start, $end])->sum('quantity') ?? 0,
            'keluar_range' => StockTransaction::where('type', StockTransaction::TIPE_OUT)->whereBetween('created_at', [$start, $end])->sum('quantity') ?? 0,
        ];
    }

    /** 
     * Get Formatted Timeline Chart Data representing In and Out movement 
     */
    public function getDashboardChartData(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $diffDays = $start->diffInDays($end) + 1;
        
        $masukData = collect();
        $keluarData = collect();
        $labels = [];
        $masukArr = [];
        $keluarArr = [];

        if ($diffDays <= 90) { // DAILY (Max 3 months)
            $masukData = StockTransaction::where('type', StockTransaction::TIPE_IN)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')->pluck('total', 'date');
                
            $keluarData = StockTransaction::where('type', StockTransaction::TIPE_OUT)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
                ->groupBy('date')->pluck('total', 'date');

            for ($i = 0; $i < $diffDays; $i++) {
                $date = $start->copy()->addDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->translatedFormat('d M'); 
                $masukArr[] = $masukData->get($dateStr, 0);
                $keluarArr[] = $keluarData->get($dateStr, 0);
            }
        } elseif ($diffDays <= 365) { // WEEKLY (Max 1 year)
            $masukData = StockTransaction::where('type', StockTransaction::TIPE_IN)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEARWEEK(created_at, 1) as week, SUM(quantity) as total')
                ->groupBy('week')->pluck('total', 'week');
                
            $keluarData = StockTransaction::where('type', StockTransaction::TIPE_OUT)
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
            $masukData = StockTransaction::where('type', StockTransaction::TIPE_IN)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(quantity) as total')
                ->groupBy('month')->pluck('total', 'month');
                
            $keluarData = StockTransaction::where('type', StockTransaction::TIPE_OUT)
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
            $masukData = StockTransaction::where('type', StockTransaction::TIPE_IN)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('YEAR(created_at) as year, SUM(quantity) as total')
                ->groupBy('year')->pluck('total', 'year');
                
            $keluarData = StockTransaction::where('type', StockTransaction::TIPE_OUT)
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

        return [
            'labels' => $labels,
            'masuk' => $masukArr,
            'keluar' => $keluarArr,
        ];
    }
}
