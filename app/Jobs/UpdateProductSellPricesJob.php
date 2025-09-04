<?php

namespace App\Jobs;

use App\Models\Currency;
use App\Models\ProductSellPrice;
use App\Models\ProductStock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductSellPricesJob implements ShouldQueue
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
    public function handle()
    {

        // Fetch the default currency
        $defaultCurrency = Currency::where('is_default', true)->first();

        if (!$defaultCurrency) {
            Log::error("Default currency not found. Cannot update product sell prices.");
            return;
        }

        // Fetch all non-default currencies
        $currencies = Currency::where('is_default', false)->get();

        if ($currencies->isEmpty()) {
            Log::info("No additional currencies found. No sell prices to update.");
            return;
        }

        // Process ProductStocks in chunks to optimize memory usage
        ProductStock::with('product_sell_prices')->chunk(100, function ($productStocks) use ($defaultCurrency, $currencies) {
            foreach ($productStocks as $productStock) {
                // Retrieve the sell price in the default currency
                $defaultSellPrice = $productStock->product_sell_prices
                    ->where('currency_id', $defaultCurrency->id)
                    ->first();

                if (!$defaultSellPrice) {
                    Log::warning("Default sell price not found for ProductStock ID: {$productStock->id}");
                    continue; // Skip if no default sell price is available
                }

                // Loop through each non-default currency to update or create sell prices
                foreach ($currencies as $currency) {
                    // Calculate the new sell price based on the exchange rate
                    // Assuming that the default currency rate is relative to a base rate (e.g., USD)
                    // Adjust the calculation as per your specific requirements
                    $exchangeRate = $currency->rate / $defaultCurrency->rate;
                    $newSellPrice = round($defaultSellPrice->sell_price * $exchangeRate, 2); // Rounded to 2 decimal places

                    // Update or create the sell price for the current currency
                    ProductSellPrice::updateOrCreate(
                        [
                            'product_stock_id' => $productStock->id,
                            'currency_id' => $currency->id,
                        ],
                        [
                            'sell_price' => $newSellPrice,
                        ]
                    );
                }
            }
        });

        Log::info("Product sell prices updated successfully based on the default currency rate.");
    }
}
