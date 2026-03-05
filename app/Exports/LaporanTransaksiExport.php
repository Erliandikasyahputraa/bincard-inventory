<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanTransaksiExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected string $tanggalMulai,
        protected string $tanggalSelesai,
        protected string $tipe = ''
    ) {}

    public function query()
    {
        return StockTransaction::with(['product', 'user'])
            ->whereDate('created_at', '>=', $this->tanggalMulai)
            ->whereDate('created_at', '<=', $this->tanggalSelesai)
            ->when($this->tipe !== '', fn ($q) => $q->where('type', $this->tipe))
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Produk', 'Barcode', 'Tipe', 'Jumlah', 'Referensi', 'User', 'Catatan'];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('Y-m-d H:i'),
            $row->product->name ?? '',
            $row->product->barcode ?? '',
            $row->type,
            $row->quantity,
            $row->reference_id ?? '',
            $row->user->name ?? '',
            $row->note ?? '',
        ];
    }
}
