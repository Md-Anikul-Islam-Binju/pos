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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->double('balance', 15,2)->nullable()->default(0);
            $table->date('production_date');
            $table->decimal('total_cost', 15, 2);
            $table->json('cost_details')->nullable();
            $table->decimal('total_raw_material_cost', 15, 2);
            $table->decimal('total_product_cost', 15, 2);
            $table->decimal('net_total', 15, 2)->nullable()->default(0);
            $table->decimal('amount', 15, 2)->default(0)->comment('Paid amount');
            $table->string('payment_type')->default('full_paid');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('house_id')->nullable()->constrained('production_houses')->nullOnDelete();
            $table->foreignId('showroom_id')->nullable()->constrained('showrooms')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
