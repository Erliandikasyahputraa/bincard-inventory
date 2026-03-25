<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanTransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        protected string $tanggalMulai,
        protected string $tanggalSelesai,
        protected string $tipe = ''
    ) {}

    public function collection()
    {
        $transactions = StockTransaction::with(['product', 'user', 'suratJalan.customer'])
            ->whereDate('created_at', '>=', $this->tanggalMulai)
            ->whereDate('created_at', '<=', $this->tanggalSelesai)
            ->when($this->tipe !== '', fn ($q) => $q->where('type', $this->tipe))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // --- RUNNING BALANCE CALCULATION ---
        $productIds = $transactions->pluck('product_id')->unique();

        // Sum of all quantities BEFORE the report window = starting balance per product
        $startingStock = [];
        foreach ($productIds as $productId) {
            $startingStock[$productId] = (int) StockTransaction::where('product_id', $productId)
                ->whereDate('created_at', '<', $this->tanggalMulai)
                ->sum('quantity');
        }

        $runningBalance = $startingStock;

        // Inject computed stock_before / stock_after per row
        $transactions->transform(function ($row) use (&$runningBalance) {
            $productId = $row->product_id;

            if (is_numeric($row->stock_before) && is_numeric($row->stock_after)) {
                // Use stored values; sync running tracker
                $row->computed_stock_before = (int) $row->stock_before;
                $row->computed_stock_after  = (int) $row->stock_after;
                $runningBalance[$productId] = (int) $row->stock_after;
            } else {
                // Legacy / seeder data — reconstruct
                $stockBefore = $runningBalance[$productId] ?? 0;
                $stockAfter  = $stockBefore + (int) $row->quantity;
                $row->computed_stock_before = $stockBefore;
                $row->computed_stock_after  = $stockAfter;
                $runningBalance[$productId] = $stockAfter;
            }

            // Explicitly set attributes on the model to ensure visibility in map()
            $row->setAttribute('computed_stock_before', $row->computed_stock_before);
            $row->setAttribute('computed_stock_after', $row->computed_stock_after);

            return $row;
        });

        return $transactions;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal & Waktu',
            'Kode Barang',
            'Kode Rak / Lokasi',
            'Nama Barang',
            'UoM',
            'Tipe',
            'Stok Awal',
            'SO / Mutasi',
            'Stok Akhir',
            'Yang Mengeluarkan',
            'Penerima Barang',
            'Keterangan',
        ];
    }

    /** @var int Row counter for the NO column */
    protected int $rowNumber = 0;

    public function map($row): array
    {
        $this->rowNumber++;

        $penerima = '';
        if ($row->type === StockTransaction::TIPE_OUT && $row->suratJalan && $row->suratJalan->customer) {
            $penerima = $row->suratJalan->customer->nama;
        }

        $product    = $row->product;
        $kodeBarang = $product?->barcode ?: ($product?->sku ?: '-');
        $kodeRak    = $product?->location ?: '-';

        $qty = (int) $row->quantity;
        // Always output as string with sign so Excel treats as text (prevents + stripping)
        $qtyStr = $qty >= 0 ? '+' . $qty : (string) $qty;

        return [
            $this->rowNumber,
            $row->created_at->format('d-m-Y H:i'),
            $kodeBarang,
            $kodeRak,
            $product?->name ?? '-',
            $product?->uom ?? 'PCS',                   // Column F: UoM
            $row->type,                                // Column G
            strval($row->getAttribute('stock_before') ?? ($row->computed_stock_before ?? '0')),
            $qtyStr,
            strval($row->getAttribute('stock_after')  ?? ($row->computed_stock_after  ?? '0')),
            $row->user?->name ?? '-',
            $penerima ?: '-',
            $row->note ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn(); // Should be 'L'

        // Full border
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Header row style
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Center: NO, Kode Barang, Kode Rak, Tipe, Stok Awal, Mutasi, Stok Akhir
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Color the Tipe column (F) AND the SO/Mutasi column (H) based on transaction type
        for ($r = 2; $r <= $lastRow; $r++) {
            $tipe = (string) $sheet->getCell('F' . $r)->getValue();

            // --- Tipe column (F) background badges ---
            if ($tipe === 'IN') {
                $sheet->getStyle('F' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                ]);
                // SO/Mutasi (H) — green for positive
                $sheet->getStyle('H' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                ]);
            } elseif ($tipe === 'OUT') {
                $sheet->getStyle('F' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                ]);
                // SO/Mutasi (H) — red for negative
                $sheet->getStyle('H' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                ]);
            } elseif ($tipe === 'ADJUST') {
                $sheet->getStyle('F' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                    'font' => ['color' => ['rgb' => '856404'], 'bold' => true],
                ]);
                // SO/Mutasi (H) — check sign for adjust (can be + or -)
                $hVal = (string) $sheet->getCell('H' . $r)->getValue();
                $isNeg = str_starts_with($hVal, '-') || (int)$hVal < 0;
                if ($isNeg) {
                    $sheet->getStyle('H' . $r)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                        'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                    ]);
                } else {
                    $sheet->getStyle('H' . $r)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                        'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                    ]);
                }
            }
        }

        return [];
    }
}
