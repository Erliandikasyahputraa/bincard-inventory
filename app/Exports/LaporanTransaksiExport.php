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
        // Fetch all transactions in the date range, ordered chronologically
        $transactions = StockTransaction::with(['product', 'user', 'suratJalan.customer'])
            ->whereDate('created_at', '>=', $this->tanggalMulai)
            ->whereDate('created_at', '<=', $this->tanggalSelesai)
            ->when($this->tipe !== '', fn ($q) => $q->where('type', $this->tipe))
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        // --- RUNNING BALANCE CALCULATION ---
        // We need the cumulative stock per product up to BEFORE each transaction in this export.
        // Strategy: for each transaction, if stock_before/stock_after are null (legacy rows),
        // we reconstruct them from the running balance across ALL prior transactions for that product.

        // Collect all unique product IDs to batch-compute their starting stock BEFORE this report period
        $productIds = $transactions->pluck('product_id')->unique();

        // For each product, get the sum of all transaction quantities BEFORE our start date
        // This is the "stock balance at epoch" for the report
        $startingStock = [];
        foreach ($productIds as $productId) {
            $sumBefore = StockTransaction::where('product_id', $productId)
                ->whereDate('created_at', '<', $this->tanggalMulai)
                ->orderBy('created_at')
                ->orderBy('id')
                ->sum('quantity');
            $startingStock[$productId] = (int) $sumBefore;
        }

        // Running balance per-product within the REPORT period
        $runningBalance = $startingStock;

        // Augment each transaction with computed stock_before/stock_after
        $transactions->transform(function ($row) use (&$runningBalance) {
            $productId = $row->product_id;

            // If stock_before is already stored (new data), use it; otherwise compute from running balance
            if (is_numeric($row->stock_before) && is_numeric($row->stock_after)) {
                // Data exists in DB, use it directly, but also sync our running tracker
                $runningBalance[$productId] = (int) $row->stock_after;
            } else {
                // Legacy / seeder data — compute on-the-fly
                $stockBefore = $runningBalance[$productId] ?? 0;
                $stockAfter = $stockBefore + (int) $row->quantity;
                $row->computed_stock_before = $stockBefore;
                $row->computed_stock_after = $stockAfter;
                $runningBalance[$productId] = $stockAfter;
            }

            return $row;
        });

        return $transactions;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Produk', 'Barcode', 'Tipe', 'Stok Awal', 'Transaksi', 'Stok Akhir', 'Yang Mengeluarkan', 'Penerima Barang', 'Catatan'];
    }

    public function map($row): array
    {
        $penerima = '';
        if ($row->type === StockTransaction::TIPE_OUT && $row->suratJalan && $row->suratJalan->customer) {
            $penerima = $row->suratJalan->customer->nama;
        }

        // Use computed values if DB values are missing (legacy data)
        $stockBefore = is_numeric($row->stock_before) ? (int) $row->stock_before : ($row->computed_stock_before ?? 0);
        $stockAfter  = is_numeric($row->stock_after)  ? (int) $row->stock_after  : ($row->computed_stock_after  ?? 0);

        $qty = (int) $row->quantity;
        $qtyFormatted = $qty >= 0 ? '+' . $qty : (string) $qty;

        return [
            $row->created_at->format('d-m-Y H:i'),
            $row->product->name ?? '',
            $row->product->barcode ?? '',
            $row->type,
            $stockBefore,
            $qtyFormatted,
            $stockAfter,
            $row->user->name ?? '',
            $penerima,
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
