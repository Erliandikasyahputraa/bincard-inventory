<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProdukIndex extends Component
{
    use WithPagination;

    public string $cari = '';

    public function hapus(int $id): void
    {
        $produk = Product::findOrFail($id);
        $produk->delete();
        $this->dispatch('sukses', 'Produk dihapus.');
    }

    public function render()
    {
        $query = Product::with('supplier')
            ->when($this->cari !== '', fn ($q) => $q->where('name', 'like', '%' . $this->cari . '%')
                ->orWhere('barcode', 'like', '%' . $this->cari . '%')
                ->orWhere('sku', 'like', '%' . $this->cari . '%'));
        $produk = $query->orderBy('name')->paginate(15);
        return view('livewire.produk-index', ['produk' => $produk])
            ->layout('layouts.app', ['title' => 'Data Produk']);
    }
}
