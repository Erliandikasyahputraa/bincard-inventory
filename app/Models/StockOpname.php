<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    protected $fillable = [
        'tanggal_opname',
        'status',
        'created_by',
        'closed_at',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
        'closed_at' => 'datetime',
    ];

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SELESAI = 'selesai';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(StockOpnameDetail::class, 'stock_opname_id');
    }
}
