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
        Schema::create('account_transaction', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('date')->nullable();
            $table->string('status')->nullable();
            $table->enum('type', ['Deposit', 'Withdraw', 'Expense', 'In', 'Out'])->nullable();
            $table->string('transaction_id')->unique()->nullable();
            $table->string('unique_id')->nullable();
            $table->timestamps();

            // Ensure the combination of account_id and unique_id is unique
            $table->unique(['account_id', 'unique_id', 'type']);

            // Set up foreign key constraint if the account table exists
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transaction');
    }
};
