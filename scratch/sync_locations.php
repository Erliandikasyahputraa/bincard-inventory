<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "Starting massive location sync for " . Product::count() . " products...\n";

$count = 0;
Product::chunk(500, function ($products) use (&$count) {
    foreach ($products as $product) {
        $loc = (string) $product->location;
        
        if (empty(trim($loc)) || $loc === '---') {
            $product->loc_aisle = '---';
            $product->loc_floor = '---';
            $product->loc_row   = '---';
            $product->loc_col   = '---';
        } else {
            $parts = explode('-', $loc);
            $product->loc_aisle = isset($parts[0]) ? trim($parts[0]) : '---';
            $product->loc_floor = isset($parts[1]) ? trim($parts[1]) : '---';
            $product->loc_row   = isset($parts[2]) ? trim($parts[2]) : '---';
            $product->loc_col   = isset($parts[3]) ? trim($parts[3]) : '---';
        }
        
        // Use direct DB update for speed and to bypass observers/logs for this mass repair
        DB::table('products')->where('id', $product->id)->update([
            'loc_aisle' => $product->loc_aisle,
            'loc_floor' => $product->loc_floor,
            'loc_row'   => $product->loc_row,
            'loc_col'   => $product->loc_col,
        ]);
        $count++;
    }
    echo "Processed $count products...\n";
});

echo "\nSYNC COMPLETE! Total $count products updated.\n";
