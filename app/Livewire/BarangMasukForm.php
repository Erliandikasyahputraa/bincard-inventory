<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\StockService;
use Livewire\Component;

class BarangMasukForm extends Component
{
    public ?int $product_id = null;
    public string $barcodeTerpilih = '';
    public int $jumlah = 1;
    public string $referensi = '';
    public string $catatan = '';

    public function mount(): void
    {
        if (request()->has('product_id')) {
            $this->product_id = (int) request()->query('product_id');
        }
    }

    public function pilihProdukDariBarcode(): void
    {
        if ($this->barcodeTerpilih === '') {
            return;
        }
        $p = Product::where('barcode', $this->barcodeTerpilih)->first();
        if ($p) {
            $this->product_id = $p->id;
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
        ]);
        $service = app(StockService::class);
        $service->barangMasuk(
            $this->product_id,
            $this->jumlah,
            auth()->id(),
            $this->referensi ?: null,
            $this->catatan ?: null
        );

        $this->dispatch('transaksi-sukses', message: 'Barang masuk berhasil dicatat.');
        $this->reset(['product_id', 'barcodeTerpilih', 'jumlah', 'referensi', 'catatan']);
    }

    public function render()
    {
        $produk = Product::orderBy('name')->get();
        return view('livewire.barang-masuk-form', ['daftarProduk' => $produk])
            ->layout('layouts.app', ['title' => 'Barang Masuk']);
    }
}
