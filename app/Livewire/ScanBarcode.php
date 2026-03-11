<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ScanBarcode extends Component
{
    public string $barcodeTerpilih = '';
    public ?array $produkDitemukan = null;
    public array $hasilPencarian = [];

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
        $this->hasilPencarian = [];
        if ($this->barcodeTerpilih === '') {
            return;
        }
        $term = trim($this->barcodeTerpilih);
        $p = Product::where('barcode', $term)->first();
        if ($p) {
            $this->pilihProduk($p->id);
            return;
        }
        
        $matches = Product::where('name', 'like', '%' . $term . '%')->get();
        if ($matches->count() === 1) {
            $this->pilihProduk($matches->first()->id);
        } elseif ($matches->count() > 1) {
            $this->hasilPencarian = $matches->toArray();
        }
    }

    public function pilihProduk(int $id): void
    {
        $p = Product::find($id);
        if ($p) {
            $this->produkDitemukan = [
                'id' => $p->id,
                'name' => $p->name,
                'barcode' => $p->barcode,
                'current_stock' => $p->current_stock,
            ];
            $this->barcodeTerpilih = $p->barcode;
            $this->hasilPencarian = [];
        }
    }

    public function pilihPertama(): void
    {
        if (count($this->hasilPencarian) > 0) {
            $this->pilihProduk($this->hasilPencarian[0]['id']);
        } else {
            $this->cariProduk();
            if (count($this->hasilPencarian) > 0) {
                $this->pilihProduk($this->hasilPencarian[0]['id']);
            }
        }
    }

    public function setBarcodeDariScan(string $barcode): void
    {
        $this->barcodeTerpilih = $barcode;
        $this->cariProduk();
    }

    public function resetScan(): void
    {
        $this->barcodeTerpilih = '';
        $this->produkDitemukan = null;
        $this->hasilPencarian = [];
        $this->dispatch('scan-reset');
    }

    public function render()
    {
        return view('livewire.scan-barcode')
            ->layout('layouts.app', ['title' => 'Scan Barcode']);
    }
}
