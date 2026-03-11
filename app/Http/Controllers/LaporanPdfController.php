<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPdfController extends Controller
{
    public function transaksi(Request $request)
    {
        $tanggalMulai = filled($request->input('tanggalMulai')) ? $request->input('tanggalMulai') : now()->startOfMonth()->format('Y-m-d');
        $tanggalSelesai = filled($request->input('tanggalSelesai')) ? $request->input('tanggalSelesai') : now()->format('Y-m-d');
        $tipe = $request->input('tipeTransaksi', null) ?: '';

        $query = StockTransaction::with(['product', 'user'])
            ->whereDate('created_at', '>=', $tanggalMulai)
            ->whereDate('created_at', '<=', $tanggalSelesai)
            ->when($tipe !== '', fn ($q) => $q->where('type', $tipe))
            ->orderBy('created_at');

        $transaksi = $query->get();

        $pdf = Pdf::loadView('pdf.laporan-transaksi', [
            'transaksi' => $transaksi,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'tipeTransaksi' => $tipe,
        ]);

        return $pdf->download('Laporan_Transaksi_' . $tanggalMulai . '_' . $tanggalSelesai . '.pdf');
    }

    public function harian(Request $request)
    {
        $tanggal = filled($request->input('tanggalMulai')) ? $request->input('tanggalMulai') : now()->format('Y-m-d');
        
        $start = \Carbon\Carbon::parse($tanggal)->startOfDay();
        $end = \Carbon\Carbon::parse($tanggal)->endOfDay();

        $transaksi = StockTransaction::with(['product', 'user', 'suratJalan.customer'])
            ->where('type', StockTransaction::TIPE_OUT)
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $pdf = Pdf::loadView('pdf.laporan-harian', [
            'transaksi' => $transaksi,
            'tanggal' => $tanggal,
        ]);

        return $pdf->download('Laporan_Stok_Harian_' . $tanggal . '.pdf');
    }
}

