<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\ProductStockTransfer;
use App\Models\Showroom;
use Illuminate\Http\Request;

class ProductStockTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('product-stock-transfer-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $transfer = ProductStockTransfer::all();
        $showroom = Showroom::all();
        return view('admin.pages.productStockTransfer.index', compact('transfer', 'showroom'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'from_showroom_id' => 'required|exists:showrooms,id',
                'to_showroom_id' => 'required|exists:showrooms,id|different:from_showroom_id',
                'selected_products' => 'required|array',
                'transfer_quantities' => 'required|array',
                'transfer_quantities.*' => 'required|integer|min:1',

            ]);

            $productStockTransfer = new ProductStockTransfer();
            $productStockTransfer->date = $request->date;
            $productStockTransfer->status = "pending";
            $productStockTransfer->from_showroom_id = $request->from_showroom_id;
            $productStockTransfer->to_showroom_id = $request->to_showroom_id;
            $productStockTransfer->user_id = auth()->id();
            $productStockTransfer->save();
            // Check if selected products and transfer quantities are equal in count
            if (count($request->selected_products) !== count($request->transfer_quantities)) {
                return redirect()->back()->with('error', 'Mismatch between selected products and transfer quantities.');
            }
            // Loop through each selected product and store the relation in the pivot table
            foreach ($request->selected_products as $index => $productStockId) {
                $quantity = $request->transfer_quantities[$productStockId];
                // Attach the product stock with the transfer in the pivot table
                $productStockTransfer->productStocks()->attach($productStockId, ['quantity' => $quantity]);
            }
            Toastr::success('Product Transfer Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $transfer = ProductStockTransfer::with([
            'productStocks.product',
            'productStocks.size',
            'productStocks.brand',
            'productStocks.color'
        ])->findOrFail($id);

        // Get the 'from_showroom_id' and fetch products for that showroom
        $showroomId = $transfer->from_showroom_id;
        $showroomProducts = ProductStock::where('showroom_id', $showroomId)->get();

        $showrooms = Showroom::all();

        return view('admin.pages.productStockTransfer.edit', compact('showrooms', 'transfer', 'showroomProducts'));
    }

    public function update(Request $request, $id)
    {
        try {
            $productStockTransfer = ProductStockTransfer::findOrFail($id);

            // Check if the status is 'completed'. If it's not 'pending', prevent update.
        if ($productStockTransfer->status !== 'pending') {
            return redirect()->route('admin.product-stock-transfers.index')
                ->with('error', 'You cannot update a transfer that is not pending.');
        }

            $request->validate([
                'date' => 'required|date',
            'from_showroom_id' => 'required|exists:showrooms,id',
            'to_showroom_id' => 'required|exists:showrooms,id|different:from_showroom_id',
            'selected_products' => 'required|array',
            'transfer_quantities' => 'required|array',
            'transfer_quantities.*' => 'required|integer|min:1',
            ]);

            $productStockTransfer->date = $request->date;
            $productStockTransfer->status = "pending";
            $productStockTransfer->from_showroom_id = $request->from_showroom_id;
            $productStockTransfer->to_showroom_id = $request->to_showroom_id;
            $productStockTransfer->user_id = auth()->id();
            $productStockTransfer->save();
            // Sync the product stocks in the pivot table
        $productStockTransfer->productStocks()->sync([]);

        foreach ($request->selected_products as $index => $productStockId) {
            $quantity = $request->transfer_quantities[$productStockId];

            // Sync the transfer quantities with the pivot table
            $productStockTransfer->productStocks()->syncWithoutDetaching([
                $productStockId => ['quantity' => $quantity],
            ]);
        }

            Toastr::success('Brand Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id) {
        $transfer = ProductStockTransfer::with(['productStocks','productStocks.product','productStocks.product.category'])->findOrFail($id);
        return view('admin.pages.productStockTransfer.show', compact('transfer'));
    }

    public function destroy($id)
    {
        try {
            $productStockTransfer = ProductStockTransfer::findOrFail($id);
            if ($productStockTransfer->status === 'approved') {
            return redirect()->route('admin.product-stock-transfers.index') ->with('error', 'Approved transfers cannot be deleted.');
        }
            $productStockTransfer->delete();
            Toastr::success('Product Stock Transfer Record Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getProductStocksByShowroom($id)
    {
        $productStocks = ProductStock::where('showroom_id', $id)
            ->with(['product', 'color', 'size', 'brand', 'showroom'])
            ->get()
            ->map(function ($productStock) {
                return [
                    'id' => $productStock->id,
                    'product_name' => $productStock->product->name,
                    'color_name' => $productStock->color->color_name,
                    'size_name' => $productStock->size->name,
                    'brand_name' => $productStock->brand->name,
                    'showroom_name' => $productStock->showroom->name,
                    'quantity' => $productStock->quantity,
                ];
            });

        return response()->json($productStocks);
    }

    public function changeStatus(Request $request, ProductStockTransfer $productStockTransfer)
    {
        // Validate that the new status is either 'approved', 'rejected', or 'pending'
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        // Check the current status
        $currentStatus = $productStockTransfer->status;

        // If the status is approved, handle stock updates
        if ($currentStatus === 'approved') {
            if ($request->status === 'pending') {
                // Allow change from approved to pending, but no stock update needed
                $productStockTransfer->update([
                    'status' => 'pending',
                ]);

                // Rollback stock changes (if any were made)
                $this->rollbackStock($productStockTransfer);

                return redirect()->route('admin.product-stock-transfers.index')
                    ->with('success', 'Product stock transfer status changed back to pending successfully.');
            }

            return redirect()->route('admin.product-stock-transfers.index')
                ->with('error', 'Approved status cannot be changed directly to rejected.');
        }

        if ($currentStatus === 'pending') {
            if ($request->status === 'approved') {
                // Allow change from pending to approved, update the stock
                $productStockTransfer->update([
                    'status' => 'approved',
                ]);

                // Update stock quantities (decrease from 'from_showroom' and increase in 'to_showroom')
                $this->updateStock($productStockTransfer);

                return redirect()->route('admin.product-stock-transfers.index')
                    ->with('success', 'Product stock transfer approved successfully and stock updated.');
            }

            if ($request->status === 'rejected') {
                // Allow change from pending to rejected, no stock update needed
                $productStockTransfer->update([
                    'status' => 'rejected',
                ]);

                return redirect()->route('admin.product-stock-transfers.index')
                    ->with('success', 'Product stock transfer rejected successfully.');
            }
        }
        if ($currentStatus === 'rejected') {
            if ($request->status === 'approved') {
                // Allow change from pending to approved, update the stock
                $productStockTransfer->update([
                    'status' => 'approved',
                ]);

                // Update stock quantities (decrease from 'from_showroom' and increase in 'to_showroom')
                $this->updateStock($productStockTransfer);

                return redirect()->route('admin.product-stock-transfers.index')
                    ->with('success', 'Product stock transfer approved successfully and stock updated.');
            }

            if ($request->status === 'pending') {
                // Allow change from pending to rejected, no stock update needed
                $productStockTransfer->update([
                    'status' => 'pending',
                ]);

                return redirect()->route('admin.product-stock-transfers.index')
                    ->with('success', 'Product stock transfer pending successfully.');
            }
        }

        // Handle invalid status change request
        return redirect()->route('admin.product-stock-transfers.index')
            ->with('error', 'Invalid status change operation.');
    }

    // Method to update stock when transfer is approved
    private function updateStock(ProductStockTransfer $productStockTransfer)
    {

        // Retrieve all product stock associated with the transfer
        $transferProducts = $productStockTransfer->productStocks;

        foreach ($transferProducts as $transferProduct) {

            // Decrease the quantity in the 'from_showroom'
            $fromStock = ProductStock::where('id', $transferProduct->pivot->product_stock_id)
                ->where('showroom_id', $productStockTransfer->from_showroom_id)
                ->first();

            if ($fromStock && $fromStock->quantity >= $transferProduct->quantity) {
                $fromStock->quantity -= $transferProduct->pivot->quantity;
                $fromStock->save();
            }

            // Increase the quantity in the 'to_showroom' or create new stock entry
            $toStock = ProductStock::where('product_id', $fromStock->product_id)
                ->where('color_id', $fromStock->color_id)
                ->where('brand_id', $fromStock->brand_id)
                ->where('size_id', $fromStock->size_id)
                ->where('showroom_id', $productStockTransfer->to_showroom_id)
                ->first();

            if ($toStock) {
                // Update existing stock in the to_showroom
                $toStock->quantity += $transferProduct->pivot->quantity;
                $toStock->save();
            } else {
                // If no stock exists in the 'to_showroom', create new stock with all necessary data
                ProductStock::create([
                    'product_id' => $fromStock->product_id,
                    'quantity' => $transferProduct->pivot->quantity,
                    'production_cost_price' => $fromStock->production_cost_price,
                    'avg_cost_price' => $fromStock->avg_cost_price,
                    'total_cost_price' => $fromStock->total_cost_price,
                    'color_id' => $fromStock->color_id,
                    'brand_id' => $fromStock->brand_id,
                    'size_id' => $fromStock->size_id,
                    'showroom_id' => $productStockTransfer->to_showroom_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // Method to rollback stock if the transfer status is set back to pending
    private function rollbackStock(ProductStockTransfer $productStockTransfer)
    {
        // Retrieve all product stock associated with the transfer
        $transferProducts = $productStockTransfer->productStocks;

        foreach ($transferProducts as $transferProduct) {
            // Increase the quantity back in the 'from_showroom'
            $fromStock = ProductStock::where('product_id', $transferProduct->product_id)
                ->where('color_id', $transferProduct->color_id)
                ->where('brand_id', $transferProduct->brand_id)
                ->where('size_id', $transferProduct->size_id)
                ->where('showroom_id', $productStockTransfer->from_showroom_id)
                ->first();

            if ($fromStock) {
                $fromStock->quantity += $transferProduct->pivot->quantity;
                $fromStock->save();
            }

            // Decrease the quantity in the 'to_showroom'
            $toStock = ProductStock::where('product_id', $transferProduct->product_id)
                ->where('color_id', $transferProduct->color_id)
                ->where('brand_id', $transferProduct->brand_id)
                ->where('size_id', $transferProduct->size_id)
                ->where('showroom_id', $productStockTransfer->to_showroom_id)
                ->first();

            if ($toStock) {
                if ($toStock->quantity > $transferProduct->pivot->quantity) {
                    $toStock->quantity -= $transferProduct->pivot->quantity;
                    $toStock->save();
                } else {
                    // If quantity reaches zero, delete the record
                    $toStock->delete();
                }
            }
        }
    }
}
