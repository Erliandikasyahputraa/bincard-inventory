<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransaction extends Model
{
    use HasFactory;

    public const TIPE_IN = 'IN';
    public const TIPE_OUT = 'OUT';
    public const TIPE_ADJUST = 'ADJUST';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reference_id',
        'user_id',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Apakah transaksi ini menambah stok */
    public function isMasuk(): bool
    {
        return $this->type === self::TIPE_IN || ($this->type === self::TIPE_ADJUST && $this->quantity > 0);
    }

    public function suratJalan(): BelongsTo
    {
        return $this->belongsTo(SuratJalan::class, 'reference_id', 'id');
    }
}
