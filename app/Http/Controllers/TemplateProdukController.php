<?php

namespace App\Http\Controllers;

use App\Exports\TemplateProdukExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TemplateProdukController extends Controller
{
    public function __invoke(): BinaryFileResponse
    {
        return Excel::download(new TemplateProdukExport(), 'template_import_produk.xlsx');
    }
}
