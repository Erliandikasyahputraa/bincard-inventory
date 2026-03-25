<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom Unit of Measure (UoM) ke tabel products.
     * Menggunakan VARCHAR untuk fleksibilitas — tidak terikat ENUM
     * agar tidak perlu migration lagi ketika ada tambahan UoM baru.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('uom', 10)->default('PCS')->after('location')
                  ->comment('Unit of Measure: PCS, SET, KG, BOX, BTG, BTL, etc.');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('uom');
        });
    }
};
