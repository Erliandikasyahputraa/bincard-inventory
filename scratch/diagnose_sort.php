<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

$total = Product::count();
$emptyAisle = Product::where('loc_aisle', '---')->orWhereNull('loc_aisle')->count();

echo "Total Products: $total\n";
echo "Products with empty/--- loc_aisle: $emptyAisle\n";

$sample = Product::where('location', 'LIKE', 'A-%')->take(5)->get();
foreach($sample as $p) {
    echo "Location: {$p->location} | Aisle: {$p->loc_aisle} | Floor: {$p->loc_floor}\n";
}

// Test Sort logic
echo "\nTesting Sort Simulation:\n";
$sorted = Product::where('location', 'LIKE', 'A-%')
    ->orderByRaw("CAST(loc_floor AS UNSIGNED) ASC")
    ->orderBy('loc_floor', 'ASC')
    ->take(5)
    ->pluck('location');

foreach($sorted as $loc) {
    echo "Sorted Case: $loc\n";
}
