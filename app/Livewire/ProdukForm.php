<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Edit Produk')]
class ProdukForm extends Component
{
    public ?int $produkId = null;
    public string $barcode = '';
    public string $sku = '';
    public string $name = '';
    public string $uom = 'PCS';
    public int $min_stock = 0;
    public ?int $max_stock = null;
    public string $location = '';
    public ?int $supplier_id = null;

    /** Daftar UoM yang tersedia — tidak perlu migration jika ada tambahan */
    public static function daftarUom(): array
    {
        return [
            'PCS' => 'PCS – Piece / Pieces',
            'SET' => 'SET – Set',
            'KLG' => 'KLG – Kaleng',
            'UN'  => 'UN – Unit',
            'KG'  => 'KG – Kilogram',
            'CM'  => 'CM – Centimeter',
            'BOX' => 'BOX – Box / Karton',
            'BTG' => 'BTG – Batang',
            'BTL' => 'BTL – Botol',
            'DUS' => 'DUS – Dus',
            'LBR' => 'LBR – Lembar',
            'MTR' => 'MTR – Meter',
            'TON' => 'TON – Ton',
            'SAK' => 'SAK – Sak',
            'CAN' => 'CAN – Can',
            'GLS' => 'GLS – Galon',
            'PKT' => 'PKT – Paket',
        ];
    }

    public function mount(?int $id = null): void
    {
        if ($id !== null) {
            $p = Product::findOrFail($id);
            $this->produkId    = $p->id;
            $this->barcode     = $p->barcode;
            $this->sku         = $p->sku ?? '';
            $this->name        = $p->name;
            $this->uom         = $p->uom ?? 'PCS';
            $this->min_stock   = $p->min_stock;
            $this->max_stock   = $p->max_stock;
            $this->location    = $p->location ?? '';
            $this->supplier_id = $p->supplier_id;
        }
    }

    public function simpan(): void
    {
        $this->validate([
            'barcode'   => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('products', 'barcode')->ignore($this->produkId)],
            'name'      => 'required|string|max:255',
            'uom'       => ['required', 'string', 'max:10', \Illuminate\Validation\Rule::in(array_keys(self::daftarUom()))],
            'min_stock' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
        ]);

        $data = [
            'barcode'     => $this->barcode,
            'sku'         => $this->sku ?: null,
            'name'        => $this->name,
            'uom'         => $this->uom,
            'min_stock'   => $this->min_stock,
            'max_stock'   => $this->max_stock ?: null,
            'location'    => $this->location ?: null,
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
        return view('livewire.produk-form', [
            'pemasok'   => $pemasok,
            'daftarUom' => self::daftarUom(),
        ])->layout('layouts.app', ['title' => $this->produkId ? 'Edit Produk' : 'Tambah Produk']);
    }
}
