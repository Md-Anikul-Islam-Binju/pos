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
        Schema::create('product_sell_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_stock_id')->constrained('product_stocks')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->double('sell_price',16,2)->comment('Sell price of the product in the specified currency');
            $table->timestamps();

            // Ensure that each product stock has a unique sell price for each currency
            $table->unique(['product_stock_id', 'currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sell_prices');
    }
};
