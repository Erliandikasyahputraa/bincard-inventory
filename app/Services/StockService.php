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
}
