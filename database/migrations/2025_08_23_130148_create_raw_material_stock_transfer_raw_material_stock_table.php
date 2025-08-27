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
        Schema::create('raw_material_stock_transfer_raw_material_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_stock_transfer_id');
            $table->foreignId('raw_material_stock_id');
            $table->double('quantity',16,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_stock_transfer_raw_material_stock');
    }
};
