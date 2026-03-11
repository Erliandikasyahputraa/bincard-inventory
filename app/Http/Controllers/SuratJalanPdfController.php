<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SuratJalanPdfController extends Controller
{
    public function __invoke(Request $request, int $id)
    {
        $suratJalan = SuratJalan::with(['details.product', 'customer', 'createdBy'])->findOrFail($id);
        $pdf = Pdf::loadView('pdf.surat-jalan', ['suratJalan' => $suratJalan]);
        return $pdf->stream('Surat-Jalan-' . $suratJalan->nomor_surat_jalan . '.pdf');
    }
}
