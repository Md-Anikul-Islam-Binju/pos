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
        Schema::create('cron_job_logs', function (Blueprint $table) {
            $table->id();
            $table->string('command')->comment('The cron job command executed');
            $table->string('status')->default('pending')->comment('Status of the cron job execution');
            $table->timestamp('executed_at')->nullable()->comment('Timestamp when the cron job was executed');
            $table->text('output')->nullable()->comment('Output or result of the cron job execution');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_job_logs');
    }
};
