<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratJalan extends Model
{
    use HasFactory;

    protected $table = 'surat_jalan';

    protected $fillable = [
        'nomor_surat_jalan',
        'customer_id',
        'tanggal',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SuratJalanDetail::class, 'surat_jalan_id');
    }
}
