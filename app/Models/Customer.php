<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'customers';

    protected $fillable = [
        'nama',
        'alamat',
        'telepon',
        'email',
    ];

    public function suratJalan(): HasMany
    {
        return $this->hasMany(SuratJalan::class, 'customer_id');
    }
}
