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

    public function __construct(protected int $opnameId) {}

    public function collection()
    {
        $opname = StockOpname::with('details.product')->findOrFail($this->opnameId);
        return $opname->details;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Material',
            'Material Description',
            'Mapping / Lokasi',
            'UoM',
            'Stok SAP (Sistem)',
            'Stok Fisik',
            'Keterangan',
            'Hasil / Selisih',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $selisih     = (int) $row->selisih;
        $stokFisik   = $row->stok_fisik;
        $stokSistem  = (int) $row->stok_sistem;

        // Auto-generate human-friendly Keterangan
        if ($stokFisik === null) {
            $keterangan = 'Belum Dihitung';
        } elseif ($selisih < 0) {
            $keterangan = 'Kurang ' . abs($selisih);
        } elseif ($selisih > 0) {
            $keterangan = 'Lebih ' . $selisih;
        } else {
            $keterangan = 'Sesuai';
        }

        $selisihFmt = $selisih > 0 ? '+' . $selisih : (string) $selisih;

        return [
            $this->rowNumber,
            $row->product->barcode ?? $row->product->sku ?? '-',
            $row->product->name ?? '-',
            $row->product->location ?? '-',
            $row->product->uom ?? 'PCS',
            $stokSistem,
            $stokFisik ?? '-',
            $keterangan,
            $selisihFmt,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        // Full border
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)
              ->getBorders()->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN);

        // Header — bold only, no colour (per request)
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Centre-align number columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Row-level conditional colour based on selisih (column I)
        for ($row = 2; $row <= $lastRow; $row++) {
            $selisihRaw = (string) $sheet->getCell('I' . $row)->getValue();
            $selisihNum = (int) str_replace('+', '', $selisihRaw);

            if ($selisihNum < 0) {
                // Kurang → full row light red
                $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFE5E5']],
                ]);
                // Keterangan cell bold red
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                ]);
                // Selisih cell red
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true],
                ]);
            } elseif ($selisihNum > 0) {
                // Lebih → full row light green
                $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8F5E9']],
                ]);
                // Keterangan cell bold green
                $sheet->getStyle('H' . $row)->applyFromArray([
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                ]);
                // Selisih cell green
                $sheet->getStyle('I' . $row)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                ]);
            }
        }

        return [];
    }
}
