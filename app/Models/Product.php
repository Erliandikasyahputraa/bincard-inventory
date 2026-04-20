<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;
    
    protected static function booted()
    {
        static::saving(function ($product) {
            if ($product->isDirty('location')) {
                $loc = $product->location ?? '---';
                
                // If it's the placeholder, set all parts to ---
                if ($loc === '---' || empty(trim($loc))) {
                    $product->loc_aisle = '---';
                    $product->loc_floor = '---';
                    $product->loc_row   = '---';
                    $product->loc_col   = '---';
                    return;
                }

                $parts = explode('-', $loc);
                
                // Canonical parts index: 0=Aisle, 1=Floor, 2=Row, 3=Col
                $product->loc_aisle = isset($parts[0]) ? trim($parts[0]) : '---';
                $product->loc_floor = isset($parts[1]) ? trim($parts[1]) : '---';
                $product->loc_row   = isset($parts[2]) ? trim($parts[2]) : '---';
                $product->loc_col   = isset($parts[3]) ? trim($parts[3]) : '---';
            }
        });
    }

    protected $fillable = [
        'barcode',
        'sku',
        'name',
        'uom',
        'min_stock',
        'max_stock',
        'location',
        'current_stock',
        'supplier_id',
        'image_path',
    ];

    protected $casts = [
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    /** Stok saat ini (alias untuk current_stock, penamaan Indonesia) */
    public function getStokSaatIniAttribute(): int
    {
        return (int) $this->current_stock;
    }

    public function suratJalanDetails(): HasMany
    {
        return $this->hasMany(SuratJalanDetail::class, 'product_id');
    }

    public function stockOpnameDetails(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class, 'product_id');
    }
}
