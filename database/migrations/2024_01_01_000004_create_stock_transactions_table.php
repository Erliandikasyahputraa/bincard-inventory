<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['IN', 'OUT', 'ADJUST']);
            $table->integer('quantity'); // signed: positif = masuk, negatif = keluar
            $table->string('reference_id')->nullable(); // Surat Jalan ID / SO ID
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
