<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Product::query();
        
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($location = $request->input('location')) {
            $query->where('location', $location);
        }

        $sort = $request->input('sort', 'name_asc');
        switch ($sort) {
            case 'newest':
                $query->orderBy('id', 'desc');
                break;
            case 'filter_kritis':
                $query->whereColumn('current_stock', '<=', 'min_stock')->where('current_stock', '>', 0)->orderBy('name', 'asc');
                break;
            case 'filter_habis':
                $query->where('current_stock', 0)->orderBy('name', 'asc');
                break;
            case 'stock_highest':
                $query->orderBy('current_stock', 'desc');
                break;
            case 'rack_asc':
                $query->orderBy('location', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $products = $query->paginate(48)->withQueryString();
        $totalProdukSistem = Product::count();
        $locations = Product::whereNotNull('location')->where('location', '!=', '')->distinct()->orderBy('location')->pluck('location');

        return view('qr.index', compact('products', 'totalProdukSistem', 'locations'));
    }

    public function single($id)
    {
        $product = Product::findOrFail($id);
        return view('qr.single', compact('product'));
    }
}
