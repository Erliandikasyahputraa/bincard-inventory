<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SuratJalan;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

use Livewire\Attributes\Title;

#[Title('Barang Keluar')]
class BarangKeluarForm extends Component
{
    public ?int $product_id = null;
    public string $barcodeTerpilih = '';
    public array $hasilPencarian = [];
    public int $jumlah = 1;
    public string $tanggal = '';
    public ?int $customer_id = null;
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
        try {
            $nomor = 'SJ-' . now()->format('Ymd') . '-' . str_pad((string) (SuratJalan::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
            $sjId = null;
            DB::transaction(function () use ($service, $nomor, &$sjId) {
            $sj = SuratJalan::create([
                'nomor_surat_jalan' => $nomor,
                'customer_id' => $this->customer_id ?: null,
                'tanggal' => \Carbon\Carbon::parse($this->tanggal)->toDateString(),
                'status' => 'selesai',
                'created_by' => auth()->id(),
            ]);
            $sjId = $sj->id;
            $sj->details()->create(['product_id' => $this->product_id, 'quantity' => $this->jumlah]);
            $service->barangKeluar(
                $this->product_id,
                $this->jumlah,
                auth()->id(),
                (string) $sj->id,
                $this->catatan ?: null,
                $this->tanggal
            );
            });
        } catch (\App\Exceptions\InsufficientStockException $e) {
            $this->dispatch('transaksi-gagal', message: $e->getMessage());
            return;
        }

        $this->dispatch('transaksi-sukses', ['message' => 'Barang keluar berhasil dicatat.', 'sjId' => $sjId]);
        $this->reset(['product_id', 'barcodeTerpilih', 'hasilPencarian', 'jumlah', 'customer_id', 'catatan']);
        $this->tanggal = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        $daftarProduk = Product::orderBy('name')->get();
        $pelanggan = Customer::orderBy('nama')->get();
        return view('livewire.barang-keluar-form', ['daftarProduk' => $daftarProduk, 'pelanggan' => $pelanggan])
            ->layout('layouts.app', ['title' => 'Barang Keluar']);
    }
}
