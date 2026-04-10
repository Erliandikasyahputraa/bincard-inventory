<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Product::orderBy('name');
        
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(48)->withQueryString();
        $totalProdukSistem = Product::count();

        return view('qr.index', compact('products', 'totalProdukSistem'));
    }

    public function single($id)
    {
        $product = Product::findOrFail($id);
        return view('qr.single', compact('product'));
    }
}
