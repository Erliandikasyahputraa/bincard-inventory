<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanStokHarianExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(protected string $tanggal) {}

    public function query()
    {
        return StockTransaction::with(['product', 'user'])
            ->whereDate('created_at', $this->tanggal)
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return ['Waktu', 'Produk', 'Barcode', 'Tipe', 'Jumlah', 'User', 'Catatan'];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('H:i'),
            $row->product->name ?? '',
            $row->product->barcode ?? '',
            $row->type,
            $row->quantity,
            $row->user->name ?? '',
            $row->note ?? '',
        ];
    }
}
