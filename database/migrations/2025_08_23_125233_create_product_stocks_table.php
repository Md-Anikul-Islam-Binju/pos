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
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->double('quantity',16,2);
            $table->double('production_cost_price',16,2);
            $table->double('avg_cost_price',16,2);
            $table->double('total_cost_price',16,2);
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('size_id');
            $table->unsignedBigInteger('showroom_id');
            $table->timestamps();

            // Assuming the referenced tables are `colors`, `brands`, and `sizes`
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
            $table->foreign('showroom_id')->references('id')->on('showrooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stocks');
    }
};
