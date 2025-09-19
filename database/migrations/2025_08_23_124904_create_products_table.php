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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->text('details')->nullable();
            $table->text('short_details')->nullable();
            $table->string('sku')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->double('width',16,2)->nullable();
            $table->double('length',16,2)->nullable();
            $table->double('density',16.2)->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
