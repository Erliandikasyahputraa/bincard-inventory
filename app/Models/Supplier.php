<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Supplier extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'suppliers';

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'email',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }
}
