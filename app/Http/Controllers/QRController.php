<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $products = $query->paginate(48)->withQueryString();

        $totalProdukSistem = Product::count();
        $aisles = Product::whereNotNull('loc_aisle')
            ->where('loc_aisle', '!=', '---')
            ->where('loc_aisle', '!=', '')
            ->distinct()
            // Alphabetical Priority: Letters first, then numbers
            ->orderByRaw("CASE WHEN LOWER(SUBSTR(loc_aisle, 1, 1)) BETWEEN 'a' AND 'z' THEN 0 ELSE 1 END ASC")
            ->orderBy('loc_aisle')
            ->pluck('loc_aisle');

        return view('qr.index', compact('products', 'totalProdukSistem', 'aisles'));
    }

    /** Cetak semua produk tanpa pagination (gunakan paginate besar agar view kompatibel) */
    /** Cetak massal dengan view minimalis (high performance) */
    public function printAll(Request $request)
    {
        $query = $this->buildQuery($request);

        // Jika ada ID terpilih, batasi hanya pada ID tersebut
        if ($request->has('ids') && $request->ids) {
            $ids = explode(',', $request->ids);
            $query->whereIn('id', $ids);
        }

        // Ambil semua produk tanpa pagination (karena view bulk menangani ribuan item)
        $products = $query->get();
        $size     = $request->get('size', '10x7');

        return view('qr.bulk', compact('products', 'size'));
    }

    private function buildQuery(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
            );
        }

        if ($aisle = $request->input('aisle')) {
            if ($aisle === 'UNKNOWN') {
                $query->where(fn($q) => $q->whereNull('loc_aisle')->orWhere('loc_aisle', '---')->orWhere('loc_aisle', ''));
            } else {
                $query->where('loc_aisle', $aisle);
            }
        }

        // Toggle sort: sort=field, dir=asc|desc
        $sortField = $request->input('sort', 'name');
        $sortDir   = $request->input('dir', 'asc');
        $sortDir   = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';

        switch ($sortField) {
            case 'newest':
                $query->orderBy('id', $sortDir);
                break;
            case 'stock':
                $query->orderBy('current_stock', $sortDir);
                break;
            case 'location':
                // Complex natural sort for multi-part location: Letters first, then numeric sequence
                $query->orderByRaw("loc_aisle = '---' ASC")
                      ->orderByRaw("CASE WHEN LOWER(SUBSTR(loc_aisle, 1, 1)) BETWEEN 'a' AND 'z' THEN 0 ELSE 1 END ASC")
                      ->orderBy('loc_aisle', $sortDir)

                      ->orderByRaw("CASE WHEN LOWER(SUBSTR(loc_floor, 1, 1)) BETWEEN 'a' AND 'z' THEN 0 ELSE 1 END ASC")
                      ->orderByRaw("CAST(loc_floor AS INTEGER) " . $sortDir)
                      ->orderBy('loc_floor', $sortDir)

                      ->orderByRaw("CASE WHEN LOWER(SUBSTR(loc_row, 1, 1)) BETWEEN 'a' AND 'z' THEN 0 ELSE 1 END ASC")
                      ->orderByRaw("CAST(loc_row AS INTEGER) " . $sortDir)
                      ->orderBy('loc_row', $sortDir)

                      ->orderByRaw("CASE WHEN LOWER(SUBSTR(loc_col, 1, 1)) BETWEEN 'a' AND 'z' THEN 0 ELSE 1 END ASC")
                      ->orderByRaw("CAST(loc_col AS INTEGER) " . $sortDir)
                      ->orderBy('loc_col', $sortDir);
                break;
            case 'status_kritis':
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0)->orderBy('name', $sortDir);
                break;
            case 'status_habis':
                $query->where('current_stock', 0)->orderBy('name', $sortDir);
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortDir);
                break;
        }

        return $query;
    }

    public function single($id)
    {
        $product = Product::findOrFail($id);
        return view('qr.single', compact('product'));
    }
}
