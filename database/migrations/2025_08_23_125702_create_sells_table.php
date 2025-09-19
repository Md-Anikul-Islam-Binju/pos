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
        Schema::create('sells', function (Blueprint $table) {
            $table->id();
            $table->string('unique_sale_id')->unique()->nullable();
            $table->foreignId('customer_id');
            $table->foreignId('salesman_id');
            $table->foreignId('account_id');
            $table->foreignId('currency_id');
            $table->double('total_amount', 16, 2)->default(0);
            $table->double('discount_amount', 16, 2)->default(0);
            $table->double('net_total', 16, 2)->default(0);
            $table->double('paid_amount', 16, 2)->default(0);
            $table->string('status')->default('approved');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sells');
    }
};
