<?php

namespace App\Http\Controllers\admin;

use App\Models\Account;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\SupplierRefund;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class SupplierRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('supplier-payment-list')) {
                return redirect()->route('unauthorized.action');
            }
            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $refunds = SupplierRefund::all();
        return view('admin.pages.supplier-refunds.index', compact('refunds'));
    }

    public function create()
    {
        $accounts = Account::all();
        $suppliers = Supplier::all();
        return view('admin.pages.supplier-refunds.create', compact('accounts', 'suppliers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'supplier_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'details' => 'nullable',
                'date' => 'required',
                'received_by' => 'nullable',
            ]);
            $account = Account::findOrFail($request->account_id);
            if ($account->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }
                return redirect()->back()->with('error', 'Insufficient Balance');
            }
            SupplierRefund::create([
                'supplier_id' => $request->account_id,
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'details' => $request->details,
                'date' => $request->date,
                'refund_by' => $request->refund_by,
            ]);
            Toastr::success('Refund Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $refund = SupplierRefund::find($id);
        $suppliers = Supplier::all();
        $accounts = Account::all();
        return view('admin.pages.supplier-refund.edit', compact('refund', 'accounts', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'supplier_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'details' => 'nullable',
                'date' => 'required',
                'received_by' => 'nullable',
                'status' => 'required'
            ]);
            $refund = SupplierRefund::find($id);
            $account = Account::findOrFail($request->account_id);
            if ($account->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }
                return redirect()->back()->with('error', 'Insufficient Balance');
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
            'status' => $request->status,
        ]);
            Toastr::success('Payment Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $refund = SupplierRefund::findOrFail($id);
        return view('admin.pages.supplier-refund.show', compact('refund'));
    }

    public function destroy($id)
    {
        try {
            $refund = SupplierRefund::find($id);
            $refund->delete();
            Toastr::success('Refund Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.pages.supplier-refund.index')->with('error', 'Invalid status.');
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
