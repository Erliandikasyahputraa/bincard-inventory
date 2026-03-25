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
            'Satuan (UoM)',
            'Tipe Transaksi',
            'Stok Awal',
            'Mutasi / SO',
            'Stok Akhir',
            'Keterangan',
        ];
    }

    /** @var int Row counter for the NO column */
    protected int $rowNumber = 0;

    public function map($row): array
    {
        $this->rowNumber++;

        $product    = $row->product;
        $kodeBarang = $product?->barcode ?: ($product?->sku ?: '-');
        $kodeRak    = $product?->location ?: '-';

        $qty    = (int) $row->quantity;
        $qtyStr = $qty >= 0 ? '+' . $qty : (string) $qty;

        // Auto-generate keterangan berdasarkan tipe transaksi
        $keterangan = match($row->type) {
            'IN'     => 'Penerimaan Barang',
            'OUT'    => 'Pengeluaran Barang',
            'ADJUST' => ($qty >= 0 ? 'Penyesuaian Stok (+)' : 'Penyesuaian Stok (-)'),
            default  => $row->note ?? '-',
        };
        if ($row->note && $row->note !== '') {
            $keterangan .= ' – ' . $row->note;
        }

        return [
            $this->rowNumber,
            $row->created_at->format('d-m-Y H:i'),
            $kodeBarang,
            $kodeRak,
            $product?->name ?? '-',
            $product?->uom ?? 'PCS',
            $row->type,
            strval($row->getAttribute('stock_before') ?? ($row->computed_stock_before ?? '0')),
            $qtyStr,
            strval($row->getAttribute('stock_after')  ?? ($row->computed_stock_after  ?? '0')),
            $keterangan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow    = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn(); // Now 'K'

        // Full border
        $sheet->getStyle('A1:' . $lastColumn . $lastRow)
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Header: bold, clean — NO yellow colour
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '000000']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Centre-align columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Colour Tipe column (G) and Mutasi column (I) per transaction type
        for ($r = 2; $r <= $lastRow; $r++) {
            $tipe = (string) $sheet->getCell('G' . $r)->getValue();

            if ($tipe === 'IN') {
                $sheet->getStyle('G' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                ]);
                $sheet->getStyle('I' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'font' => ['color' => ['rgb' => '155724'], 'bold' => true],
                ]);
                // Keterangan (K) — green
                $sheet->getStyle('K' . $r)->applyFromArray([
                    'font' => ['color' => ['rgb' => '155724']],
                ]);
            } elseif ($tipe === 'OUT') {
                $sheet->getStyle('G' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                ]);
                $sheet->getStyle('I' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                    'font' => ['color' => ['rgb' => 'CC0000'], 'bold' => true],
                ]);
                // Keterangan (K) — red
                $sheet->getStyle('K' . $r)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'CC0000']],
                ]);
            } elseif ($tipe === 'ADJUST') {
                $sheet->getStyle('G' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF3CD']],
                    'font' => ['color' => ['rgb' => '856404'], 'bold' => true],
                ]);
                $hVal   = (string) $sheet->getCell('I' . $r)->getValue();
                $isNeg  = str_starts_with($hVal, '-') || (int)$hVal < 0;
                $color  = $isNeg ? 'CC0000' : '155724';
                $bgColor = $isNeg ? 'FFB6C1' : 'D4EDDA';
                $sheet->getStyle('I' . $r)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'font' => ['color' => ['rgb' => $color], 'bold' => true],
                ]);
                $sheet->getStyle('K' . $r)->applyFromArray([
                    'font' => ['color' => ['rgb' => $color]],
                ]);
            }
        }

        return [];
    }
}
