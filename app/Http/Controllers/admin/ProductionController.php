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
        $rawMaterialStock = RawMaterialStock::all();
        return view('admin.pages.production.index', compact('production', 'productionHouse', 'showroom', 'account', 'warehouse', 'brand', 'color', 'size', 'rawMaterialStock'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'production_house_id'       => 'required|exists:production_houses,id',
                'showroom_id'               => 'required|exists:showrooms,id',
                'account_id'                => 'required|exists:accounts,id',
                'production_date'           => 'required|date',
                'warehouse_id'              => 'required|exists:warehouses,id',

                'payment_type'              => 'required|in:full_paid,partial_paid',
                'paid_amount'               => 'nullable|numeric|min:0',

                'cost_details.*'            => 'nullable|string|max:255',
                'cost_amount.*'             => 'nullable|numeric|min:0',

                'raw_material_id.*'         => 'nullable|exists:raw_materials,id',
                'raw_material_brand_id.*'   => 'nullable|exists:brands,id',
                'raw_material_size_id.*'    => 'nullable|exists:sizes,id',
                'raw_material_color_id.*'   => 'nullable|exists:colors,id',
                'raw_material_warehouse_id.*' => 'nullable|exists:warehouses,id',
                'raw_material_price.*'      => 'nullable|numeric|min:0',
                'raw_material_quantity.*'   => 'nullable|numeric|min:0',
                'raw_material_total_price.*'=> 'nullable|numeric|min:0',

                'product_id.*'              => 'nullable|exists:products,id',
                'brand_id.*'                => 'nullable|exists:brands,id',
                'size_id.*'                 => 'nullable|exists:sizes,id',
                'color_id.*'                => 'nullable|exists:colors,id',
                'price.*'                   => 'nullable|numeric|min:0',
                'quantity.*'                => 'nullable|numeric|min:0',
                'total_price.*'             => 'nullable|numeric|min:0',
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
                DB::table('production_raw_materials')->insert([
                    'production_id' => $production->id,
                    'raw_material_id' => $rawMaterial,
                    'brand_id' => isset($request->raw_material_brand_id[$index]) ? $request->raw_material_brand_id[$index] : null,
                    'size_id' => isset($request->raw_material_size_id[$index]) ? $request->raw_material_size_id[$index] : null,
                    'color_id' => isset($request->raw_material_color_id[$index]) ? $request->raw_material_color_id[$index] : null,
                    'warehouse_id' => isset($request->raw_material_warehouse_id[$index]) ? $request->raw_material_warehouse_id[$index] : null,
                    'price' => isset($request->raw_material_price[$index]) ? $request->raw_material_price[$index] : 0,
                    'quantity' => isset($request->raw_material_quantity[$index]) ? $request->raw_material_quantity[$index] : 0,
                    'total_price' => isset($request->raw_material_total_price[$index]) ? (float) $request->raw_material_total_price[$index] : 0,
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
                    'sub_total' => isset($request->total_price[$index]) ? (float) $request->total_price[$index] : 0,
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

    public function edit($id)
    {
        $production = Production::with(['productionHouse', 'showroom', 'account'])->findOrFail($id);

        // Prepare options for selects (HTML <option> tags with selected)
        $productionHouseOptions = '';
        foreach (ProductionHouse::all() as $house) {
            $selected = $house->id == $production->production_house_id ? 'selected' : '';
            $productionHouseOptions .= "<option value='{$house->id}' {$selected}>{$house->name}</option>";
        }

        $showroomOptions = '';
        foreach (Showroom::all() as $showroom) {
            $selected = $showroom->id == $production->showroom_id ? 'selected' : '';
            $showroomOptions .= "<option value='{$showroom->id}' {$selected}>{$showroom->name}</option>";
        }

        $accountOptions = '';
        foreach (Account::all() as $account) {
            $selected = $account->id == $production->account_id ? 'selected' : '';
            $accountOptions .= "<option value='{$account->id}' {$selected}>{$account->name}</option>";
        }

        // Cost Details
        $costDetails = json_decode($production->cost_details, true) ?? [];

        // Raw Materials
        $rawMaterials = DB::table('production_raw_materials')
            ->where('production_id', $production->id)
            ->get()
            ->map(function($rm) {
                return [
                    'raw_material_id' => $rm->raw_material_id,
                    'brand_id' => $rm->brand_id,
                    'size_id' => $rm->size_id,
                    'color_id' => $rm->color_id,
                    'warehouse_id' => $rm->warehouse_id,
                    'price' => $rm->price,
                    'quantity' => $rm->quantity,
                    'total_price' => $rm->total_price,
                    // Option HTMLs
                    'options' => $this->generateOptions(RawMaterialStock::all(), 'id', 'name', $rm->raw_material_id),
                    'brandOptions' => $this->generateOptions(Brand::all(), 'id', 'name', $rm->brand_id),
                    'sizeOptions' => $this->generateOptions(Size::all(), 'id', 'name', $rm->size_id),
                    'colorOptions' => $this->generateOptions(Color::all(), 'id', 'name', $rm->color_id),
                    'warehouseOptions' => $this->generateOptions(Warehouse::all(), 'id', 'name', $rm->warehouse_id),
                ];
            });

        // Products
        $products = DB::table('production_product')
            ->where('production_id', $production->id)
            ->get()
            ->map(function($p) {
                return [
                    'product_id' => $p->product_id,
                    'brand_id' => $p->brand_id,
                    'size_id' => $p->size_id,
                    'color_id' => $p->color_id,
                    'per_pc_cost' => $p->per_pc_cost,
                    'quantity' => $p->quantity,
                    'sub_total' => $p->sub_total,
                    'options' => $this->generateOptions(Production::all(), 'id', 'name', $p->product_id), // Replace with actual Product model
                    'brandOptions' => $this->generateOptions(Brand::all(), 'id', 'name', $p->brand_id),
                    'sizeOptions' => $this->generateOptions(Size::all(), 'id', 'name', $p->size_id),
                    'colorOptions' => $this->generateOptions(Color::all(), 'id', 'name', $p->color_id),
                ];
            });

        return response()->json([
            'id' => $production->id,
            'production_date' => $production->production_date,
            'payment_type' => $production->payment_type,
            'amount' => $production->amount,
            'productionHouseOptions' => $productionHouseOptions,
            'showroomOptions' => $showroomOptions,
            'accountOptions' => $accountOptions,
            'cost_details' => $costDetails,
            'raw_materials' => $rawMaterials,
            'products' => $products,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'production_house_id'       => 'required|exists:production_houses,id',
                'showroom_id'               => 'required|exists:showrooms,id',
                'account_id'                => 'required|exists:accounts,id',
                'production_date'           => 'required|date',
                'warehouse_id'              => 'required|exists:warehouses,id',

                'payment_type'              => 'required|in:full_paid,partial_paid',
                'paid_amount'               => 'nullable|numeric|min:0',

                'cost_details.*'            => 'nullable|string|max:255',
                'cost_amount.*'             => 'nullable|numeric|min:0',

                'raw_material_id.*'         => 'nullable|exists:raw_materials,id',
                'raw_material_brand_id.*'   => 'nullable|exists:brands,id',
                'raw_material_size_id.*'    => 'nullable|exists:sizes,id',
                'raw_material_color_id.*'   => 'nullable|exists:colors,id',
                'raw_material_warehouse_id.*' => 'nullable|exists:warehouses,id',
                'raw_material_price.*'      => 'nullable|numeric|min:0',
                'raw_material_quantity.*'   => 'nullable|numeric|min:0',
                'raw_material_total_price.*'=> 'nullable|numeric|min:0',

                'product_id.*'              => 'nullable|exists:products,id',
                'brand_id.*'                => 'nullable|exists:brands,id',
                'size_id.*'                 => 'nullable|exists:sizes,id',
                'color_id.*'                => 'nullable|exists:colors,id',
                'price.*'                   => 'nullable|numeric|min:0',
                'quantity.*'                => 'nullable|numeric|min:0',
                'total_price.*'             => 'nullable|numeric|min:0',
            ]);

            $production = Production::find($id);

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
                $data = [
                    'raw_material_id' => $rawMaterial,
                    'brand_id' => $request->raw_material_brand_id[$index] ?? null,
                    'size_id' => $request->raw_material_size_id[$index] ?? null,
                    'color_id' => $request->raw_material_color_id[$index] ?? null,
                    'warehouse_id' => $request->raw_material_warehouse_id[$index] ?? null,
                    'price' => (double) ($request->raw_material_price[$index] ?? 0), // Ensure it's a float
                    'quantity' => (double) ($request->raw_material_quantity[$index] ?? 0), // Ensure it's a float
                    'total_price' => (double) ($request->raw_material_total_price[$index] ?? 0), // Ensure it's a float
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
                $productData = [
                    'product_id' => $product,
                    'brand_id' => $request->brand_id[$index] ?? null,
                    'size_id' => $request->size_id[$index] ?? null,
                    'color_id' => $request->color_id[$index] ?? null,
                    'per_pc_cost' => (double) ($request->price[$index] ?? 0), // Ensure it's a float
                    'quantity' => (double) ($request->quantity[$index] ?? 0), // Ensure it's a float
                    'sub_total' => (double) ($request->total_price[$index] ?? 0), // Ensure it's a float
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
            DB::table('production_raw_materials')->where('production_id', $production->id)->delete();
            DB::table('production_product')->where('production_id', $production->id)->delete();
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
            return redirect()->route('admin.pages.production.index')->with('error', 'Invalid status.');
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

    /**
     * Helper method to generate <option> tags
     */
    private function generateOptions($collection, $valueField, $textField, $selectedId = null)
    {
        $options = '';
        foreach ($collection as $item) {
            $selected = $item->$valueField == $selectedId ? 'selected' : '';
            $options .= "<option value='{$item->$valueField}' {$selected}>{$item->$textField}</option>";
        }
        return $options;
    }

    public function editAjax(Production $production)
    {
        return response()->json([
            'id' => $production->id,
            'production_date' => $production->production_date,
            'payment_type' => $production->payment_type,
            'amount' => $production->paid_amount,
            'productionHouseOptions' => view('admin.partials.house_options', ['selected' => $production->house_id])->render(),
            'showroomOptions' => view('admin.partials.showroom_options', ['selected' => $production->showroom_id])->render(),
            'accountOptions' => view('admin.partials.account_options', ['selected' => $production->account_id])->render(),
            'cost_details' => $production->costDetails->map(function($c) {
                return ['detail'=>$c->detail, 'amount'=>$c->amount];
            }),
            'raw_materials' => $production->rawMaterials->map(function($rm) {
                return [
                    'options' => view('admin.partials.material_options', ['selected'=>$rm->material_id])->render(),
                    'brandOptions' => view('admin.partials.brand_options', ['selected'=>$rm->brand_id])->render(),
                    'sizeOptions' => view('admin.partials.size_options', ['selected'=>$rm->size_id])->render(),
                    'colorOptions' => view('admin.partials.color_options', ['selected'=>$rm->color_id])->render(),
                    'warehouseOptions' => view('admin.partials.warehouse_options', ['selected'=>$rm->warehouse_id])->render(),
                    'price' => $rm->price,
                    'quantity' => $rm->quantity,
                    'total_price' => $rm->total_price,
                ];
            }),
            'products' => $production->products->map(function($p){
                return [
                    'options' => view('admin.partials.product_options', ['selected'=>$p->product_id])->render(),
                    'brandOptions' => view('admin.partials.brand_options', ['selected'=>$p->brand_id])->render(),
                    'sizeOptions' => view('admin.partials.size_options', ['selected'=>$p->size_id])->render(),
                    'colorOptions' => view('admin.partials.color_options', ['selected'=>$p->color_id])->render(),
                    'per_pc_cost' => $p->price,
                    'quantity' => $p->quantity,
                    'sub_total' => $p->total_price,
                ];
            }),
        ]);
    }
}
