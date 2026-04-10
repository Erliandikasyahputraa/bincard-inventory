<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\StockService;
use Livewire\Component;

use Livewire\Attributes\Title;

#[Title('Barang Masuk')]
class BarangMasukForm extends Component
{
    public ?int $product_id = null;
    public string $barcodeTerpilih = '';
    public array $hasilPencarian = [];
    public int $jumlah = 1;
    public string $tanggal = '';
    public string $referensi = '';
    public string $catatan = '';

    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d\TH:i');
        if (request()->has('product_id')) {
            $this->product_id = (int) request()->query('product_id');
        }
    }

    public function pilihProdukDariBarcode(): void
    {
        $this->hasilPencarian = [];
        if ($this->barcodeTerpilih === '') {
            return;
        }

        $p = Product::where('barcode', $this->barcodeTerpilih)->first();
        if ($p) {
            $this->pilihProduk($p->id);
            return;
        }

        $matches = Product::where('name', 'like', '%' . $this->barcodeTerpilih . '%')->get();
        if ($matches->count() === 1) {
            $this->pilihProduk($matches->first()->id);
        } elseif ($matches->count() > 1) {
            $this->hasilPencarian = $matches->toArray();
        } else {
            $this->dispatch('transaksi-gagal', ['message' => 'Barang tidak ditemukan.']);
        }
    }

    public function pilihProduk(int $id): void
    {
        $p = Product::find($id);
        if ($p) {
            $this->product_id = $p->id;
            $this->barcodeTerpilih = $p->barcode;
            $this->hasilPencarian = [];
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
        ]);
        $service = app(StockService::class);
        $service->barangMasuk(
            $this->product_id,
            $this->jumlah,
            auth()->id(),
            $this->referensi ?: null,
            $this->catatan ?: null,
            $this->tanggal
        );

        $this->dispatch('transaksi-sukses', ['message' => 'Barang masuk berhasil dicatat.']);
        $this->reset(['product_id', 'barcodeTerpilih', 'hasilPencarian', 'jumlah', 'referensi', 'catatan']);
        $this->tanggal = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        $produkDipilih = $this->product_id ? Product::find($this->product_id) : null;
        return view('livewire.barang-masuk-form', ['produkDipilih' => $produkDipilih])
            ->layout('layouts.app', ['title' => 'Barang Masuk']);
    }
}
