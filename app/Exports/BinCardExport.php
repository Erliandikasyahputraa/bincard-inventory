<?php

namespace App\Exports;

use App\Models\StockTransaction;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class BinCardExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected int $productId;
    protected ?Carbon $start;
    protected ?Carbon $end;

    public function __construct(int $productId, ?Carbon $start, ?Carbon $end)
    {
        $this->productId = $productId;
        $this->start     = $start;
        $this->end       = $end;
    }

    public function title(): string
    {
        return 'Bin Card';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal & Waktu',
            'Jenis Transaksi',
            'Referensi',
            'Masuk (Qty)',
            'Keluar (Qty)',
            'Saldo Stok',
            'PIC',
            'Keterangan',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 18,
            'D' => 20,
            'E' => 14,
            'F' => 14,
            'G' => 14,
            'H' => 20,
            'I' => 35,
        ];
    }

    public function collection()
    {
        $query = StockTransaction::with('user')
            ->where('product_id', $this->productId)
            ->orderBy('created_at', 'asc');

        if ($this->start) $query->where('created_at', '>=', $this->start);
        if ($this->end)   $query->where('created_at', '<=', $this->end);

        $rawTransactions = $query->get();

        // Running balance
        $runningBalance = $this->start
            ? StockTransaction::where('product_id', $this->productId)
                ->where('created_at', '<', $this->start)
                ->sum('quantity')
            : 0;

        $no = 1;
        return $rawTransactions->map(function ($trx) use (&$runningBalance, &$no) {
            $runningBalance += $trx->quantity;
            $masuk  = $trx->quantity > 0 ? $trx->quantity : '-';
            $keluar = $trx->quantity < 0 ? abs($trx->quantity) : '-';
            $typeLabel = match($trx->type) {
                'IN'     => 'Barang Masuk',
                'OUT'    => 'Barang Keluar',
                'ADJUSTMENT' => 'Penyesuaian',
                default  => $trx->type,
            };

            // Strip timestamp from notes, keep user text only
            $notes = preg_replace('/\s*\|\s*\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}/', '', $trx->notes ?? '');
            $notes = preg_replace('/\s*\(Penyesuaian stok oleh sistem.*?\)/', '', $notes);
            $notes = trim($notes);

            return [
                $no++,
                $trx->created_at->format('d/m/Y H:i'),
                $typeLabel,
                $trx->reference ?? '-',
                $masuk,
                $keluar,
                $runningBalance,
                $trx->user?->name ?? 'Sistem',
                $notes ?: '-',
            ];
        });
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = 'I';

        // Title header row
        $sheet->insertNewRowBefore(1, 3);

        $product = Product::find($this->productId);
        $sheet->setCellValue('A1', 'BIN CARD — ' . strtoupper($product->name ?? 'PRODUK'));
        $sheet->setCellValue('A2', 'Periode: ' . ($this->start ? $this->start->format('d M Y') : 'Semua') . ' s/d ' . ($this->end ? $this->end->format('d M Y') : now()->format('d M Y')));
        $sheet->setCellValue('A3', 'Dicetak: ' . now()->format('d M Y H:i'));

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        // Hitung actual last data row
        $dataLastRow = $sheet->getHighestRow();

        return [
            'A1' => [
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            'A2' => [
                'font'      => ['size' => 10, 'color' => ['argb' => 'FF334155']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F9FF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            'A3' => [
                'font'      => ['size' => 9, 'color' => ['argb' => 'FF64748B'], 'italic' => true],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF0F9FF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            '4'  => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            "A4:{$lastCol}{$dataLastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFD1D5DB'],
                    ],
                ],
            ],
            "E5:E{$dataLastRow}" => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF15803D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            "F5:F{$dataLastRow}" => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFBE123C']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            "G5:G{$dataLastRow}" => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
