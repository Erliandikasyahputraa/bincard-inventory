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
    public string $sortBy = 'newest';
    public array $selectedIds = [];
    public bool $selectAll = false;

    public function hapus(int $id): void
    {
        $produk = Product::findOrFail($id);
        $produk->delete();
        $this->dispatch('sukses', 'Produk dihapus.');
    }

    public function mount(): void
    {
        if (request()->query('filter') === 'kritis') {
            $this->sortBy = 'filter_kritis';
        } elseif (request()->query('filter') === 'habis') {
            $this->sortBy = 'filter_habis';
        }
    }

    public function updatedSelectAll(bool $value): void
    {
        if (!$value) {
            $this->selectedIds = [];
            return;
        }

        $this->selectedIds = $this->getCurrentQuery()->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }



    public function hapusTerpilih(array $ids = []): void
    {
        $ids = array_filter(array_map('intval', $ids));
        if (count($ids) === 0) {
            return;
        }

        Product::whereIn('id', $ids)->delete();
        $deleted = count($ids);
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('sukses', "{$deleted} produk berhasil dihapus.");
    }

    private function getCurrentQuery()
    {
        $query = Product::query();

        $query->when($this->cari !== '', function ($q) {
            $q->where(function ($subQ) {
                $subQ->where('name', 'like', '%' . $this->cari . '%')
                    ->orWhere('barcode', 'like', '%' . $this->cari . '%')
                    ->orWhere('sku', 'like', '%' . $this->cari . '%');
            });
        });

        if ($this->sortBy === 'filter_habis') {
            $query->where('current_stock', '=', 0);
        } elseif ($this->sortBy === 'filter_kritis') {
            $query->whereColumn('current_stock', '<=', 'min_stock')
                  ->where('current_stock', '>', 0);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->getCurrentQuery();

        switch ($this->sortBy) {
            case 'newest':
                $query->orderBy('id', 'desc');
                break;
            case 'rack_asc':
                $query->orderBy('location', 'asc');
                break;
            case 'stock_highest':
                $query->orderBy('current_stock', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'filter_habis':
            case 'filter_kritis':
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
