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
        $locations = Product::whereNotNull('location')->where('location', '!=', '')->distinct()->orderBy('location')->pluck('location');

        return view('qr.index', compact('products', 'totalProdukSistem', 'locations'));
    }

    /** Cetak semua produk tanpa pagination */
    public function printAll(Request $request)
    {
        $query = $this->buildQuery($request);
        $products = $query->get();
        $totalProdukSistem = Product::count();
        $locations = Product::whereNotNull('location')->where('location', '!=', '')->distinct()->orderBy('location')->pluck('location');

        return view('qr.index', compact('products', 'totalProdukSistem', 'locations'));
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

        if ($location = $request->input('location')) {
            $query->where('location', $location);
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
                $query->orderBy('location', $sortDir);
                break;
            case 'status_kritis':
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0)->orderBy('name', 'asc');
                break;
            case 'status_habis':
                $query->where('current_stock', 0)->orderBy('name', 'asc');
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
