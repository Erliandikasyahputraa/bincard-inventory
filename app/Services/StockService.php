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
        ?string $note = null
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

        return DB::transaction(function () use ($productId, $type, $quantity, $userId, $referenceId, $note) {
            $transaksi = StockTransaction::create([
                'product_id' => $productId,
                'type' => $type,
                'quantity' => $quantity,
                'reference_id' => $referenceId,
                'user_id' => $userId,
                'note' => $note,
            ]);

            $product = Product::lockForUpdate()->findOrFail($productId);
            $product->increment('current_stock', $quantity);

            return $transaksi;
        });
    }

    /** Barang masuk: tambah stok (type IN, quantity positif). */
    public function barangMasuk(
        int $productId,
        int $jumlah,
        int $userId,
        ?string $referensiId = null,
        ?string $catatan = null
    ): StockTransaction {
        return $this->catatTransaksiStok(
            $productId,
            StockTransaction::TIPE_IN,
            abs($jumlah),
            $userId,
            $referensiId,
            $catatan
        );
    }

    /** Barang keluar: kurangi stok (type OUT, quantity positif akan diubah jadi negatif di dalam). */
    public function barangKeluar(
        int $productId,
        int $jumlah,
        int $userId,
        ?string $referensiId = null,
        ?string $catatan = null
    ): StockTransaction {
        return $this->catatTransaksiStok(
            $productId,
            StockTransaction::TIPE_OUT,
            $jumlah,
            $userId,
            $referensiId,
            $catatan
        );
    }

    /** Penyesuaian stok (opname/koreksi). quantity = selisih (boleh negatif). */
    public function penyesuaianStok(
        int $productId,
        int $selisih,
        int $userId,
        ?string $referensiId = null,
        ?string $catatan = null
    ): StockTransaction {
        return $this->catatTransaksiStok(
            $productId,
            StockTransaction::TIPE_ADJUST,
            $selisih,
            $userId,
            $referensiId,
            $catatan
        );
    }
}
