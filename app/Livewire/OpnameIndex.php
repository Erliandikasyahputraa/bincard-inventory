<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

#[Title('Stock Opname')]
class OpnameIndex extends Component
{
    use WithPagination;

    public string $cari       = '';
    public ?int $opnameId     = null;
    public string $tanggalBaru = '';
    public string $cariBarang  = '';
    public string $filterAisle = '';

    // Removed filterRak per user request
    public function updatedFilterAisle(): void {
        $this->resetPage();
    }

    public function render()
    {
        $opname  = $this->opnameId ? StockOpname::find($this->opnameId) : null;
        $details = collect();

        if ($opname) {
            $query = StockOpnameDetail::with('product')
                ->where('stock_opname_id', $this->opnameId);

            if ($this->cariBarang !== '') {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->cariBarang . '%')
                      ->orWhere('barcode', 'like', '%' . $this->cariBarang . '%')
                      ->orWhere('location', 'like', '%' . $this->cariBarang . '%');
                });
            }

            if ($this->filterAisle !== '') {
                $query->whereHas('product', fn($q) => $q->where('loc_aisle', $this->filterAisle));
            }

            // Removed filterRak search block

            // Sort detail berdasarkan field + dir
            $dir = $this->detailSortDir;
            match ($this->detailSortField) {
                'barcode'  => $query->join('products as pb', 'stock_opname_details.product_id', '=', 'pb.id')
                                     ->orderBy('pb.barcode', $dir)->select('stock_opname_details.*'),
                'location' => $query->join('products as pl', 'stock_opname_details.product_id', '=', 'pl.id')
                                     ->orderByRaw("pl.loc_aisle = '---' ASC")
                                     ->orderBy('pl.loc_aisle', $dir)
                                     ->orderByRaw("LENGTH(pl.loc_floor) " . $dir)
                                     ->orderBy('pl.loc_floor', $dir)
                                     ->orderByRaw("LENGTH(pl.loc_row) " . $dir)
                                     ->orderBy('pl.loc_row', $dir)
                                     ->orderByRaw("LENGTH(pl.loc_col) " . $dir)
                                     ->orderBy('pl.loc_col', $dir)
                                     ->select('stock_opname_details.*'),
                'selisih'  => $query->orderByRaw('ABS(selisih) ' . ($dir === 'asc' ? 'ASC' : 'DESC')),
                default    => $query->join('products as pn', 'stock_opname_details.product_id', '=', 'pn.id')
                                     ->orderBy('pn.name', $dir)->select('stock_opname_details.*'),
            };

            $details = $query->paginate($this->perPage, ['*'], 'detail_page');
        }

        // Riwayat
        $queryHistory = StockOpname::with('createdBy');

        if ($this->historyDate !== '') {
            $queryHistory->whereDate('tanggal_opname', $this->historyDate);
        }
        if ($this->historyStatus !== '') {
            $queryHistory->where('status', $this->historyStatus);
        }
        if ($this->historySearch !== '') {
            $queryHistory->where(function ($q) {
                $q->whereHas('createdBy', fn($s) => $s->where('name', 'like', '%' . $this->historySearch . '%'))
                  ->orWhere('id', 'like', '%' . $this->historySearch . '%');
            });
        }

        $queryHistory->orderBy('tanggal_opname', $this->historySortDir)
                     ->orderBy('id', $this->historySortDir);

        $daftarOpname = $queryHistory->paginate(10);

        // List Lorong for filters within the session
        $aisles = collect();
        if($opname) {
            $aisles = Product::whereNotNull('loc_aisle')
                ->where('loc_aisle', '!=', '---')
                ->where('loc_aisle', '!=', '')
                ->distinct()
                ->orderBy('loc_aisle')
                ->pluck('loc_aisle');
        }

        return view('livewire.opname-index', [
            'opname'       => $opname,
            'details'      => $details,
            'daftarOpname' => $daftarOpname,
            'aisles'       => $aisles,
        ])->layout('layouts.app', ['title' => 'Stock Opname']);
    }
}
