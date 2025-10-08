<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Supplier;
use App\Models\SupplierRefund;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\SupplierRefund,supplier_refund')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index()
    {
        $refund = SupplierRefund::orderBy('id', 'DESC')->get();
        $account = Account::all();
        $supplier = Supplier::all();
        return view('admin.pages.supplier-refund.index', compact('refund', 'account', 'supplier'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'refund_by' => 'nullable',
            'image' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('SupplierRefund-photo');
        }
        $account = Account::findOrFail($request->account_id);
        if ($account->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }
            return redirect()->back()->with('error', 'Insufficient Balance');
        }
        SupplierRefund::create([
            'supplier_id' => $request->supplier_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'refund_by' => $request->refund_by,
            'image' => $image ? 'uploads/' . $image : null
        ]);
        return redirect()->route('supplier.refund.section')->with('success','Refund created successfully.');
    }

    public function update(Request $request, $id)
    {
        $refund = SupplierRefund::find($id);
        $request->validate([
            'supplier_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'refund_by' => 'nullable',
            'image' => 'nullable',
            'status' => 'required'
        ]);
        $image = $refund->image ?? null;
        if ($request->hasFile('photo')) {
            if($refund->image) {
                $prev_image = $refund->image;
                if (file_exists($prev_image)) {
                    unlink($prev_image);
                }
            }
            $image = 'uploads/' . $request->file('photo')->store('supplierRefund-photo');
        }
        $account = Account::findOrFail($request->account_id);
        if ($account->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }
            return redirect()->back()->with('error', 'Insufficient Balance');
        }
        $accountId = $request->account_id ?? $refund->account_id;
        $refund->update([
            'supplier_id' => $request->supplier_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'refund_by' => $request->refund_by,
            'image' => $image ? 'uploads/' . $image : null,
            'status' => $request->status,
        ]);
        return redirect()->route('supplier.refund.section')->with('success','Refund updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $refund = SupplierRefund::find($id);
        if ($refund->image) {
            $previousImages = json_decode($refund->image, true);
            if ($previousImages) {
                foreach ($previousImages as $previousImage) {
                    $imagePath = public_path('uploads/' . $previousImage);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
        }
        $refund->delete();
        return redirect()->route('supplier.refund.index')->with('success','Refund deleted successfully.');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('supplier.refund.section')->with('error', 'Invalid status.');
        }
        $refund = SupplierRefund::find($id);
        if (!$refund) {
            return redirect()->back()->with('error','Refund not found.');
        }
        $refund->status = $status;
        $refund->update();
        return redirect()->back()->with('success','Refund status updated successfully.');
    }
}
