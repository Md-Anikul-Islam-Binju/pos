<?php

namespace App\Observers;

use App\Models\RawMaterialPurchase;
use App\Models\RawMaterialStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RawMaterialPurchaseObserver
{
    public function updated(RawMaterialPurchase $purchase)
    {
        // Check if the status has changed
        if ($purchase->isDirty('status')) {
            $oldStatus = $purchase->getOriginal('status');
            if ($purchase->status == 'approved') {
                Log::info("updated observer working status approved");
                Log::info($purchase);
                Log::info($purchase->rawMaterials);

                // Process each raw material in the purchase
                foreach ($purchase->raw_materials as $rawMaterial) {
                    // Check if there is an existing stock entry for the same product, price, and warehouse
                    $existingStock = RawMaterialStock::where('raw_material_id', $rawMaterial->pivot->raw_material_id)
                        ->where('price', $rawMaterial->pivot->price)
                        ->where('brand_id', $rawMaterial->pivot->brand_id)
                        ->where('size_id', $rawMaterial->pivot->size_id)
                        ->where('color_id', $rawMaterial->pivot->color_id)
                        ->where('warehouse_id', $purchase->warehouse_id)
                        ->first();
                    if ($existingStock) {
                        // If stock entry exists, update the quantity
                        $existingStock->quantity += $rawMaterial->pivot->quantity;
                        $existingStock->save();
                    } else {
                        // If no stock entry exists, create a new one
                        RawMaterialStock::create([
                            'raw_material_id' => $rawMaterial->pivot->raw_material_id,
                            'brand_id' => $rawMaterial->pivot->brand_id,
                            'size_id' => $rawMaterial->pivot->size_id,
                            'color_id' => $rawMaterial->pivot->color_id,
                            'quantity' => $rawMaterial->pivot->quantity,
                            'price' => $rawMaterial->pivot->price,
                            'warehouse_id' => $purchase->warehouse_id,
                        ]);
                    }
                }
            }

            if ($this->statusChangeRequirement($oldStatus, $purchase->status)) {
                // Adjust the stock for rejected purchase
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
                        $existingStock->save();

                    }
                }

            }

        }

    }
    private function statusChangeRequirement($originalStatus, $newStatus): bool
    {
        if ($originalStatus === 'approved' && ($newStatus === 'pending' || $newStatus === 'rejected')) {
            return true; // Handle rejections or pending status
        }
        return false;
    }
}

