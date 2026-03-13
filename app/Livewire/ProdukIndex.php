<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Title;

#[Title('Data Produk')]
class ProdukIndex extends Component
{
    use WithPagination;

    public string $cari = '';
    public string $sortBy = 'name_asc';

    public function hapus(int $id): void
    {
        $produk = Product::findOrFail($id);
        $produk->delete();
        $this->dispatch('sukses', 'Produk dihapus.');
    }

    public function render()
    {
        $query = Product::with('supplier')
            ->when($this->cari !== '', function ($q) {
                $q->where(function($subQ) {
                    $subQ->where('name', 'like', '%' . $this->cari . '%')
                         ->orWhere('barcode', 'like', '%' . $this->cari . '%')
                         ->orWhere('sku', 'like', '%' . $this->cari . '%');
                });
            });

        switch ($this->sortBy) {
            case 'rack_asc':
                $query->orderBy('location', 'asc');
                break;
            case 'stock_highest':
                $query->orderBy('current_stock', 'desc');
                break;
            case 'stock_critical':
                $query->orderByRaw('current_stock <= min_stock DESC')
                      ->orderBy('current_stock', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $produk = $query->paginate(15);
        return view('livewire.produk-index', ['produk' => $produk])
            ->layout('layouts.app', ['title' => 'Data Produk']);
    }
}
