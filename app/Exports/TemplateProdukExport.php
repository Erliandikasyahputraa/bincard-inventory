<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TemplateProdukExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['barcode', 'sku', 'name', 'min_stock', 'max_stock', 'location', 'stok_awal', 'supplier_id'];
    }

    public function array(): array
    {
        return [
            ['BRC001', 'SKU001', 'Contoh Produk 1', 5, 100, 'Rak A1', 10, 1],
        ];
    }
}
