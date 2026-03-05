<?php

namespace App\Http\Controllers;

use App\Exports\LaporanStockOpnameExport;
use App\Models\StockOpname;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanOpnameExportController extends Controller
{
    public function __invoke(int $id): BinaryFileResponse
    {
        $opname = StockOpname::findOrFail($id);

        return Excel::download(
            new LaporanStockOpnameExport($opname->id),
            'Laporan_Stock_Opname_' . $opname->tanggal_opname->format('Y-m-d') . '.xlsx'
        );
    }
}

