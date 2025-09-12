<?php

namespace App\Http\Controllers\admin;

use App\Models\Account;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class SupplierPaymentController extends Controller
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
        $payments = SupplierPayment::all();
        return view('admin.supplierPayments.index', compact('payments'));
    }

    public function create()
    {
        $accounts = Account::all();
        $suppliers = Supplier::all();
        return view('admin.pages.supplier-payment.create', compact('accounts', 'suppliers'));
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
            SupplierPayment::create([
                'supplier_id' => $request->account_id,
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'details' => $request->details,
                'date' => $request->date,
                'received_by' => $request->received_by,
            ]);
            Toastr::success('Payment Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $payment = SupplierPayment::find($id);
        $suppliers = Supplier::all();
        $accounts = Account::all();
        return view('admin.pages.supplier-payment.edit', compact('payment', 'accounts', 'suppliers'));
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
            $payment = SupplierPayment::find($id);
            $account = Account::findOrFail($request->account_id);
        if ($account->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }
            return redirect()->back()->with('error', 'Insufficient Balance');
        }
        $accountId = $request->account_id ?? $payment->account_id;
        $payment->update([
            'supplier_id' => $request->supplier_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'received_by' => $request->received_by,
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
        $payment = SupplierPayment::findOrFail($id);
        return view('admin.pages.supplier-payment.show', compact('payment'));
    }

    public function destroy($id)
    {
        try {
            $payment = SupplierPayment::find($id);
            $payment->delete();
            Toastr::success('Payment Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.pages.supplier-payment.index')->with('error', 'Invalid status.');
        }
        $payment = SupplierPayment::find($id);
        if (!$payment) {
            return redirect()->back()->with('error','Payment not found.');
        }
        $payment->status = $status;
        $payment->update();
        return redirect()->back()->with('success','Payment status updated successfully.');
    }
}
