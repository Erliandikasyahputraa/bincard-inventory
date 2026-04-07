<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Database\QueryException;

class ProdukImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public int $barisSukses = 0;
    public array $barisGagal = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $baris = $index + 2;
            
            // Map new client headers to internal data structure
            $barcode = trim((string) ($row['komat'] ?? $row['barcode'] ?? $row['material'] ?? ''));
            $name = trim((string) ($row['material_description'] ?? $row['name'] ?? $row['nama'] ?? ''));
            
            if ($barcode === '' || $name === '') {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => 'KOMAT/Material (Barcode) dan Material Description (Nama) wajib diisi'];
                continue;
            }
            if (Product::where('barcode', $barcode)->exists()) {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => "Produk dengan KOMAT/Barcode '$barcode' sudah terdaftar"];
                continue;
            }
            try {
                $supplierId = isset($row['supplier_id']) && $row['supplier_id'] !== '' ? (int) $row['supplier_id'] : null;
                $allowedUom = ['PCS','SET','KLG','UN','KG','CM','BOX','BTG','BTL','DUS','LBR','MTR','TON','SAK','CAN','GLS','PKT'];
                $uom = strtoupper(trim((string) ($row['uom'] ?? $row['base_unit_of_measure'] ?? 'PCS')));
                if (!in_array($uom, $allowedUom)) $uom = 'PCS';

                Product::create([
                    'barcode'       => $barcode,
                    'sku'           => $barcode,
                    'name'          => $name,
                    'uom'           => $uom,
                    'min_stock'     => (int) ($row['min_stock'] ?? 0),
                    'max_stock'     => isset($row['max_stock']) && $row['max_stock'] !== '' ? (int) $row['max_stock'] : null,
                    'location'      => $row['mapping'] ?? $row['location'] ?? $row['storage_location'] ?? null,
                    'current_stock' => (int) ($row['stock_sap'] ?? $row['stok_awal'] ?? $row['current_stock'] ?? $row['unrestricted'] ?? 0),
                    'supplier_id'   => $supplierId,
                ]);
                $this->barisSukses++;
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1452) {
                    $this->barisGagal[] = ['baris' => $baris, 'alasan' => "ID Supplier '" . ($row['supplier_id'] ?? '') . "' tidak dikenali sistem. Kosongkan kolom 'Supplier ID' di Excel atau daftar Supplier dulu."];
                } else {
                    $this->barisGagal[] = ['baris' => $baris, 'alasan' => 'Gagal simpan ke database.'];
                }
            } catch (\Throwable $e) {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => 'Error: ' . $e->getMessage()];
            }
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
