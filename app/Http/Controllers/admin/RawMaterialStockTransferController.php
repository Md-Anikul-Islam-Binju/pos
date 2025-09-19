<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminActivity;
use App\Models\RawMaterialStock;
use App\Models\RawMaterialStockTransfer;
use App\Models\Warehouse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RawMaterialStockTransferController extends Controller
{
    public function index() {
        $transfers = RawMaterialStockTransfer::all();
        return view('admin.pages.raw-material-stock-transfer.index', compact('transfers'));
    }

    public function create() {
        $warehouses = Warehouse::all();
        return view('admin.pages.raw-material-stock-transfer.create', compact('warehouses'));
    }

    public function getrawMaterialStocksByWarehouse($warehouse_id)
    {
        $rawMaterialStocks = RawMaterialStock::where('warehouse_id', $warehouse_id)
            ->with(['raw_material', 'warehouse'])
            ->get()
            ->map(function ($rawMaterialStock) {
                return [
                    'id' => $rawMaterialStock->id,
                    'raw_material_name' => $rawMaterialStock->raw_material->name,
                    'warehouse_name' => $rawMaterialStock->warehouse->name,
                    'quantity' => $rawMaterialStock->quantity,
                ];
            });

        return response()->json($rawMaterialStocks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'selected_raw_materials' => 'required|array',
            'transfer_quantities' => 'required|array',
            'transfer_quantities.*' => 'required|integer|min:1',
        ]);

        // Create the main RawMaterialStockTransfer entry
        $rawMaterialStockTransfer = RawMaterialStockTransfer::create([
            'date' => $request->date,
            'status' => 'pending',
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id' => $request->to_warehouse_id,
            'note' => $request->note,
            'admin_id' => auth()->id(),
        ]);

        // Check if selected raw materials and transfer quantities are equal in count
        if (count($request->selected_raw_materials) !== count($request->transfer_quantities)) {
            return redirect()->back()->with('error', 'Mismatch between selected raw materials and transfer quantities.');
        }

        // Loop through each selected raw material and store the relation in the pivot table
        foreach ($request->selected_raw_materials as $index => $rawMaterialStockId) {
            $quantity = $request->transfer_quantities[$rawMaterialStockId];
            // Attach the raw material stock with the transfer in the pivot table
            $rawMaterialStockTransfer->rawMaterialStocks()->attach($rawMaterialStockId, ['quantity' => $quantity]);
        }

        return redirect()->route('raw-material-stock-transfers.index')->with('success', 'Raw material stock transfer created successfully.');
    }

    public function edit($id) {
        $transfer = RawMaterialStockTransfer::with(['rawMaterialStocks.raw_material'])->findOrFail($id);
        // Get the 'from_warehouse_id' and fetch products for that warehouse
        $warehouseId = $transfer->from_warehouse_id;
        $warehouseRawMaterials = RawMaterialStock::where('warehouse_id', $warehouseId)->get();
        $warehouses = Warehouse::all();
        return view('admin.pages.raw-material-stock-transfer.edit', compact('warehouses', 'transfer', 'warehouseRawMaterials'));
    }

    public function update(Request $request, RawMaterialStockTransfer $rawMaterialStockTransfer)
    {
        // Check if the status is 'completed'. If it's not 'pending', prevent update.
        if ($rawMaterialStockTransfer->status !== 'pending') {
            return redirect()->route('raw-material-stock-transfers.index')
                ->with('error', 'You cannot update a transfer that is not pending.');
        }

        // Validate the incoming request data
        $request->validate([
            'date' => 'required|date',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'selected_raw_materials' => 'required|array',
            'transfer_quantities' => 'required|array',
            'transfer_quantities.*' => 'required|integer|min:1',
        ]);

        // Update the ProductStockTransfer record
        $rawMaterialStockTransfer->update([
            'date' => $request->date,
            'status' => 'pending',
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id' => $request->to_warehouse_id,
            'note' => $request->note,
            'admin_id' => auth()->id(),
        ]);

        // Sync the product stocks in the pivot table
        $rawMaterialStockTransfer->rawMaterialStocks()->sync([]);

        foreach ($request->selected_raw_materials as $index => $rawMaterialStockId) {
            $quantity = $request->transfer_quantities[$rawMaterialStockId];

            // Sync the transfer quantities with the pivot table
            $rawMaterialStockTransfer->rawMaterialStocks()->syncWithoutDetaching([
                $rawMaterialStockId => ['quantity' => $quantity],
            ]);
        }

        // Redirect back with a success message
        return redirect()->route('raw-material-stock-transfers.index')->with('success', 'Raw material stock transfer updated successfully.');
    }

    public function show($id) {
        $transfer = RawMaterialStockTransfer::with(['rawMaterialStocks','rawMaterialStocks.raw_material'])->findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(RawMaterialStockTransfer::class, $id)->orderBy('created_at', 'desc')->take(10)->get();
        return view('admin.pages.raw-material-stock-transfer.show', compact(['transfer','admins','activities']));
    }

    public function changeStatus(Request $request, RawMaterialStockTransfer $rawMaterialStockTransfer)
    {
        // Validate that the new status is either 'approved', 'rejected', or 'pending'
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        // Check the current status
        $currentStatus = $rawMaterialStockTransfer->status;

        // If the status is approved, handle stock updates
        if ($currentStatus === 'approved') {
            if ($request->status === 'pending') {
                // Allow change from approved to pending, but no stock update needed
                $rawMaterialStockTransfer->update([
                    'status' => 'pending',
                ]);

                // Rollback stock changes (if any were made)
                $this->rollbackStock($rawMaterialStockTransfer);

                return redirect()->route('raw-material-stock-transfers.index')
                    ->with('success', 'Raw material stock transfer status changed back to pending successfully.');
            }

            return redirect()->route('raw-material-stock-transfers.index')
                ->with('error', 'Approved status cannot be changed directly to rejected.');
        }

        if ($currentStatus === 'pending') {
            if ($request->status === 'approved') {
                // Allow change from pending to approved, update the stock
                $rawMaterialStockTransfer->update([
                    'status' => 'approved',
                ]);

                // Update stock quantities (decrease from 'from_warehouse' and increase in 'to_warehouse')
                $this->updateStock($rawMaterialStockTransfer);

                return redirect()->route('raw-material-stock-transfers.index')
                    ->with('success', 'Raw material stock transfer approved successfully and stock updated.');
            }

            if ($request->status === 'rejected') {
                // Allow change from pending to rejected, no stock update needed
                $rawMaterialStockTransfer->update([
                    'status' => 'rejected',
                ]);

                return redirect()->route('raw-material-stock-transfers.index')
                    ->with('success', 'Raw material stock transfer rejected successfully.');
            }
        }
        if ($currentStatus === 'rejected') {
            if ($request->status === 'approved') {
                // Allow change from pending to approved, update the stock
                $rawMaterialStockTransfer->update([
                    'status' => 'approved',
                ]);

                // Update stock quantities (decrease from 'from_warehouse' and increase in 'to_warehouse')
                $this->updateStock($rawMaterialStockTransfer);

                return redirect()->route('raw-material-stock-transfers.index')
                    ->with('success', 'Raw material stock transfer approved successfully and stock updated.');
            }

            if ($request->status === 'pending') {
                // Allow change from pending to rejected, no stock update needed
                $rawMaterialStockTransfer->update([
                    'status' => 'pending',
                ]);

                return redirect()->route('raw-material-stock-transfers.index')
                    ->with('success', 'Raw material stock transfer pending successfully.');
            }
        }

        // Handle invalid status change request
        return redirect()->route('raw-material-stock-transfers.index')
            ->with('error', 'Invalid status change operation.');
    }

    // Method to update stock when transfer is approved
    private function updateStock(RawMaterialStockTransfer $rawMaterialStockTransfer)
    {

        // Retrieve all product stock associated with the transfer
        $transferRawMaterials = $rawMaterialStockTransfer->rawMaterialStocks;

        foreach ($transferRawMaterials as $transferRawMaterial) {

            // Decrease the quantity in the 'from_showroom'
            $fromStock = RawMaterialStock::where('id', $transferRawMaterial->pivot->raw_material_stock_id)
                ->where('warehouse_id', $rawMaterialStockTransfer->from_warehouse_id)
                ->first();

            if ($fromStock && $fromStock->quantity >= $transferRawMaterial->quantity) {
                $fromStock->quantity -= $transferRawMaterial->pivot->quantity;
                $fromStock->save();
            }

            // Increase the quantity in the 'to_warehouse' or create new stock entry
            $toStock = RawMaterialStock::where('raw_material_id', $fromStock->raw_material_id)
                ->where('color_id', $fromStock->color_id)
                ->where('brand_id', $fromStock->brand_id)
                ->where('size_id', $fromStock->size_id)
                ->where('warehouse_id', $rawMaterialStockTransfer->to_warehouse_id)
                ->first();

            if ($toStock) {
                // Update existing stock in the to_showroom
                $toStock->quantity += $transferRawMaterial->pivot->quantity;
                $toStock->save();
            } else {
                // If no stock exists in the 'to_warehouse', create new stock with all necessary data
                RawMaterialStock::create([
                    'raw_material_id' => $fromStock->raw_material_id,
                    'quantity' => $transferRawMaterial->pivot->quantity,
                    'price' => $fromStock->price,
                    'color_id' => $fromStock->color_id,
                    'brand_id' => $fromStock->brand_id,
                    'size_id' => $fromStock->size_id,
                    'warehouse_id' => $rawMaterialStockTransfer->to_warehouse_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    // Method to rollback stock if the transfer status is set back to pending
    private function rollbackStock(RawMaterialStockTransfer $rawMaterialStockTransfer)
    {
        // Retrieve all product stock associated with the transfer
        $transferRawMaterials = $rawMaterialStockTransfer->rawMaterialStocks;

        foreach ($transferRawMaterials as $transferRawMaterial) {
            // Increase the quantity back in the 'from_showroom'
            $fromStock = RawMaterialStock::where('raw_material_id', $transferRawMaterial->raw_material_id)
                ->where('color_id', $transferRawMaterial->color_id)
                ->where('brand_id', $transferRawMaterial->brand_id)
                ->where('size_id', $transferRawMaterial->size_id)
                ->where('warehouse_id', $rawMaterialStockTransfer->from_warehouse_id)
                ->first();

            if ($fromStock) {
                $fromStock->quantity += $transferRawMaterial->pivot->quantity;
                $fromStock->save();
            }

            // Decrease the quantity in the 'to_showroom'
            $toStock = RawMaterialStock::where('raw_material_id', $transferRawMaterial->raw_material_id)
                ->where('color_id', $transferRawMaterial->color_id)
                ->where('brand_id', $transferRawMaterial->brand_id)
                ->where('size_id', $transferRawMaterial->size_id)
                ->where('warehouse_id', $rawMaterialStockTransfer->to_warehouse_id)
                ->first();

            if ($toStock) {
                if ($toStock->quantity > $transferRawMaterial->pivot->quantity) {
                    $toStock->quantity -= $transferRawMaterial->pivot->quantity;
                    $toStock->save();
                } else {
                    // If quantity reaches zero, delete the record
                    $toStock->delete();
                }
            }
        }
    }

    public function destroy(RawMaterialStockTransfer $rawMaterialStockTransfer)
    {
        // Check if the status is approved, prevent soft delete if already approved
        if ($rawMaterialStockTransfer->status === 'approved') {
            return redirect()->route('raw-material-stock-transfers.index') ->with('error', 'Approved transfers cannot be deleted.');
        }
        // Soft delete the transfer
        $rawMaterialStockTransfer->delete();
        return redirect()->route('raw-material-stock-transfers.index')->with('success', 'Raw material stock transfer deleted successfully.');
    }
}
