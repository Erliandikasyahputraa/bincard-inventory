<?php

namespace App\Http\Controllers;

use App\Models\StockTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanPdfController extends Controller
{
    public function transaksi(Request $request)
    {
        $tanggalMulai = $request->input('tanggalMulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->input('tanggalSelesai', now()->format('Y-m-d'));
        $tipe = $request->input('tipeTransaksi', '');

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
}

