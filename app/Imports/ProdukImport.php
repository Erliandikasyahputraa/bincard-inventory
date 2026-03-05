<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProdukImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public int $barisSukses = 0;
    public array $barisGagal = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $baris = $index + 2;
            $barcode = trim((string) ($row['barcode'] ?? ''));
            $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));
            if ($barcode === '' || $name === '') {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => 'Barcode dan Nama wajib'];
                continue;
            }
            if (Product::where('barcode', $barcode)->exists()) {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => 'Barcode sudah ada'];
                continue;
            }
            try {
                Product::create([
                    'barcode' => $barcode,
                    'sku' => $row['sku'] ?? null,
                    'name' => $name,
                    'min_stock' => (int) ($row['min_stock'] ?? 0),
                    'max_stock' => isset($row['max_stock']) && $row['max_stock'] !== '' ? (int) $row['max_stock'] : null,
                    'location' => $row['location'] ?? null,
                    'current_stock' => (int) ($row['stok_awal'] ?? $row['current_stock'] ?? 0),
                    'supplier_id' => isset($row['supplier_id']) && $row['supplier_id'] !== '' ? (int) $row['supplier_id'] : null,
                ]);
                $this->barisSukses++;
            } catch (\Throwable $e) {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => $e->getMessage()];
            }
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
