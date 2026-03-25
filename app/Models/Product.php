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
