<?php

namespace App\Jobs;

use App\Models\Production;
use App\Models\ProductionHouse;
use App\Models\ProductionPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductionHouseBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('UpdateProductionHouseBalance job started.');

        $houses = ProductionHouse::all();

        foreach ($houses as $house) {
            Log::info("Processing Production House ID: {$house->id}");

            // Get the total sales and payments for each customer
            $totalProduction = Production::where('house_id', $house->id)
                ->whereNull('deleted_at') // Optional: ensure no deleted records are included
                ->sum('net_total');

            $totalProductionPaid = Production::where('house_id', $house->id)
                ->whereNull('deleted_at') // Optional: ensure no deleted records are included
                ->sum('amount');

            $totalPayments = ProductionPayment::where('house_id', $house->id)
                ->whereNull('deleted_at') // Optional: ensure no deleted records are included
                ->sum('amount');

            $totalRefunds = ProductionPayment::where('house_id', $house->id)
                ->whereNull('deleted_at') // Optional: ensure no deleted records are included
                ->sum('amount');

            // Calculate the new balance
            $newBalance = ($totalRefunds + $totalProduction) - $totalProductionPaid - $totalPayments;

            Log::info("House ID: {$house->id} - Total Production: {$totalProduction}, Total Paid: {$totalProductionPaid}, Total Payments: {$totalPayments}, Total Refunds: {$totalRefunds}, New Balance: {$newBalance}");

            // Update the customer's balance
            $house->balance = $newBalance;
            $house->save();

            Log::info("Updated balance for House ID: {$house->id} to {$newBalance}");
        }

        Log::info('UpdateProductionHouseBalance job completed.');
    }
}
