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
        Schema::create('product_stock_transfer_product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_stock_transfer_id');
            $table->foreignId('product_stock_id');
            $table->double('quantity',16,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_transfer_product_stock');
    }
};
