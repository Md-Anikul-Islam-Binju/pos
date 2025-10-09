<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminActivity;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionHouse;
use App\Models\RawMaterial;
use App\Models\RawMaterialStock;
use App\Models\Showroom;
use App\Models\Size;
use App\Models\Warehouse;
use Illuminate\Console\Application;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\Production,production')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        $productions = Production::orderBy('id', 'DESC')->get();
        return view('admin.pages.production.index', compact('productions'));
    }

    public function create(): View|Factory|Application
    {
        $houses = ProductionHouse::all();
        $showrooms = Showroom::all();
        $accounts  = Account::all();
        $warehouses = Warehouse::all();
        $brands = Brand::all();
        $colors = Color::all();
        $sizes = Size::all();
        $rawMaterialStocks = RawMaterialStock::all();
        return view('admin.pages.production.create',
            compact('houses', 'showrooms', 'accounts', 'warehouses', 'rawMaterialStocks', 'brands', 'colors', 'sizes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'production_house_id' => 'required',
            'showroom_id' => 'required',
            'account_id' => 'required',
            'production_date' => 'required',
            'warehouse_id' => 'required',
        ]);
        // Retrieve cost details and amounts
        $costDetails = $request->input('cost_details', []);
        $costAmounts = $request->input('cost_amount', []);
        // Combine into a JSON array
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
        $production = Production::create([
            'production_house_id' => $request->production_house_id,
            'showroom_id' => $request->showroom_id,
            'account_id' => $request->account_id,
            'production_date' => $request->production_date,
            'cost_details' => json_encode($combinedCosts),
            'total_cost' => $totalCost,
            'total_raw_material_cost' => 0, // Will be updated later
            'total_product_cost' => 0, // Will be updated later
            'amount' => $totalCost + 0, // Will be updated later
        ]);

        //dd($request->raw_material_id);
        foreach ($request->raw_material_id as $index => $rawMaterial) {
            DB::table('production_raw_materials')->insert([
                'production_id' => $production->id,
                'raw_material_id' => $rawMaterial,
                'brand_id' => isset($request->raw_material_brand_id[$index]) ? $request->raw_material_brand_id[$index] : null,
                'size_id' => isset($request->raw_material_size_id[$index]) ? $request->raw_material_size_id[$index] : null,
                'color_id' => isset($request->raw_material_color_id[$index]) ? $request->raw_material_color_id[$index] : null,
                'warehouse_id' => isset($request->raw_material_warehouse_id[$index]) ? $request->raw_material_warehouse_id[$index] : null,
                'price' => isset($request->raw_material_price[$index]) ? $request->raw_material_price[$index] : 0,
                'quantity' => isset($request->raw_material_quantity[$index]) ? $request->raw_material_quantity[$index] : 0,
                'total_price' => isset($request->raw_material_total_price[$index]) ? (double) $request->raw_material_total_price[$index] : 0,
            ]);
        }
        foreach ($request->product_id as $index => $product) {
            DB::table('production_product')->insert([
                'production_id' => $production->id,
                'product_id' => $product,
                'brand_id' => isset($request->brand_id[$index]) ? $request->brand_id[$index] : null,
                'size_id' => isset($request->size_id[$index]) ? $request->size_id[$index] : null,
                'color_id' => isset($request->color_id[$index]) ? $request->color_id[$index] : null,
                'per_pc_cost' => isset($request->price[$index]) ? $request->price[$index] : 0,
                'quantity' => isset($request->quantity[$index]) ? $request->quantity[$index] : 0,
                'sub_total' => isset($request->total_price[$index]) ? (double) $request->total_price[$index] : 0,
            ]);
        }
        // Calculate the total price
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
            'total_product_cost' => $totalProductCost,// Update amount
            'net_total' => $totalForProduction,
            'payment_type'=>$request->payment_type,
            'amount' => $request->payment_type == 'full_paid'?$totalForProduction: $request->paid_amount
        ]);
        return redirect()->route('production.section')->with('success', 'Productions Created Successfully');
    }

    public function edit($id): View|Factory|Application
    {
        $production = Production::findOrFail($id);
        $houses = ProductionHouse::all();
        $showrooms = Showroom::all();
        $accounts = Account::all();
        $warehouses = Warehouse::all();
        $brands = Brand::all();
        $colors = Color::all();
        $sizes = Size::all();
        $rawMaterialStocks = RawMaterialStock::all();

        // Retrieve production's raw materials and products
        $existingRawMaterials = DB::table('production_raw_materials')->where('production_id', $production->id)->get();
        $existingProducts = DB::table('production_product')->where('production_id', $production->id)->get();

        return view('admin.pages.production.edit', compact(
            'production',
            'houses',
            'showrooms',
            'accounts',
            'warehouses',
            'rawMaterialStocks',
            'brands',
            'colors',
            'sizes',
            'existingRawMaterials',
            'existingProducts'
        ));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        // Fetch the production record by its ID
        $production = Production::find($id);

        // Validate the incoming request
        $request->validate([
            'production_house_id' => 'required',
            'showroom_id' => 'required',
            'account_id' => 'required',
            'production_date' => 'required',
        ]);
        // Retrieve and process cost details
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
        // Update the production record
        $production->update([
            'production_house_id' => $request->production_house_id,
            'showroom_id' => $request->showroom_id,
            'account_id' => $request->account_id,
            'production_date' => $request->production_date,
            'cost_details' => json_encode($combinedCosts),
            'total_cost' => $totalCost,
        ]);
        // Handle existing raw materials
        $existingRawMaterials = DB::table('production_raw_materials')
            ->where('production_id', $production->id)
            ->get();
        // Handle raw materials
        $newRawMaterials = $request->raw_material_id ?? [];
        // Add or update raw materials
        foreach ($newRawMaterials as $index => $rawMaterial) {
            // Prepare the data array
            $data = [
                'raw_material_id' => $rawMaterial,
                'brand_id' => $request->raw_material_brand_id[$index] ?? null,
                'size_id' => $request->raw_material_size_id[$index] ?? null,
                'color_id' => $request->raw_material_color_id[$index] ?? null,
                'warehouse_id' => $request->raw_material_warehouse_id[$index] ?? null,
                'price' => (double) ($request->raw_materials_price[$index] ?? 0), // Ensure it's a float
                'quantity' => (double) ($request->raw_material_quantity[$index] ?? 0), // Ensure it's a float
                'total_price' => (double) ($request->raw_material_total_price[$index] ?? 0), // Ensure it's a float
            ];
            // Check if the raw material exists
            $existingMaterial = $existingRawMaterials->firstWhere('raw_material_id', $rawMaterial);

            if ($existingMaterial) {
                // Update existing raw material
                DB::table('production_raw_materials')->where('id', $existingMaterial->id)->update($data);
            } else {
                // Insert new raw material
                $data['production_id'] = $production->id; // Add foreign key
                DB::table('production_raw_materials')->insert($data);
            }
        }

        // Remove raw materials that are no longer included
        $existingRawMaterialIds = $existingRawMaterials->pluck('raw_material_id')->toArray();
        $newRawMaterialIds = array_filter($newRawMaterials);
        $removedRawMaterials = array_diff($existingRawMaterialIds, $newRawMaterialIds);

        if (!empty($removedRawMaterials)) {
            DB::table('production_raw_materials')
                ->where('production_id', $production->id)
                ->whereIn('raw_material_id', $removedRawMaterials)
                ->delete();
        }

        // Handle existing products
        $existingProducts = DB::table('production_product')
            ->where('production_id', $production->id)
            ->get();

        // Handle products
        $newProducts = $request->product_id ?? [];

        // Add or update products
        foreach ($newProducts as $index => $product) {
            // Prepare the data array
            $productData = [
                'product_id' => $product,
                'brand_id' => $request->brand_id[$index] ?? null,
                'size_id' => $request->size_id[$index] ?? null,
                'color_id' => $request->color_id[$index] ?? null,
                'per_pc_cost' => (double) ($request->price[$index] ?? 0), // Ensure it's a float
                'quantity' => (double) ($request->quantity[$index] ?? 0), // Ensure it's a float
                'sub_total' => (double) ($request->total_price[$index] ?? 0), // Ensure it's a float
            ];

            // Check if the product exists
            $existingProduct = $existingProducts->firstWhere('product_id', $product);

            if ($existingProduct) {
                // Update existing product
                DB::table('production_product')->where('id', $existingProduct->id)->update($productData);
            } else {
                // Insert new product
                $productData['production_id'] = $production->id; // Add foreign key
                DB::table('production_product')->insert($productData);
            }
        }

        // Remove products that are no longer included
        $existingProductIds = $existingProducts->pluck('product_id')->toArray();
        $newProductIds = array_filter($newProducts);
        $removedProducts = array_diff($existingProductIds, $newProductIds);

        if (!empty($removedProducts)) {
            DB::table('production_product')
                ->where('production_id', $production->id)
                ->whereIn('product_id', $removedProducts)
                ->delete();
        }

        // Recalculate total costs
        $totalRawMaterialCost = DB::table('production_raw_materials')
            ->where('production_id', $production->id)
            ->sum('total_price');

        $totalProductCost = DB::table('production_product')
            ->where('production_id', $production->id)
            ->sum('sub_total');

        // Update the total cost and amount
        $totalForProduction = $totalCost + $totalRawMaterialCost + $totalProductCost;
        $production->update([
            'total_raw_material_cost' => $totalRawMaterialCost,
            'total_product_cost' => $totalProductCost,
            'net_total' => $totalForProduction,
            'payment_type'=>$request->payment_type,
            'amount' => $request->payment_type == 'full_paid'?$totalForProduction: $request->paid_amount
        ]);

        // Redirect with a success message
        return redirect()->route('production.section')->with('success', 'Production Updated Successfully');
    }

    public function show($id): View|Factory|Application
    {
        $production = Production::findOrFail($id);
        $activities = AdminActivity::getActivities(Production::class, $id)->orderBy('created_at', 'desc')->take(10)->get();
        return view('admin.pages.production.show',
            compact('production',
                'activities',));
    }

    public function destroy($id): RedirectResponse
    {
        $production = Production::find($id);
        $production->delete();
        return redirect()->route('production.section')->with('success', 'Production Deleted Successfully');
    }

    public function getRawMaterialsByWarehouse(Request $request)
    {
        $warehouseId = $request->get('warehouse_id');
        // Fetch raw materials based on the warehouse id
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
