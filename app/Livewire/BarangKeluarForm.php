<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SuratJalan;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BarangKeluarForm extends Component
{
    public ?int $product_id = null;
    public string $barcodeTerpilih = '';
    public int $jumlah = 1;
    public ?int $customer_id = null;
    public string $catatan = '';

    public function mount(): void
    {
        if (request()->has('product_id')) {
            $this->product_id = (int) request()->query('product_id');
        }
    }

    public function pilihProdukDariBarcode(): void
    {
        if ($this->barcodeTerpilih === '') {
            return;
        }
        $p = Product::where('barcode', $this->barcodeTerpilih)->first();
        if ($p) {
            $this->product_id = $p->id;
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1',
        ]);
        $service = app(StockService::class);
        try {
            $nomor = 'SJ-' . now()->format('Ymd') . '-' . str_pad((string) (SuratJalan::whereDate('tanggal', today())->count() + 1), 4, '0', STR_PAD_LEFT);
            $sjId = null;
            DB::transaction(function () use ($service, $nomor, &$sjId) {
            $sj = SuratJalan::create([
                'nomor_surat_jalan' => $nomor,
                'customer_id' => $this->customer_id ?: null,
                'tanggal' => now()->toDateString(),
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
                $this->catatan ?: null
            );
            });
        } catch (\App\Exceptions\InsufficientStockException $e) {
            $this->dispatch('transaksi-gagal', message: $e->getMessage());
            return;
        }

        $this->dispatch('transaksi-sukses', message: 'Barang keluar berhasil dicatat.', sjId: $sjId);
        $this->reset(['product_id', 'barcodeTerpilih', 'jumlah', 'customer_id', 'catatan']);
    }

    public function render()
    {
        $daftarProduk = Product::orderBy('name')->get();
        $pelanggan = Customer::orderBy('nama')->get();
        return view('livewire.barang-keluar-form', ['daftarProduk' => $daftarProduk, 'pelanggan' => $pelanggan])
            ->layout('layouts.app', ['title' => 'Barang Keluar']);
    }
}
