<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanStokBarangExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected int $rowNumber = 0;

    public function title(): string
    {
        return 'Data Stok Barang';
    }

    public function query()
    {
        return Product::with('supplier')->orderBy('name');
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Material',
            'Material Description',
            'Mapping / Lokasi',
            'UoM',
            'Stok Saat Ini',
            'Stok Min',
            'Stok Max',
            'Status',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $status = 'Normal';
        if ($row->current_stock <= 0) {
            $status = 'Habis';
        } elseif ($row->current_stock <= $row->min_stock) {
            $status = 'Kritis';
        }

        return [
            $this->rowNumber,
            $row->barcode ?? $row->sku ?? '-',
            $row->name,
            $row->location ?? '-',
            $row->uom ?? 'PCS',
            $row->current_stock,
            $row->min_stock,
            $row->max_stock ?? '-',
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Borders for all data
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)
              ->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        // Header style — clean, no colour
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Number columns centre-align
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Colour rows by status
        for ($row = 2; $row <= $lastRow; $row++) {
            $status = $sheet->getCell('I' . $row)->getValue();
            if ($status === 'Habis') {
                $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'CC0000']],
                ]);
            } elseif ($status === 'Kritis') {
                $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                    'font' => ['color' => ['rgb' => '856404']],
                ]);
            }
        }

        return [];
    }
}
