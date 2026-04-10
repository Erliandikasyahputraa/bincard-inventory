<?php

namespace App\Livewire;

use App\Imports\ProdukImport;
use App\Imports\ProdukImportPreview;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

use Livewire\Attributes\Title;

#[Title('Import Produk')]
class ImportProdukExcel extends Component
{
    use WithFileUploads;

    public $file;
    public int $barisSukses = 0;
    public array $barisGagal = [];
    public array $duplikatDitemukan = [];
    public bool $menungguKonfirmasi = false;
    public array $duplikatPreview = [];
    public int $totalDuplikat = 0;
    public int $totalGagal = 0;

    public function simpan(): void
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);
        $this->barisSukses = 0;
        $this->barisGagal = [];
        $this->duplikatDitemukan = [];
        $this->duplikatPreview = [];
        $this->totalDuplikat = 0;
        $this->totalGagal = 0;
        $this->menungguKonfirmasi = false;

        $preview = new ProdukImportPreview();
        Excel::import($preview, $this->file->getRealPath());

        $normalizedRows = [];
        $validBarcodes = [];

        foreach ($preview->rows as $index => $row) {
            $baris = (int) $index + 2;
            $normalized = ProdukImport::normalizeRow($row, $baris);

            if ($normalized['is_invalid']) {
                $this->totalGagal++;
                if (count($this->barisGagal) < 50) {
                    $this->barisGagal[] = ['baris' => $baris, 'alasan' => $normalized['error_reason']];
                }
                continue;
            }

            $normalizedRows[] = $normalized;
            $validBarcodes[] = $normalized['barcode'];
        }

        if (count($normalizedRows) === 0) {
            $this->dispatch('transaksi-gagal', message: 'Tidak ada baris valid untuk diimport.');
            return;
        }

        $existingByBarcode = Product::whereIn('barcode', array_values(array_unique($validBarcodes)))
            ->get()
            ->keyBy('barcode');

        foreach ($normalizedRows as $row) {
            $existing = $existingByBarcode->get($row['barcode']);
            if (!$existing) {
                continue;
            }

            $incomingName = $row['name'] === '' ? $existing->name : $row['name'];
            $payload = $row['payload'];
            if ($row['name'] === '') {
                $payload['name'] = $existing->name;
            }

            $changes = $this->buildChangeSet($existing->toArray(), $payload);
            $this->totalDuplikat++;
            
            if (count($this->duplikatDitemukan) < 50) {
                $this->duplikatDitemukan[] = [
                    'baris' => $row['baris'],
                    'barcode' => $row['barcode'],
                    'name' => $incomingName,
                ];
            }
            if (count($this->duplikatPreview) < 50) {
                $this->duplikatPreview[] = [
                    'baris' => $row['baris'],
                    'barcode' => $row['barcode'],
                    'name' => $incomingName,
                    'status' => count($changes) > 0 ? 'akan_ditimpa' : 'tidak_berubah',
                    'changes' => $changes,
                ];
            }
        }

        if ($this->totalDuplikat > 0) {
            $this->menungguKonfirmasi = true;
            $this->dispatch('transaksi-gagal', message: "Ditemukan {$this->totalDuplikat} data duplikat. Pilih tindakan: lewati atau timpa data duplikat.");
            return;
        }

        $this->prosesImport(ProdukImport::DUPLICATE_SKIP);
    }

    public function pilihTindakanDuplikat(string $mode): void
    {
        if (!$this->menungguKonfirmasi) {
            return;
        }

        $mode = $mode === ProdukImport::DUPLICATE_OVERWRITE
            ? ProdukImport::DUPLICATE_OVERWRITE
            : ProdukImport::DUPLICATE_SKIP;

        $this->prosesImport($mode);
    }

    private function prosesImport(string $duplicateMode): void
    {
        $import = new ProdukImport($duplicateMode);
        
        // Baca dan import ulang dari file Excel secara langsung untuk menghemat memory
        Excel::import($import, $this->file->getRealPath());

        $this->barisSukses = $import->barisSukses;
        $this->totalGagal += count($import->barisGagal);
        $this->barisGagal = array_merge($this->barisGagal, array_slice($import->barisGagal, 0, 50));
        $this->menungguKonfirmasi = false;
        $this->duplikatDitemukan = [];
        $this->duplikatPreview = [];

        $this->dispatch('transaksi-sukses', [
            'message' => "Import selesai. Sukses: {$this->barisSukses}, Gagal: " . count($this->barisGagal),
        ]);
    }

    private function buildChangeSet(array $existing, array $incoming): array
    {
        $tracked = [
            'uom' => 'UOM',
            'location' => 'Lokasi',
            'min_stock' => 'Min Stock',
            'max_stock' => 'Max Stock',
            'current_stock' => 'Stok Saat Ini',
            'supplier_id' => 'Supplier ID',
        ];

        $changes = [];
        foreach ($tracked as $field => $label) {
            $old = $existing[$field] ?? null;
            $new = $incoming[$field] ?? null;
            if ((string) ($old ?? '') === (string) ($new ?? '')) {
                continue;
            }

            $changes[] = [
                'field' => $field,
                'label' => $label,
                'old' => $old,
                'new' => $new,
            ];
        }

        return $changes;
    }

    public function render()
    {
        return view('livewire.import-produk-excel')
            ->layout('layouts.app', ['title' => 'Import Produk Excel']);
    }
}
