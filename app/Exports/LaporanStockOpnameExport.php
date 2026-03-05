<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanStockOpnameExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected int $opnameId)
    {
    }

    public function collection()
    {
        $opname = StockOpname::with('details.product')->findOrFail($this->opnameId);

        return $opname->details;
    }

    public function headings(): array
    {
        return ['Produk', 'Barcode', 'Stok Sistem', 'Stok Fisik', 'Selisih'];
    }

    public function map($row): array
    {
        return [
            $row->product->name ?? '',
            $row->product->barcode ?? '',
            $row->stok_sistem,
            $row->stok_fisik,
            $row->selisih,
        ];
    }
}

