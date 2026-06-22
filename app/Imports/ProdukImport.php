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
    public const DUPLICATE_SKIP = 'skip';
    public const DUPLICATE_OVERWRITE = 'overwrite';

    public int $barisSukses = 0;
    public array $barisGagal = [];
    private string $duplicateMode;

    public function __construct(string $duplicateMode = self::DUPLICATE_SKIP)
    {
        $this->duplicateMode = $duplicateMode;
    }

    public function collection(Collection $rows): void
    {
        $this->importRows($rows);
    }

    public function importRows(iterable $rows): void
    {
        foreach ($rows as $index => $row) {
            $baris = is_int($index) ? $index + 2 : 2;
            $normalized = self::normalizeRow($row, $baris);

            if ($normalized['is_invalid']) {
                $this->barisGagal[] = ['baris' => $baris, 'alasan' => $normalized['error_reason']];
                continue;
            }

            try {
                $existing = Product::where('barcode', $normalized['barcode'])->first();

                if ($existing) {
                    // Timpani nama beda atau tanpa nama, anggap sebagai UPDATE
                    $incomingName = $normalized['name'] === '' ? $existing->name : $normalized['name'];
                    
                    if ($this->duplicateMode === self::DUPLICATE_SKIP) {
                        $this->barisGagal[] = [
                            'baris' => $baris,
                            'alasan' => "Produk dengan Barcode '{$normalized['barcode']}' sudah ada di sistem (dilewati).",
                        ];
                        continue;
                    }

                    $payload = $normalized['payload'];
                    if ($normalized['name'] === '') {
                        $payload['name'] = $existing->name;
                    }
                    $existing->update($payload);
                } else {
                    if ($normalized['name'] === '') {
                        $this->barisGagal[] = [
                            'baris' => $baris,
                            'alasan' => "Barcode '{$normalized['barcode']}' adalah produk baru. Nama (Material Description) wajib diisi.",
                        ];
                        continue;
                    }
                    Product::create($normalized['payload']);
                }

                $this->barisSukses++;
            } catch (QueryException $e) {
                if ($e->errorInfo[1] == 1452) {
                    $this->barisGagal[] = [
                        'baris' => $baris,
                        'alasan' => "ID Supplier '" . ($normalized['supplier_id_raw'] ?? '') . "' tidak dikenali sistem. Kosongkan kolom 'Supplier ID' di Excel atau daftar Supplier dulu.",
                    ];
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

    public static function normalizeRow(mixed $row, int $baris): array
    {
        $data = is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : []);

        // Mendukung KOMAT dari template (komat, barcode) maupun Kode Material dari hasil export (kode_material)
        $barcode = trim((string) ($data['komat'] ?? $data['barcode'] ?? $data['material'] ?? $data['kode_material'] ?? ''));
        $name = trim((string) ($data['material_description'] ?? $data['name'] ?? $data['nama'] ?? ''));

        if ($barcode === '') {
            return [
                'is_invalid' => true,
                'error_reason' => 'KOMAT/Material (Barcode) wajib diisi',
            ];
        }

        $supplierIdRaw = $data['supplier_id'] ?? null;
        $supplierId = isset($supplierIdRaw) && $supplierIdRaw !== '' ? (int) $supplierIdRaw : null;
        $allowedUom = ['PCS', 'SET', 'KLG', 'UN', 'KG', 'CM', 'BOX', 'BTG', 'BTL', 'DUS', 'LBR', 'MTR', 'TON', 'SAK', 'CAN', 'GLS', 'PKT'];
        $uom = strtoupper(trim((string) ($data['uom'] ?? $data['base_unit_of_measure'] ?? 'PCS')));
        if (!in_array($uom, $allowedUom, true)) {
            $uom = 'PCS';
        }

        // Mendukung MAPPING dari template maupun Mapping / Lokasi dari hasil export (mapping_lokasi / mapping__lokasi)
        $location = trim((string) ($data['mapping'] ?? $data['location'] ?? $data['storage_location'] ?? $data['mapping_lokasi'] ?? $data['mapping__lokasi'] ?? $data['lokasi'] ?? ''));
        $location = $location !== '' ? $location : 'Lantai 1';

        // Mendukung Stock SAP dari template maupun Stok Saat Ini dari hasil export
        $minRaw = $data['min_stock'] ?? $data['stok_min'] ?? null;
        $maxRaw = $data['max_stock'] ?? $data['stok_max'] ?? null;
        $stockRaw = $data['stock_sap'] ?? $data['stok_awal'] ?? $data['current_stock'] ?? $data['unrestricted'] ?? $data['stok_saat_ini'] ?? null;

        return [
            'is_invalid' => false,
            'baris' => $baris,
            'barcode' => $barcode,
            'name' => $name,
            'supplier_id_raw' => $supplierIdRaw,
            'payload' => [
                'barcode' => $barcode,
                'sku' => $barcode,
                'name' => $name,
                'uom' => $uom,
                'min_stock' => self::toIntOrDefault($minRaw, 1),
                'max_stock' => self::toIntOrDefault($maxRaw, 10),
                'location' => $location,
                'current_stock' => self::toIntOrDefault($stockRaw, 0),
                'supplier_id' => $supplierId,
            ],
        ];
    }

    private static function toIntOrDefault(mixed $value, int $default): int
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return (int) $value;
    }
}
