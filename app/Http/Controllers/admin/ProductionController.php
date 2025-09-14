<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Production;
use App\Models\ProductionHouse;
use App\Models\RawMaterialStock;
use App\Models\Showroom;
use App\Models\Size;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;
use App\Models\Product;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('production-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $production = Production::all();
        $productionHouse = ProductionHouse::all();
        $showroom = Showroom::all();
        $account = Account::all();
        $warehouse = Warehouse::all();
        $brand = Brand::all();
        $color = Color::all();
        $size = Size::all();
        $product = Product::all();
        $rawMaterialStock = RawMaterialStock::all();
        return view('admin.pages.production.index',
            compact('production', 'productionHouse', 'showroom',
                'account', 'warehouse', 'brand', 'color', 'size', 'product', 'rawMaterialStock'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'production_house_id' => 'required',
                'showroom_id' => 'required',
                'account_id' => 'required',
                'production_date' => 'required',
                'warehouse_id' => 'required',
            ]);

            $costDetails = $request->input('cost_details', []);
            $costAmounts = $request->input('cost_amount', []);

            $combinedCosts = [];
            $totalCost = 0;

            foreach ($costDetails as $index => $detail) {
                $amount = isset($costAmounts[$index]) ? $costAmounts[$index] : null;
                if ($detail && $amount) {
                    $combinedCosts[] = [
                        'detail' => $detail,
                        'amount' => $amount
                    ];
                    $totalCost += $amount;
                }
            }

            $totalRawMaterialCost = 0;
            $totalProductCost = 0;

            $production = new Production();
            $production->production_house_id = $request->production_house_id;
            $production->showroom_id = $request->showroom_id;
            $production->account_id = $request->account_id;
            $production->production_date = $request->production_date;
            $production->cost_details = json_encode($combinedCosts);
            $production->total_cost = $totalCost;
            $production->total_raw_material_cost = 0;   // Will be updated later
            $production->total_product_cost = 0;   // Will be updated later
            $production->amount = $totalCost + 0;   // Will be updated later
            $production->save();

            foreach ($request->raw_material_id as $index => $rawMaterial) {
                $price = isset($request->raw_material_price[$index]) && is_numeric($request->raw_material_price[$index])
                    ? (double) $request->raw_material_price[$index] : 0;

                $quantity = isset($request->raw_material_quantity[$index]) && is_numeric($request->raw_material_quantity[$index])
                    ? (double) $request->raw_material_quantity[$index] : 0;

                $totalPrice = isset($request->raw_material_total_price[$index]) && is_numeric($request->raw_material_total_price[$index])
                    ? (double) $request->raw_material_total_price[$index] : 0;

                DB::table('production_raw_materials')->insert([
                    'production_id' => $production->id,
                    'raw_material_id' => $rawMaterial,
                    'brand_id' => $request->raw_material_brand_id[$index] ?? null,
                    'size_id' => $request->raw_material_size_id[$index] ?? null,
                    'color_id' => $request->raw_material_color_id[$index] ?? null,
                    'warehouse_id' => $request->raw_material_warehouse_id[$index] ?? null,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ]);
            }


            foreach ($request->product_id as $index => $product) {
                $perPcCost = isset($request->price[$index]) && is_numeric($request->price[$index])
                    ? (double) $request->price[$index] : 0;

                $quantity = isset($request->quantity[$index]) && is_numeric($request->quantity[$index])
                    ? (double) $request->quantity[$index] : 0;

                $subTotal = isset($request->total_price[$index]) && is_numeric($request->total_price[$index])
                    ? (double) $request->total_price[$index] : 0;

                DB::table('production_product')->insert([
                    'production_id' => $production->id,
                    'product_id' => $product,
                    'brand_id' => $request->brand_id[$index] ?? null,
                    'size_id' => $request->size_id[$index] ?? null,
                    'color_id' => $request->color_id[$index] ?? null,
                    'per_pc_cost' => $perPcCost,
                    'quantity' => $quantity,
                    'sub_total' => $subTotal,
                ]);
            }


            $totalRawMaterialCost = DB::table('production_raw_materials')
                ->where('production_id', $production->id)
                ->sum('total_price');

            $totalProductCost = DB::table('production_product')
                ->where('production_id', $production->id)
                ->sum('sub_total');

            // Update the total_price and amount in the purchases table
            $totalForProduction = $totalCost + $totalRawMaterialCost + $totalProductCost;

            $production->update([
                'total_raw_material_cost' => $totalRawMaterialCost, // Update amount
                'total_product_cost' => $totalProductCost, // Update amount
                'net_total' => $totalForProduction,
                'payment_type' => $request->payment_type,
                'amount' => $request->payment_type == 'full_paid' ? $totalForProduction : $request->paid_amount
            ]);

            Toastr::success('Production Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'production_house_id' => 'required',
                'showroom_id' => 'required',
                'account_id' => 'required',
                'production_date' => 'required',
                'warehouse_id' => 'required',
            ]);

            $production = Production::findOrFail($id);

            $costDetails = $request->input('cost_details', []);
            $costAmounts = $request->input('cost_amount', []);

            $combinedCosts = [];
            $totalCost = 0;

            foreach ($costDetails as $index => $detail) {
                $amount = isset($costAmounts[$index]) ? $costAmounts[$index] : null;
                if ($detail && $amount) {
                    $combinedCosts[] = [
                        'detail' => $detail,
                        'amount' => $amount
                    ];
                    $totalCost += $amount;
                }
            }

            $production->update([
                'production_house_id' => $request->production_house_id,
                'showroom_id' => $request->showroom_id,
                'account_id' => $request->account_id,
                'production_date' => $request->production_date,
                'cost_details' => json_encode($combinedCosts),
                'total_cost' => $totalCost,
            ]);

            $existingRawMaterials = DB::table('production_raw_materials')
                ->where('production_id', $production->id)
                ->get();

            $newRawMaterials = $request->raw_material_id ?? [];

            foreach ($newRawMaterials as $index => $rawMaterial) {
                $price = isset($request->raw_material_price[$index]) && is_numeric($request->raw_material_price[$index])
                    ? (double) $request->raw_material_price[$index] : 0;

                $quantity = isset($request->raw_material_quantity[$index]) && is_numeric($request->raw_material_quantity[$index])
                    ? (double) $request->raw_material_quantity[$index] : 0;

                $totalPrice = isset($request->raw_material_total_price[$index]) && is_numeric($request->raw_material_total_price[$index])
                    ? (double) $request->raw_material_total_price[$index] : 0;

                $data = [
                    'raw_material_id' => $rawMaterial,
                    'brand_id' => $request->raw_material_brand_id[$index] ?? null,
                    'size_id' => $request->raw_material_size_id[$index] ?? null,
                    'color_id' => $request->raw_material_color_id[$index] ?? null,
                    'warehouse_id' => $request->raw_material_warehouse_id[$index] ?? null,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice,
                ];

                $existingMaterial = $existingRawMaterials->firstWhere('raw_material_id', $rawMaterial);

                if ($existingMaterial) {
                    DB::table('production_raw_materials')->where('id', $existingMaterial->id)->update($data);
                } else {
                    $data['production_id'] = $production->id; // Add foreign key
                    DB::table('production_raw_materials')->insert($data);
                }
            }

            $existingRawMaterialIds = $existingRawMaterials->pluck('raw_material_id')->toArray();
            $newRawMaterialIds = array_filter($newRawMaterials);
            $removedRawMaterials = array_diff($existingRawMaterialIds, $newRawMaterialIds);

            if (!empty($removedRawMaterials)) {
                DB::table('production_raw_materials')
                    ->where('production_id', $production->id)
                    ->whereIn('raw_material_id', $removedRawMaterials)
                    ->delete();
            }

            $existingProducts = DB::table('production_product')
                ->where('production_id', $production->id)
                ->get();

            $newProducts = $request->product_id ?? [];

            foreach ($newProducts as $index => $product) {
                $perPcCost = isset($request->price[$index]) && is_numeric($request->price[$index])
                    ? (double) $request->price[$index] : 0;

                $quantity = isset($request->quantity[$index]) && is_numeric($request->quantity[$index])
                    ? (double) $request->quantity[$index] : 0;

                $subTotal = isset($request->total_price[$index]) && is_numeric($request->total_price[$index])
                    ? (double) $request->total_price[$index] : 0;

                $productData = [
                    'product_id' => $product,
                    'brand_id' => $request->brand_id[$index] ?? null,
                    'size_id' => $request->size_id[$index] ?? null,
                    'color_id' => $request->color_id[$index] ?? null,
                    'per_pc_cost' => $perPcCost,
                    'quantity' => $quantity,
                    'sub_total' => $subTotal,
                ];

                $existingProduct = $existingProducts->firstWhere('product_id', $product);

                if ($existingProduct) {
                    DB::table('production_product')->where('id', $existingProduct->id)->update($productData);
                } else {
                    $productData['production_id'] = $production->id; // Add foreign key
                    DB::table('production_product')->insert($productData);
                }
            }

            $existingProductIds = $existingProducts->pluck('product_id')->toArray();
            $newProductIds = array_filter($newProducts);
            $removedProducts = array_diff($existingProductIds, $newProductIds);

            if (!empty($removedProducts)) {
                DB::table('production_product')
                    ->where('production_id', $production->id)
                    ->whereIn('product_id', $removedProducts)
                    ->delete();
            }

            $totalRawMaterialCost = DB::table('production_raw_materials')
                ->where('production_id', $production->id)
                ->sum('total_price');

            $totalProductCost = DB::table('production_product')
                ->where('production_id', $production->id)
                ->sum('sub_total');

            $totalForProduction = $totalCost + $totalRawMaterialCost + $totalProductCost;

            $production->total_raw_material_cost = $totalRawMaterialCost;
            $production->total_product_cost = $totalProductCost;
            $production->net_total = $totalForProduction;
            $production->payment_type = $request->payment_type;
            $production->amount = $request->payment_type == "full_paid" ? $totalForProduction : $request->paid_amount;
            $production->save();

            Toastr::success('Production Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $production = Production::find($id);
            $production->delete();

            Toastr::success('Production Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getRawMaterialsByWarehouse(Request $request): JsonResponse
    {
        $warehouseId = $request->get('warehouse_id');
        $rawMaterials = RawMaterialStock::where('warehouse_id', $warehouseId)->with('raw_material.category')->get();
        return response()->json($rawMaterials);
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('production.section')->with('error', 'Invalid status.');
        }

        $production = Production::find($id);

        if (!$production) {
            return redirect()->back()->with('error', 'Production Information not found.');
        }

        $production->status = $status;
        $production->update();

        return redirect()->back()->with('success', 'Production status updated successfully.');
    }

    public function printProduction($id)
    {
        $production = Production::findOrFail($id);
        return view('admin.pages.production.invoice', compact('production'));
    }
}
