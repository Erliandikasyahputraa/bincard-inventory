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
    public array $stokFisik = []; // [product_id => quantity]

    public function buatOpname(): void
    {
        $opname = StockOpname::create([
            'tanggal_opname' => now()->toDateString(),
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
        session()->flash('sukses', 'Sesi opname dibuat.');
        $this->opnameId = $opname->id;
        $this->redirect(route('opname.index') . '?opname=' . $opname->id, navigate: true);
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
            session()->flash('error', 'Opname ini sudah direkonsiliasi.');
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
        session()->flash('sukses', 'Rekonsiliasi selesai.');
        $this->opnameId = null;
        $this->redirect(route('opname.index'), navigate: true);
    }

    public function hapusSesi(int $id): void
    {
        $opname = StockOpname::findOrFail($id);
        
        // Opsional: jika ingin melarang penghapusan sesi yang 'selesai', bisa uncomment ini
        // if ($opname->status === StockOpname::STATUS_SELESAI) {
        //     session()->flash('error', 'Sesi yang sudah direkonsiliasi tidak dapat dihapus.');
        //     return;
        // }
        
        $opname->details()->delete();
        $opname->delete();
        
        session()->flash('sukses', 'Sesi opname berhasil dihapus.');
    }

    public function mount(): void
    {
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

    public function render()
    {
        $opname = $this->opnameId ? StockOpname::with('details.product')->find($this->opnameId) : null;
        $daftarOpname = StockOpname::with('createdBy')->orderByDesc('tanggal_opname')->paginate(10);
        return view('livewire.opname-index', [
            'opname' => $opname,
            'daftarOpname' => $daftarOpname,
        ])->layout('layouts.app', ['title' => 'Stock Opname']);
    }
}
