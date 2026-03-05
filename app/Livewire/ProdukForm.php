<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Supplier;
use Livewire\Component;

class ProdukForm extends Component
{
    public ?int $produkId = null;
    public string $barcode = '';
    public string $sku = '';
    public string $name = '';
    public int $min_stock = 0;
    public ?int $max_stock = null;
    public string $location = '';
    public ?int $supplier_id = null;

    public function mount(?int $id = null): void
    {
        if ($id !== null) {
            $p = Product::findOrFail($id);
            $this->produkId = $p->id;
            $this->barcode = $p->barcode;
            $this->sku = $p->sku ?? '';
            $this->name = $p->name;
            $this->min_stock = $p->min_stock;
            $this->max_stock = $p->max_stock;
            $this->location = $p->location ?? '';
            $this->supplier_id = $p->supplier_id;
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'barcode' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('products', 'barcode')->ignore($this->produkId)],
            'name' => 'required|string|max:255',
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);

        $data = [
            'barcode' => $this->barcode,
            'sku' => $this->sku ?: null,
            'name' => $this->name,
            'min_stock' => $this->min_stock,
            'max_stock' => $this->max_stock ?: null,
            'location' => $this->location ?: null,
            'supplier_id' => $this->supplier_id ?: null,
        ];
        if ($this->produkId !== null) {
            Product::findOrFail($this->produkId)->update($data);
            session()->flash('sukses', 'Produk diperbarui.');
        } else {
            $data['current_stock'] = 0;
            Product::create($data);
            session()->flash('sukses', 'Produk ditambahkan.');
        }
        $this->redirect(route('produk.index'), navigate: true);
    }

    public function render()
    {
        $pemasok = Supplier::orderBy('nama')->get();
        return view('livewire.produk-form', ['pemasok' => $pemasok])
            ->layout('layouts.app', ['title' => $this->produkId ? 'Edit Produk' : 'Tambah Produk']);
    }
}
