<?php

namespace App\Exports;

use App\Models\StockTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanStokHarianExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(protected string $tanggal) {}

    public function query()
    {
        $start = \Carbon\Carbon::parse($this->tanggal)->startOfDay();
        $end = \Carbon\Carbon::parse($this->tanggal)->endOfDay();

        // Only pull OUT transactions = Surat Jalan
        return StockTransaction::with(['product', 'user', 'suratJalan.customer'])
            ->where('type', StockTransaction::TIPE_OUT)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at');
    }

    public function headings(): array
    {
        return ['Waktu Keluaran', 'Nomor Surat Jalan', 'Penerima Barang', 'Nama Produk', 'SKU/Barcode', 'Jumlah Keluar', 'Admin / Issuer', 'Catatan'];
    }

    public function map($row): array
    {
        $nomorSj = $row->suratJalan ? $row->suratJalan->nomor_surat_jalan : $row->reference_id;
        $penerima = ($row->suratJalan && $row->suratJalan->customer) ? $row->suratJalan->customer->nama : '-';

        return [
            $row->created_at->format('H:i'),
            $nomorSj ?? '-',
            $penerima,
            $row->product->name ?? '',
            $row->product->barcode ?? '',
            '-' . $row->quantity,
            $row->user->name ?? '',
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
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($row = 2; $row <= $lastRow; $row++) {
            $sheet->getStyle('F' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB6C1']],
                'font' => ['color' => ['rgb' => 'FF0000'], 'bold' => true]
            ]);
        }

        return [];
    }
}
