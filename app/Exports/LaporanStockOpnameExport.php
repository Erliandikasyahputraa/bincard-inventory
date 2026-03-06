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
            'KOMAT',
            'MAPPING',
            'Material Description',
            'Stock SAP',
            'Stock Fisik',
            'UoM',
            'SO',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        
        $selisih = $row->selisih;
        $keterangan = '';
        if ($selisih < 0) {
            $keterangan = 'Kurang ' . abs($selisih);
        } elseif ($selisih > 0) {
            $keterangan = 'Lebih ' . $selisih;
        } else {
            $keterangan = 'OK';
        }

        return [
            $this->rowNumber,
            $row->product->sku ?? $row->product->barcode ?? '',
            $row->product->location ?? '-',
            $row->product->name ?? '',
            $row->stok_sistem,
            $row->stok_fisik ?? 0,
            'PC',
            $selisih,
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

        // 2. Format Header (Yellow, Bold, AutoFilter)
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
        
        // Let's set auto filter
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // 3. Conditional Formatting for "SO" column (H) if it's less than 0
        // And optional styling for column alignments
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($row = 2; $row <= $lastRow; $row++) {
            $soValue = $sheet->getCell('H' . $row)->getValue();
            if (is_numeric($soValue) && $soValue < 0) {
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'FFB6C1', // Light Pink/Red for negatives
                        ],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FF0000']
                    ]
                ]);
            }
        }

        return [];
    }
}
