<?php

namespace App\Observers;

use App\Models\Production;
use App\Models\ProductStock;
use App\Models\RawMaterialStock;
use Illuminate\Support\Facades\Log;

class ProductionObserver
{
    public function updated(Production $production)
    {
        // Check if the status has changed
        if ($production->isDirty('status')) {
            $oldStatus = $production->getOriginal('status');
            $totalProductQuantity = $production->products->sum('quantity');
            $totalCostExceptProduct = $production->total_raw_material_cost + $production->total_cost;
            $avgProductCost = $totalProductQuantity / $totalCostExceptProduct;
            if ($production->status == 'approved') {
                Log::info("updated observer working status approved");
                Log::info($production);
                Log::info($production->products);
                // Process each raw material in the purchase
                foreach ($production->rawMaterials as $rawMaterial) {
                    // Check if there is an existing stock entry for the same product, price, and warehouse
                    $existingRawMaterialStock = RawMaterialStock::where('raw_material_id', $rawMaterial->raw_material_id)
                        ->where('price', $rawMaterial->price)
                        ->where('brand_id', $rawMaterial->brand_id)
                        ->where('size_id', $rawMaterial->size_id)
                        ->where('color_id', $rawMaterial->color_id)
                        ->where('warehouse_id', $rawMaterial->warehouse_id)
                        ->first();
                    if ($existingRawMaterialStock) {
                        // If stock entry exists, update the quantity
                        $existingRawMaterialStock->quantity -= $rawMaterial->quantity;
                        $existingRawMaterialStock->save();
                    } else {
                        // If no stock entry exists, create a new one
                        RawMaterialStock::create([
                            'raw_material_id' => $rawMaterial->raw_material_id,
                            'brand_id' => $rawMaterial->brand_id,
                            'size_id' => $rawMaterial->size_id,
                            'color_id' => $rawMaterial->color_id,
                            'quantity' => $rawMaterial->quantity * -1,
                            'price' => $rawMaterial->price,
                            'warehouse_id' => $rawMaterial->warehouse_id,
                        ]);
                    }
                }

                foreach ($production->products as $product) {
                    // Check if there is an existing stock entry for the same product, price, and warehouse
                    $existingProductStock = ProductStock::where('product_id', $product->product_id)
                        ->where('production_cost_price', $product->per_pc_cost)
                        ->where('avg_cost_price', $avgProductCost)
                        ->where('total_cost_price', $avgProductCost + $product->per_pc_cost)
                        ->where('brand_id', $product->brand_id)
                        ->where('size_id', $product->size_id)
                        ->where('color_id', $product->color_id)
                        ->where('showroom_id', $production->showroom_id)
                        ->first();
                    if ($existingProductStock) {
                        // If stock entry exists, update the quantity
                        $existingProductStock->quantity += $product->quantity;
                        $existingProductStock->save();
                    } else {
                        // If no stock entry exists, create a new one
                        ProductStock::create([
                            'product_id' => $product->product_id,
                            'quantity' => $product->quantity,
                            'production_cost_price' => $product->per_pc_cost,
                            'avg_cost_price' => $avgProductCost,
                            'total_cost_price' => $avgProductCost + $product->per_pc_cost,
                            'color_id' => $product->color_id,
                            'brand_id' => $product->brand_id,
                            'size_id' => $product->size_id,
                            'showroom_id' => $production->showroom_id,
                        ]);
                    }
                }
            }

            if ($this->statusChangeRequirement($oldStatus, $production->status)) {
                // Adjust the stock for rejected production
                foreach ($production->rawMaterials as $rawMaterial) {
                    $existingRawMaterialStock = RawMaterialStock::where('raw_material_id', $rawMaterial->raw_material_id)
                        ->where('price', $rawMaterial->price)
                        ->where('brand_id', $rawMaterial->brand_id)
                        ->where('size_id', $rawMaterial->size_id)
                        ->where('color_id', $rawMaterial->color_id)
                        ->where('warehouse_id', $rawMaterial->warehouse_id)
                        ->first();
                    if ($existingRawMaterialStock) {
                        // Adjust the stock by subtracting the purchased quantity
                        $existingRawMaterialStock->quantity += $rawMaterial->quantity;
                        $existingRawMaterialStock->save();
                    } else {
                        RawMaterialStock::create([
                            'raw_material_id' => $rawMaterial->raw_material_id,
                            'brand_id' => $rawMaterial->brand_id,
                            'size_id' => $rawMaterial->size_id,
                            'color_id' => $rawMaterial->color_id,
                            'quantity' => $rawMaterial->quantity,
                            'price' => $rawMaterial->price,
                            'warehouse_id' => $rawMaterial->warehouse_id,
                        ]);
                    }
                }

                foreach ($production->products as $product) {
                    $existingProductStock = ProductStock::where('product_id', $product->product_id)
                        ->where('production_cost_price', $product->per_pc_cost)
                        ->where('avg_cost_price', $avgProductCost)
                        ->where('total_cost_price', $avgProductCost + $product->per_pc_cost)
                        ->where('brand_id', $product->brand_id)
                        ->where('size_id', $product->size_id)
                        ->where('color_id', $product->color_id)
                        ->where('showroom_id', $production->showroom_id)
                        ->first();
                    if ($existingProductStock) {
                        // Adjust the stock by subtracting the purchased quantity
                        $existingProductStock->quantity -= $product->quantity;
                        $existingProductStock->save();
                    } else {
                        ProductStock::create([
                            'product_id' => $product->product_id,
                            'quantity' => $product->quantity * -1,
                            'production_cost_price' => $product->per_pc_cost,
                            'avg_cost_price' => $avgProductCost,
                            'total_cost_price' => $avgProductCost + $product->per_pc_cost,
                            'color_id' => $product->color_id,
                            'brand_id' => $product->brand_id,
                            'size_id' => $product->size_id,
                            'showroom_id' => $production->showroom_id,
                        ]);
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
