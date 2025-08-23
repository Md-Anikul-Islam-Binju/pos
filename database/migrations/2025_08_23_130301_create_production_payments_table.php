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
        Schema::create('production_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('house_id')->nullable();
            $table->double('amount');
            $table->unsignedBigInteger('account_id');
            $table->date('date')->nullable();
            $table->string('received_by')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_payments');
    }
};
