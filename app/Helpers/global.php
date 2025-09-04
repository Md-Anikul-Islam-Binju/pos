<?php

use App\Models\Sell;
use App\Models\SellStock;
use App\Models\RawMaterialStock;
use App\Models\RawMaterialPurchase;

if (!function_exists('canStatusChangeFromApprove')) {
    /**
     * Check if the status can be changed from "approved"
     *
     * @param \App\Models\RawMaterialPurchase $purchase
     * @return bool
     */
    function canRawMaterialPurchaseStatusChangeFromApprove(RawMaterialPurchase $purchase): bool
    {
        foreach ($purchase->raw_materials as $rawMaterial) {
            $existingStock = RawMaterialStock::where('raw_material_id', $rawMaterial->pivot->raw_material_id)
                ->where('price', $rawMaterial->pivot->price)
                ->where('brand_id', $rawMaterial->pivot->brand_id)
                ->where('size_id', $rawMaterial->pivot->size_id)
                ->where('color_id', $rawMaterial->pivot->color_id)
                ->where('warehouse_id', $purchase->warehouse_id)
                ->first();

            if ($existingStock) {
                // Adjust the stock by subtracting the purchased quantity
                $existingStock->quantity -= $rawMaterial->pivot->quantity;
                if ($existingStock->quantity < 0) {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('getDefaultCurrencyConvertedPrice')) {

    function getDefaultCurrencyConvertedPrice($model)
    {
        // Fetch the default currency
        $defaultCurrency = Currency::where('is_default', true)->first();

        if (!$defaultCurrency) {
            // If no default currency is found, return null
            return null;
        }

        // Initialize the result array
        $convertedData = [];

        // If the model is a Sell, convert the required fields
        if ($model instanceof Sell) {
            $currency = $model->currency;
            $convertedData['total_amount'] = convertToDefaultCurrency($model->total_amount, $currency, $defaultCurrency);
            $convertedData['discount_amount'] = convertToDefaultCurrency($model->discount_amount, $currency, $defaultCurrency);
            $convertedData['net_total'] = convertToDefaultCurrency($model->net_total, $currency, $defaultCurrency);
            $convertedData['paid_amount'] = convertToDefaultCurrency($model->paid_amount, $currency, $defaultCurrency);
        }
        // If the model is a SellStock, convert the required fields
        elseif ($model instanceof SellStock) {
            $currency = $model->currency;
            $convertedData['price'] = convertToDefaultCurrency($model->price, $currency, $defaultCurrency);
            $convertedData['discount_amount'] = convertToDefaultCurrency($model->discount_amount, $currency, $defaultCurrency);
            $convertedData['total'] = convertToDefaultCurrency($model->total, $currency, $defaultCurrency);
        } else {
            return null;
        }
        return $convertedData;
    }
}

if (!function_exists('convertToDefaultCurrency')) {
    function convertToDefaultCurrency($amount, $currency, $defaultCurrency)
    {
        // If the currency is the same as the default currency, return the original amount
        if ($currency->id === $defaultCurrency->id) {
            return $amount;
        }
        // Get the conversion rate from the original currency to the default currency
        $conversionRate = $currency->rate;

        // If the rate is available, convert and return the amount
        if ($conversionRate) {
            return $amount * $conversionRate;
        }

        // If no conversion rate is found, return null
        return null;
    }
}
