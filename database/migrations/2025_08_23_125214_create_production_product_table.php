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
        Schema::create('production_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_id');
            $table->unsignedBigInteger('product_id');
            $table->foreignId('brand_id');
            $table->foreignId('size_id');
            $table->foreignId('color_id');
            $table->double('per_pc_cost',16,2);
            $table->double('quantity',16,2);
            $table->double('sub_total',16,2);
            $table->timestamps();

            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_product');
    }
};
