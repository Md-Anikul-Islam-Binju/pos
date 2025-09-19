<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminActivity;
use App\Models\RawMaterial;
use App\Models\RawMaterialPurchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Brand;
use App\Models\Size;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class RawMaterialPurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\RawMaterialPurchase,raw_material_purchase')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        $purchases = RawMaterialPurchase::orderBy('id', 'DESC')->get();
        return view('admin.pages.raw-material-purchase.index', compact('purchases'));
    }

    public function create(): View|Factory|Application
    {
        $suppliers = Supplier::orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::orderBy('id', 'DESC')->get();
        $brands = Brand::orderBy('id', 'DESC')->get();
        $sizes = Size::orderBy('id', 'DESC')->get();
        $colors = Color::orderBy('id', 'DESC')->get();
        $accounts = Account::all();

        $rawMaterials = RawMaterial::with(['brands', 'sizes', 'colors'])->get();

        return view('admin.pages.raw-material-purchase.create',
            compact('suppliers', 'warehouses', 'brands', 'sizes', 'colors', 'accounts', 'rawMaterials'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'account_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'cost_details' => 'nullable',
            'cost_amount' => 'nullable',
            'total_cost' => 'required',
            'total_price' => 'required',
            'status' => 'nullable',
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

        // Temporarily set total_price to 0
        $purchase = RawMaterialPurchase::create([
            'supplier_id' => $request->supplier_id,
            'warehouse_id' => $request->warehouse_id,
            'account_id' => $request->account_id,
            'purchase_date' => $request->purchase_date,
            'cost_details' => json_encode($combinedCosts),
            'total_cost' => $totalCost,
            'total_price' => 0, // Will be updated later
            'amount' => $totalCost + 0,
        ]);

        // Calculate total price if products exist
        if ($request->filled('product_id')) {
            $product_ids = $request->input('product_id');
            $brand_ids = $request->input('brand_id');
            $size_ids = $request->input('size_id');
            $color_ids = $request->input('color_id');
            $prices = $request->input('price');
            $quantities = $request->input('quantity');
            $total_prices = $request->input('total_price');

            foreach ($product_ids as $index => $raw_material_id) {
                DB::table('purchase_raw_material')->insert([
                    'raw_material_purchase_id' => $purchase->id,
                    'raw_material_id' => $raw_material_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'brand_id' => isset($brand_ids[$index]) ? $brand_ids[$index] : null,
                    'size_id' => isset($size_ids[$index]) ? $size_ids[$index] : null,
                    'color_id' => isset($color_ids[$index]) ? $color_ids[$index] : null,
                    'price' => isset($prices[$index]) ? $prices[$index] : 0,
                    'quantity' => isset($quantities[$index]) ? $quantities[$index] : 0,
                    'total_price' => isset($total_prices[$index]) ? (float) $total_prices[$index] : 0,
                ]);
            }

            // Calculate the total price
            $totalPrice = DB::table('purchase_raw_material')
                ->where('raw_material_purchase_id', $purchase->id)
                ->sum('total_price');

            // Update the total_price and amount in the purchases table
            $amount = $totalCost + $totalPrice;
            $purchase->update([
                'total_price' => $totalPrice,
                'net_total' => $amount,
                'payment_type'=>$request->payment_type,
                'amount' => $request->payment_type == 'full_paid' ? $amount: $request->paid_amount
            ]);
        }

        return redirect()->route('raw-material-purchases.index')->with('success', 'RawMaterialPurchase Created Successfully');
    }



    public function edit($id)
    {
        $purchase = RawMaterialPurchase::find($id);
        if (!$purchase) {
            return redirect()->route('raw-material-purchases.index')->with('error', 'RawMaterialPurchase Not Found');
        }
        elseif ($purchase->status == 'approved'){
            return redirect()->route('raw-material-purchases.index')->with('error', 'RawMaterialPurchase already approved');
        }
        $suppliers = Supplier::orderBy('id', 'DESC')->get();
        $warehouses = Warehouse::orderBy('id', 'DESC')->get();
        $brands = Brand::orderBy('id', 'DESC')->get();
        $sizes = Size::orderBy('id', 'DESC')->get();
        $colors = Color::orderBy('id', 'DESC')->get();
        $accounts = Account::orderBy('id', 'DESC')->get();
        $products = $purchase->raw_materials;
        return view('admin.pages.raw-material-purchase.edit',
            compact('purchase', 'suppliers', 'warehouses', 'brands', 'sizes', 'colors', 'accounts', 'products'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $purchase = RawMaterialPurchase::find($id);
        if (!$purchase) {
            return redirect()->route('raw-material-purchases.index')->with('error', 'RawMaterialPurchase Not Found');
        }elseif ($purchase->status == 'approved'){
            return redirect()->route('raw-material-purchases.index')->with('error', 'RawMaterialPurchase already approved');
        }
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'account_id' => 'required|exists:warehouses,id',
            'purchase_date' => 'required|date',
            'cost_details' => 'nullable',
            'cost_amount' => 'nullable',
            'total_cost' => 'required',
            'status' => 'nullable',
        ]);

        // Retrieve cost details and amounts
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
                $totalCost += $amount;  // Calculate total cost
            }
        }

        // Update the purchase record
        $purchase->update([
            'supplier_id' => $request->supplier_id,
            'warehouse_id' => $request->warehouse_id,
            'account_id' => $request->account_id,
            'purchase_date' => $request->purchase_date,
            'cost_details' => json_encode($combinedCosts),
            'total_cost' => $totalCost,
            'total_price' => 0,  // Temporarily set to 0, will update it later
            'status' => $request->status ?? $purchase->status,
        ]);

        if ($request->filled('product_id')) {
            $product_ids = $request->input('product_id');
            $brand_ids = $request->input('brand_id');
            $size_ids = $request->input('size_id');
            $color_ids = $request->input('color_id');
            $prices = $request->input('price');
            $quantities = $request->input('quantity');
            $total_prices = $request->input('total_price');

            // Delete existing related records
            DB::table('purchase_raw_material')->where('raw_material_purchase_id', $purchase->id)->delete();

            foreach ($product_ids as $index => $raw_material_id) {
                DB::table('purchase_raw_material')->insert([
                    'raw_material_purchase_id' => $purchase->id,
                    'raw_material_id' => $raw_material_id,
                    'warehouse_id' => $purchase->warehouse_id,
                    'brand_id' => isset($brand_ids[$index]) ? $brand_ids[$index] : null,
                    'size_id' => isset($size_ids[$index]) ? $size_ids[$index] : null,
                    'color_id' => isset($color_ids[$index]) ? $color_ids[$index] : null,
                    'price' => isset($prices[$index]) ? $prices[$index] : 0,
                    'quantity' => isset($quantities[$index]) ? $quantities[$index] : 0,
                    'total_price' => isset($total_prices[$index]) ? (float) $total_prices[$index] : 0,
                ]);
            }

            // Calculate the total price again
            $totalPrice = DB::table('purchase_raw_material')
                ->where('raw_material_purchase_id', $purchase->id)
                ->sum('total_price');

            // Update total_price and amount
            $amount = $totalCost + $totalPrice;
            $purchase->update([
                'total_price' => $totalPrice,
                //'amount' => $amount, // Update amount
                'net_total' => $amount,
                'payment_type'=>$request->payment_type,
                'amount' => $request->payment_type == 'full_paid' ? $amount: $request->paid_amount
            ]);
        }

        return redirect()->route('raw-material-purchases.index')->with('success', 'RawMaterialPurchase Updated Successfully');
    }

    public function show($id): View|Factory|Application
    {
        $purchase = RawMaterialPurchase::findOrFail($id);
        $supplier = Supplier::findOrFail($purchase->supplier_id);
        $warehouse = Warehouse::findOrFail($purchase->warehouse_id);
        $brands = Brand::orderBy('id', 'DESC')->get();
        $sizes = Size::orderBy('id', 'DESC')->get();
        $colors = Color::orderBy('id', 'DESC')->get();
        $account = Account::where('id', $purchase->account_id)->first();
        $products = $purchase->raw_materials;
        $activities = AdminActivity::getActivities(RawMaterialPurchase::class, $id)->orderBy('created_at', 'desc')->take(10)->get();
        return view('admin.pages.raw-material-purchase.show',
            compact('purchase', 'supplier', 'warehouse', 'brands', 'sizes', 'colors', 'account', 'products', 'activities'));
    }

    public function destroy($id): RedirectResponse
    {
        $purchase = RawMaterialPurchase::find($id);
        $purchase->delete();
        return redirect()->route('raw-material-purchases.index')->with('success', 'RawMaterialPurchase Deleted Successfully');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        $purchase = RawMaterialPurchase::find($id);
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('raw-material-purchases.index')->with('error', 'Invalid status.');
        }
        elseif ($purchase->status == 'approved' && in_array($status, ['pending', 'rejected']) && !canRawMaterialPurchaseStatusChangeFromApprove($purchase)) {
            return redirect()->route('raw-material-purchases.index')->with('error', 'You cant Change Status yet.');
        }
        elseif (!$purchase) {
            return redirect()->back()->with('error', 'Purchase not found.');
        }
        $purchase->status = $status;
        $purchase->update();
        return redirect()->back()->with('success', 'Raw Material Purchase status updated successfully.');
    }

    public function printRawMaterialPurchase($id)
    {
        $purchase = RawMaterialPurchase::find($id);
        $products = $purchase->raw_materials;
        $brands = Brand::orderBy('id', 'DESC')->get();
        $sizes = Size::orderBy('id', 'DESC')->get();
        $colors = Color::orderBy('id', 'DESC')->get();
        return view('admin.pages.raw-material-purchase.invoice', compact('purchase', 'products', 'brands', 'sizes', 'colors'));
    }
}
