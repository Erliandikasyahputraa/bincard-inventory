<?php

namespace App\Livewire;

use App\Imports\ProdukImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ImportProdukExcel extends Component
{
    use WithFileUploads;

    public $file;
    public int $barisSukses = 0;
    public array $barisGagal = [];

    public function simpan(): void
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        $import = new ProdukImport();
        Excel::import($import, $this->file->getRealPath());
        $this->barisSukses = $import->barisSukses;
        $this->barisGagal = $import->barisGagal;
        $this->dispatch('transaksi-sukses', ['message' => "Import selesai. Sukses: {$import->barisSukses}, Gagal: " . count($import->barisGagal)]);
    }

    public function render()
    {
        return view('livewire.import-produk-excel')
            ->layout('layouts.app', ['title' => 'Import Produk Excel']);
    }
}
