<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class OpnameIndex extends Component
{
    use WithPagination;

    public string $cari = '';
    public ?int $opnameId = null;
    public array $stokFisik = [];
    public string $tanggalBaru = '';
    public string $cariBarang = '';

    public function buatOpname(): void
    {
        $opname = StockOpname::create([
            'tanggal_opname' => $this->tanggalBaru ?: now()->toDateString(),
            'status' => StockOpname::STATUS_DRAFT,
            'created_by' => auth()->id(),
        ]);
        $produk = Product::orderBy('name')->get();
        foreach ($produk as $p) {
            StockOpnameDetail::create([
                'stock_opname_id' => $opname->id,
                'product_id' => $p->id,
                'stok_sistem' => $p->current_stock,
                'stok_fisik' => null,
                'selisih' => 0,
            ]);
        }
        $this->dispatch('transaksi-sukses', ['message' => 'Sesi opname dibuat.']);
        $this->opnameId = $opname->id;
        $this->tanggalBaru = '';
        $this->riwayatFilter = '';
        // No redirect needed, Livewire will re-render natively without breaking Sweetalert DOM
    }

    public function setStokFisik(int $productId, $value): void
    {
        $val = is_numeric($value) ? (int) $value : null;
        $this->stokFisik[$productId] = $val;
        $detail = StockOpnameDetail::where('stock_opname_id', $this->opnameId)->where('product_id', $productId)->first();
        if ($detail) {
            $detail->update([
                'stok_fisik' => $val,
                'selisih' => $val !== null ? $val - $detail->stok_sistem : 0,
            ]);
        }
    }

    public function rekonsiliasi(): void
    {
        $opname = StockOpname::with('details.product')->findOrFail($this->opnameId);
        if ($opname->status === StockOpname::STATUS_SELESAI) {
            $this->dispatch('transaksi-gagal', ['message' => 'Opname ini sudah direkonsiliasi.']);
            return;
        }
        foreach ($opname->details as $d) {
            $fisik = isset($this->stokFisik[$d->product_id]) && $this->stokFisik[$d->product_id] !== '' ? (int) $this->stokFisik[$d->product_id] : null;
            $d->update(['stok_fisik' => $fisik, 'selisih' => $fisik !== null ? $fisik - $d->stok_sistem : 0]);
        }
        $opname->refresh();
        $service = app(StockService::class);
        DB::transaction(function () use ($opname, $service) {
            foreach ($opname->details as $d) {
                if ($d->selisih != 0 && $d->stok_fisik !== null) {
                    $service->penyesuaianStok(
                        $d->product_id,
                        $d->selisih,
                        auth()->id(),
                        (string) $opname->id,
                        'Stock Opname ' . $opname->tanggal_opname->format('Y-m-d')
                    );
                }
            }
            $opname->update(['status' => StockOpname::STATUS_SELESAI, 'closed_at' => now()]);
        });
        $this->dispatch('transaksi-sukses', ['message' => 'Rekonsiliasi selesai.']);
        $this->opnameId = null;
        $this->redirect(route('opname.index'), navigate: true);
    }

    public function batalRekonsiliasi(int $id): void
    {
        $opname = StockOpname::with('details')->findOrFail($id);
        if ($opname->status !== StockOpname::STATUS_SELESAI) return;

        $service = app(StockService::class);
        DB::transaction(function () use ($opname, $service) {
            foreach ($opname->details as $d) {
                if ($d->selisih != 0) {
                    $service->penyesuaianStok(
                        $d->product_id,
                        -$d->selisih,
                        auth()->id(),
                        'Batal Opname #' . $opname->id,
                        'Pembatalan Opname'
                    );
                    $d->update(['stok_fisik' => null, 'selisih' => 0]);
                }
            }
            $opname->update(['status' => StockOpname::STATUS_DRAFT, 'closed_at' => null]);
        });

        $this->dispatch('transaksi-sukses', ['message' => 'Rekonsiliasi dibatalkan. Menunggu input ulang.']);
    }

    public function hapusSesi(int $id): void
    {
        $opname = StockOpname::findOrFail($id);
        if ($opname->status === StockOpname::STATUS_SELESAI) {
            $this->dispatch('transaksi-gagal', ['message' => 'Sesi yang sudah direkonsiliasi tidak dapat dihapus. Batalkan rekonsiliasi terlebih dahulu.']);
            return;
        }
        
        $opname->details()->delete();
        $opname->delete();
        
        $this->dispatch('transaksi-sukses', ['message' => 'Sesi opname berhasil dihapus.']);
    }

    public function mount(): void
    {
        $this->tanggalBaru = now()->toDateString();
        $this->opnameId = request()->query('opname') ? (int) request()->query('opname') : null;
        if ($this->opnameId) {
            $opname = StockOpname::with('details')->find($this->opnameId);
            if ($opname) {
                foreach ($opname->details as $d) {
                    $this->stokFisik[$d->product_id] = $d->stok_fisik ?? '';
                }
            }
        }
    }

    public string $historySearch = '';
    public string $historyDate = '';

    public function updatedHistorySearch()
    {
        $this->resetPage();
    }

    public function updatedHistoryDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $opname = $this->opnameId ? StockOpname::find($this->opnameId) : null;
        
        $details = [];
        if ($opname) {
            $query = StockOpnameDetail::with('product')->where('stock_opname_id', $this->opnameId);
            if ($this->cariBarang !== '') {
                $query->whereHas('product', function($q) {
                    $q->where('name', 'like', '%' . $this->cariBarang . '%')
                      ->orWhere('barcode', 'like', '%' . $this->cariBarang . '%');
                });
            }
            $details = $query->get();
        }
        
        $queryHistory = StockOpname::with('createdBy')->orderByDesc('tanggal_opname')->orderByDesc('id');
        
        if ($this->historyDate !== '') {
            $query->whereDate('tanggal_opname', $this->historyDate);
        }
        
        if ($this->historySearch !== '') {
            $query->whereHas('createdBy', function($q) {
                $q->where('name', 'like', '%' . $this->historySearch . '%');
            })->orWhere('id', 'like', '%' . $this->historySearch . '%');
        }

        $daftarOpname = $queryHistory->paginate(10);
        
        return view('livewire.opname-index', [
            'opname' => $opname,
            'details' => $details,
            'daftarOpname' => $daftarOpname,
        ])->layout('layouts.app', ['title' => 'Stock Opname']);
    }
}
