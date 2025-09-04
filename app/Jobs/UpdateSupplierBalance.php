<?php

namespace App\Jobs;

use App\Models\RawMaterialPurchase;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierRefund;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateSupplierBalance implements ShouldQueue
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
        Log::info('UpdateSupplierBalance job started.');

        $suppliers = Supplier::all();

        foreach ($suppliers as $supplier) {
            Log::info("Processing Supplier ID: {$supplier->id}");

            // Get the total purchases and payments for each supplier
            $totalPurchases = RawMaterialPurchase::where('supplier_id', $supplier->id)
                ->where('status', 'approved') // Optional: ensure no deleted records are included
                ->sum('net_total');

            $totalPurchasesPaid = RawMaterialPurchase::where('supplier_id', $supplier->id)
                ->where('status', 'approved') // Optional: ensure no deleted records are included
                ->sum('amount');

            $totalPayments = SupplierPayment::where('supplier_id', $supplier->id)
                ->where('status', 'approved')
                ->sum('amount');

            $totalRefunds = SupplierRefund::where('supplier_id', $supplier->id)
                ->where('status', 'approved')
                ->sum('amount');

            // Calculate the new balance
            $newBalance = ($totalRefunds + $totalPurchases) - $totalPurchasesPaid - $totalPayments;

            Log::info("Supplier ID: {$supplier->id} - Total Purchases: {$totalPurchases}, Total Paid: {$totalPurchasesPaid}, Total Payments: {$totalPayments}, Total Refunds: {$totalRefunds}, New Balance: {$newBalance}");

            // Update the supplier's balance
            $supplier->balance = $newBalance;
            $supplier->save();

            Log::info("Updated balance for Supplier ID: {$supplier->id} to {$newBalance}");
        }

        Log::info('UpdateSupplierBalance job completed.');
    }
}
