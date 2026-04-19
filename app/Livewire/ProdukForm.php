<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Storage;

#[Title('Edit Produk')]
class ProdukForm extends Component
{
    use WithFileUploads;

    public ?int $produkId = null;
    public string $barcode = '';
    public string $sku = '';
    public string $name = '';
    public string $uom = 'PCS';
    public int $min_stock = 0;
    public ?int $max_stock = null;
    public string $location = '';
    public ?int $supplier_id = null;
    
    public $image;
    public ?string $existingImagePath = null;

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
            $this->existingImagePath = $p->image_path;
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
            'image'     => 'nullable|image|max:5120',
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

        if ($this->image) {
            $manager = new ImageManager(new Driver());
            $img = $manager->decodePath($this->image->getRealPath());
            $img->scaleDown(width: 400);

            $encoded = $img->encode(new WebpEncoder(quality: 80));

            $filename = 'products/' . uniqid() . '.webp';
            Storage::disk('public')->makeDirectory('products');
            Storage::disk('public')->put($filename, (string) $encoded);

            $data['image_path'] = $filename;

            // Hapus file lama jika ada
            if ($this->existingImagePath) {
                Storage::disk('public')->delete($this->existingImagePath);
            }
        }

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
