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
        Schema::create('production_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id');
            $table->foreignId('raw_material_id');
            $table->foreignId('brand_id');
            $table->foreignId('size_id');
            $table->foreignId('color_id');
            $table->foreignId('warehouse_id');
            $table->decimal('price', 16, 2);
            $table->double('quantity',16,2);
            $table->double('total_price', 15, 2);
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_raw_materials');
    }
};
