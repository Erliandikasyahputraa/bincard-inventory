<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ScanBarcode extends Component
{
    public string $barcodeTerpilih = '';
    public ?array $produkDitemukan = null;

    public function mount(): void
    {
        if (request()->has('barcode')) {
            $this->barcodeTerpilih = request()->query('barcode');
            $this->cariProduk();
        }
    }

    public function updatedBarcodeTerpilih(): void
    {
        $this->cariProduk();
    }

    public function cariProduk(): void
    {
        $this->produkDitemukan = null;
        if ($this->barcodeTerpilih === '') {
            return;
        }
        $p = Product::where('barcode', trim($this->barcodeTerpilih))->first();
        if ($p) {
            $this->produkDitemukan = [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'current_stock' => $p->current_stock,
            ];
        }
    }

    /** Dipanggil dari Alpine.js saat scan berhasil (listener barcodeScanned). */
    public function setBarcodeDariScan(string $barcode): void
    {
        $this->barcodeTerpilih = $barcode;
        $this->cariProduk();
    }

    public function render()
    {
        return view('livewire.scan-barcode')
            ->layout('layouts.app', ['title' => 'Scan Barcode']);
    }
}
