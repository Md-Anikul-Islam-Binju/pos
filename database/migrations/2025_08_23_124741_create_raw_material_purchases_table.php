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
        Schema::create('raw_material_purchases', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date')->nullable();
            $table->json('cost_details')->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->decimal('net_total', 15, 2)->nullable();
            $table->decimal('amount', 15, 2)->default(0)->comment('Paid amount');
            $table->string('payment_type')->default('full_paid');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_material_purchases');
    }
};
