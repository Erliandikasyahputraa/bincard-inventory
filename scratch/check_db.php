<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;

echo "Total Products: " . Product::count() . "\n";
$latest = StockOpname::latest('id')->first();
if ($latest) {
    echo "Latest Opname ID: " . $latest->id . " Status: " . $latest->status . "\n";
    echo "Details Count: " . StockOpnameDetail::where('stock_opname_id', $latest->id)->count() . "\n";
} else {
    echo "No Opname sessions found.\n";
}
