<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('qr.index', compact('products'));
    }

    public function single($id)
    {
        $product = Product::findOrFail($id);
        return view('qr.single', compact('product'));
    }
}
