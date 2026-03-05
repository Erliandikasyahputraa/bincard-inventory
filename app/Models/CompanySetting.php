<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'alamat',
        'telepon',
        'email',
        'logo_path',
    ];
}

