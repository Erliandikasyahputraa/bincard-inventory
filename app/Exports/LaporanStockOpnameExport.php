<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanStockOpnameExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected int $rowNumber = 0;

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
        return [
            'NO',
            'Kode Barang / QR',
            'Nama Produk',
            'Letak / Rak',
            'Jumlah Awal',
            'Adjust',
            'Jumlah Akhir',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        
        $selisih = $row->selisih;
        $keterangan = '';
        if ($selisih < 0) {
            $keterangan = 'Minus ' . abs($selisih);
        } elseif ($selisih > 0) {
            $keterangan = 'Plus ' . $selisih;
        } else {
            $keterangan = 'Sesuai';
        }

        return [
            $this->rowNumber,
            $row->product->barcode ?? $row->product->sku ?? '',
            $row->product->name ?? '',
            $row->product->location ?? '-',
            $row->stok_sistem,
            $selisih > 0 ? '+' . $selisih : $selisih,
            $row->stok_fisik ?? 0,
            $keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $lastColumn . $lastRow;

        // 1. Set All Borders
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // 2. Format Header (Yellow, Bold)
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFFF00', // Yellow
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ]
        ]);
        
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Alignments
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Conditional Formatting for "Adjust" column (F)
        for ($row = 2; $row <= $lastRow; $row++) {
            $adjustValue = $sheet->getCell('F' . $row)->getValue();
            
            // Clean up the value for numeric comparison (remove + if present)
            $numericValue = (int) str_replace('+', '', $adjustValue);
            
            if ($numericValue < 0) {
                // Formatting for Negative (Red)
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFB6C1'], // Light Pink
                    ],
                    'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true]
                ]);
            } elseif ($numericValue > 0) {
                // Formatting for Positive (Green)
                $sheet->getStyle('F' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D4EDDA'], // Light Green
                    ],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true]
                ]);
            }
            // If 0, do nothing (default black)
        }

        return [];
    }
}
