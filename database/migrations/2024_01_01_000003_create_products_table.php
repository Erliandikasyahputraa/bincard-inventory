<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->unsignedInteger('min_stock')->default(0);
            $table->unsignedInteger('max_stock')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('current_stock')->default(0);
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->index('barcode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
