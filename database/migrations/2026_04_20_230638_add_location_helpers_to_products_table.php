<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('loc_aisle')->nullable()->after('location')->index();
            $table->string('loc_floor')->nullable()->after('loc_aisle')->index();
            $table->string('loc_row')->nullable()->after('loc_floor')->index();
            $table->string('loc_col')->nullable()->after('loc_row')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['loc_aisle', 'loc_floor', 'loc_row', 'loc_col']);
        });
    }
};
