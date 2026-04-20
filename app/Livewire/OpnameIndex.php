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

    // Pagination detail
    public int $perPage = 50;

    // Filter & sort detail dalam sesi
    public string $detailSort = 'name_asc'; // name_asc|name_desc|barcode|location

    // Filter & sort riwayat
    public string $historySearch = '';
    public string $historyDate   = '';
    public string $historySort   = 'terbaru';  // terbaru|terlama
    public string $historyStatus = '';          // ''|draft|selesai

    public function buatOpname(): void
    {
        $opname = StockOpname::create([
            'tanggal_opname' => $this->tanggalBaru ?: now()->toDateString(),
            'status'         => StockOpname::STATUS_DRAFT,
            'created_by'     => auth()->id(),
        ]);

        $produk = Product::select('id', 'current_stock')->get();
        $now    = now();
        $details = [];

        foreach ($produk as $p) {
            $details[] = [
                'stock_opname_id' => $opname->id,
                'product_id'      => $p->id,
                'stok_sistem'     => $p->current_stock,
                'stok_fisik'      => null,
                'selisih'         => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }

        foreach (array_chunk($details, 500) as $chunk) {
            StockOpnameDetail::insert($chunk);
        }

        $this->dispatch('transaksi-sukses', ['message' => 'Sesi opname dibuat.']);
        $this->opnameId    = $opname->id;
        $this->tanggalBaru = '';
    }

    /** Simpan langsung ke DB — Alpine.js menangani nilai lokal */
    public function setStokFisik(int $productId, $value): void
    {
        $val    = is_numeric($value) ? (int) $value : null;
        $detail = StockOpnameDetail::where('stock_opname_id', $this->opnameId)
                    ->where('product_id', $productId)
                    ->first();

        if ($detail) {
            $detail->update([
                'stok_fisik' => $val,
                'selisih'    => $val !== null ? $val - $detail->stok_sistem : 0,
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

        $this->dispatch('transaksi-sukses', ['message' => 'Rekonsiliasi selesai. Lihat hasil di riwayat sesi.']);
        // Tampilkan hasil di halaman yang sama (jangan null-kan opnameId)
        // User bisa lihat selisih karena status sudah SELESAI
    }

    public function batalRekonsiliasi(int $id): void
    {
        $opname  = StockOpname::with('details')->findOrFail($id);
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
            $this->dispatch('transaksi-gagal', ['message' => 'Batalkan rekonsiliasi terlebih dahulu sebelum menghapus.']);
            return;
        }
        $opname->details()->delete();
        $opname->delete();
        $this->dispatch('transaksi-sukses', ['message' => 'Sesi opname berhasil dihapus.']);
    }

    public function lihatSesi(int $id): void
    {
        $this->opnameId   = $id;
        $this->cariBarang = '';
    }

    public function tutupSesi(): void
    {
        $this->opnameId   = null;
        $this->cariBarang = '';
    }

    public function mount(): void
    {
        $this->tanggalBaru = now()->toDateString();
        $this->opnameId    = request()->query('opname') ? (int) request()->query('opname') : null;

        if (request()->query('auto_create') == 1 && !$this->opnameId) {
            $this->buatOpname();
            if (request()->query('cari_barcode')) {
                $this->cariBarang = request()->query('cari_barcode');
            }
        }
    }

    public function updatedCariBarang(): void   { $this->resetPage(); }
    public function updatedDetailSort(): void    { $this->resetPage(); }
    public function updatedHistorySearch(): void { $this->resetPage(); }
    public function updatedHistoryDate(): void   { $this->resetPage(); }
    public function updatedHistorySort(): void   { $this->resetPage(); }
    public function updatedHistoryStatus(): void { $this->resetPage(); }

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

            // Sort detail
            match ($this->detailSort) {
                'name_desc' => $query->join('products', 'stock_opname_details.product_id', '=', 'products.id')
                                     ->orderByDesc('products.name')->select('stock_opname_details.*'),
                'barcode'   => $query->join('products as p2', 'stock_opname_details.product_id', '=', 'p2.id')
                                     ->orderBy('p2.barcode')->select('stock_opname_details.*'),
                'location'  => $query->join('products as p3', 'stock_opname_details.product_id', '=', 'p3.id')
                                     ->orderBy('p3.location')->select('stock_opname_details.*'),
                'selisih_besar' => $query->orderByRaw('ABS(selisih) DESC'),
                default     => $query->join('products as p0', 'stock_opname_details.product_id', '=', 'p0.id')
                                     ->orderBy('p0.name')->select('stock_opname_details.*'),
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

        $queryHistory->orderBy('tanggal_opname', $this->historySort === 'terlama' ? 'asc' : 'desc')
                     ->orderBy('id', $this->historySort === 'terlama' ? 'asc' : 'desc');

        $daftarOpname = $queryHistory->paginate(10);

        return view('livewire.opname-index', [
            'opname'       => $opname,
            'details'      => $details,
            'daftarOpname' => $daftarOpname,
        ])->layout('layouts.app', ['title' => 'Stock Opname']);
    }
}
