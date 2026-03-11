<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanTransaksiExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected string $tanggalMulai,
        protected string $tanggalSelesai,
        protected string $tipe = ''
    ) {}

    public function query()
    {
        return StockTransaction::with(['product', 'user', 'suratJalan.customer'])
            ->whereDate('created_at', '>=', $this->tanggalMulai)
            ->whereDate('created_at', '<=', $this->tanggalSelesai)
            ->when($this->tipe !== '', fn ($q) => $q->where('type', $this->tipe))
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return ['Tanggal', 'Produk', 'Barcode', 'Tipe', 'Stok Awal', 'Transaksi', 'Stok Akhir', 'Referensi', 'Yang Mengeluarkan', 'Penerima Barang', 'Catatan'];
    }

    public function map($row): array
    {
        $penerima = '';
        if ($row->type === StockTransaction::TIPE_OUT && $row->suratJalan && $row->suratJalan->customer) {
            $penerima = $row->suratJalan->customer->nama;
        }

        return [
            $row->created_at->format('Y-m-d H:i'),
            $row->product->name ?? '',
            $row->product->barcode ?? '',
            $row->type,
            $row->stock_before,
            $row->type === StockTransaction::TIPE_IN ? '+' . $row->quantity : '-' . ltrim((string)$row->quantity, '-'),
            $row->stock_after,
            $row->reference_id ?? '',
            $row->user->name ?? '', // Yang Mengeluarkan
            $penerima,              // Penerima Barang
            $row->note ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $lastColumn . $lastRow;

        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($row = 2; $row <= $lastRow; $row++) {
            $val = $sheet->getCell('F' . $row)->getValue();
            if (is_string($val) && str_starts_with($val, '-')) {
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true]
                ]);
            } elseif (is_string($val) && str_starts_with($val, '+')) {
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true]
                ]);
            }
        }

        return [];
    }
}
