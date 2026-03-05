<?php

namespace App\Http\Controllers;

use App\Exports\LaporanStokHarianExport;
use App\Exports\LaporanTransaksiExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanExportController extends Controller
{
    public function transaksi(Request $request): BinaryFileResponse
    {
        $tanggalMulai = filled($request->input('tanggalMulai')) ? $request->input('tanggalMulai') : now()->startOfMonth()->format('Y-m-d');
        $tanggalSelesai = filled($request->input('tanggalSelesai')) ? $request->input('tanggalSelesai') : now()->format('Y-m-d');
        $tipe = (string) ($request->input('tipeTransaksi') ?? '');
        return Excel::download(
            new LaporanTransaksiExport($tanggalMulai, $tanggalSelesai, $tipe),
            'Laporan_Transaksi_' . $tanggalMulai . '_' . $tanggalSelesai . '.xlsx'
        );
    }

    public function harian(Request $request): BinaryFileResponse
    {
        $tanggal = filled($request->input('tanggalMulai')) ? $request->input('tanggalMulai') : now()->format('Y-m-d');
        return Excel::download(
            new LaporanStokHarianExport($tanggal),
            'Laporan_Stok_Harian_' . $tanggal . '.xlsx'
        );
    }
}
