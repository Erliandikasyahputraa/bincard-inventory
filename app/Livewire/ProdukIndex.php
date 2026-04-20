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

    public string $cari       = '';
    public string $sortField  = 'newest'; // newest|name|location|stock|status
    public string $sortDir    = 'desc';   // asc|desc
    public string $filterStatus = '';     // ''|kritis|habis
    public string $filterAisle  = '';     // ''|A|B|C...
    // Removed filterRak for simplification per user request

    public array $selectedIds = [];
    public bool  $selectAll   = false;

    /** Toggle sort: klik field sama → balik dir; klik field baru → asc (kecuali 'newest'→desc) */
    public function toggleSort(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            // newest default desc, yang lain default asc
            $this->sortDir = $field === 'newest' ? 'desc' : 'asc';
        }
        $this->resetPage();
    }

    public function setFilter(string $status): void
    {
        $this->filterStatus = $this->filterStatus === $status ? '' : $status;
        $this->resetPage();
    }

    public function hapus(int $id): void
    {
        Product::findOrFail($id)->delete();
        $this->dispatch('sukses', 'Produk dihapus.');
    }

    public function mount(): void
    {
        if (request()->query('filter') === 'kritis') {
            $this->filterStatus = 'kritis';
        } elseif (request()->query('filter') === 'habis') {
            $this->filterStatus = 'habis';
        }
    }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value
            ? $this->getCurrentQuery()->pluck('id')->map(fn($id) => (int)$id)->all()
            : [];
    }

    public function updatedCari(): void    { $this->resetPage(); }
    
    public function updatedFilterAisle(): void {
        $this->resetPage();
    }
    // Removed updatedFilterRak for simplification

    public function hapusTerpilih(array $ids = []): void
    {
        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) return;

        Product::whereIn('id', $ids)->delete();
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('sukses', count($ids) . ' produk berhasil dihapus.');
    }

    private function getCurrentQuery()
    {
        $query = Product::query();

        $query->when($this->cari !== '', fn($q) =>
            $q->where(fn($s) =>
                $s->where('name', 'like', '%' . $this->cari . '%')
                  ->orWhere('barcode', 'like', '%' . $this->cari . '%')
                  ->orWhere('sku', 'like', '%' . $this->cari . '%')
            )
        );

        if ($this->filterStatus === 'habis') {
            $query->where('current_stock', '=', 0);
        } elseif ($this->filterStatus === 'kritis') {
            $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0);
        }

        if ($this->filterAisle !== '') {
            $query->where('loc_aisle', $this->filterAisle);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->getCurrentQuery();

        match ($this->sortField) {
            'name'     => $query->orderBy('name', $this->sortDir),
            'location' => $query->orderByRaw("loc_aisle = '---' ASC")
                                ->orderByRaw("IF(loc_aisle REGEXP '^[a-zA-Z]', 0, 1) ASC")
                                ->orderBy('loc_aisle', $this->sortDir)
                                ->orderByRaw("IF(loc_floor REGEXP '^[a-zA-Z]', 0, 1) ASC")
                                ->orderByRaw("LENGTH(loc_floor) " . $this->sortDir)
                                ->orderBy('loc_floor', $this->sortDir)
                                ->orderByRaw("IF(loc_row REGEXP '^[a-zA-Z]', 0, 1) ASC")
                                ->orderByRaw("LENGTH(loc_row) " . $this->sortDir)
                                ->orderBy('loc_row', $this->sortDir)
                                ->orderByRaw("IF(loc_col REGEXP '^[a-zA-Z]', 0, 1) ASC")
                                ->orderByRaw("LENGTH(loc_col) " . $this->sortDir)
                                ->orderBy('loc_col', $this->sortDir),
            'stock'    => $query->orderBy('current_stock', $this->sortDir),
            'newest'   => $query->orderBy('id', $this->sortDir),
            default    => $query->orderBy('id', 'desc'),
        };

        $produk = $query->paginate(15);

        // List Lorong for filters
        $aisles = Product::whereNotNull('loc_aisle')
            ->where('loc_aisle', '!=', '---')
            ->where('loc_aisle', '!=', '')
            ->distinct()
            // Alphabetical Priority: Letters first
            ->orderByRaw("IF(loc_aisle REGEXP '^[a-zA-Z]', 0, 1) ASC")
            ->orderBy('loc_aisle')
            ->pluck('loc_aisle');

        return view('livewire.produk-index', [
                'produk' => $produk,
                'aisles' => $aisles
            ])
            ->layout('layouts.app', ['title' => 'Data Produk']);
    }
}
