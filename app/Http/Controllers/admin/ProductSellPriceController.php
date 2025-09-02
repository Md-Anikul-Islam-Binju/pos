<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ProductSellPrice;
use Illuminate\Http\Request;

class ProductSellPriceController extends Controller
{
    public function getSellPriceData($stockId)
    {
        $currencies = Currency::where('status', true)->get();
        $productSellPriceList = ProductSellPrice::where('product_stock_id', $stockId)->with(['currency'])->get();
        $sellPrice = ProductSellPrice::where('product_stock_id', $stockId)->first();

        return response()->json([
            'currencies' => $currencies,
            'productSellPriceList' => $productSellPriceList,
            'sellPrice' => $sellPrice ? $sellPrice->sell_price : null,
        ]);
    }

    public function updateSellPrice(Request $request, $stockId)
    {
        try {
            $validatedData = $request->validate([
                'currency_id' => ['required', 'exists:currencies,id'],
                'sell_price'  => ['required', 'numeric', 'min:0'],
            ]);

            ProductSellPrice::updateOrCreate(
                [
                    'product_stock_id' => $stockId,
                    'currency_id'      => $validatedData['currency_id'],
                ],
                ['sell_price' => $validatedData['sell_price']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Sell price updated successfully.'.$request->sell_price.$request->currency_id,
            ] );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sell price. Please try again later.',
            ]);
        }
    }
}
