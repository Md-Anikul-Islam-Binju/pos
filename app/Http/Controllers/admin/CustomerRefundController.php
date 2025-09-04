<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateCustomerBalance;
use App\Models\Customer;
use App\Models\CustomerRefund;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class CustomerRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('customer-refund-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        UpdateCustomerBalance::dispatch();

        $refund = CustomerRefund::all();
        $account = Account::all();
        $customer = Customer::all();

        return view('admin.pages.customer-refund.index', compact('refund', 'account', 'customer'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'details' => 'required',
                'date' => 'required',
                'refund_by' => 'required',
            ]);

            $refund = new CustomerRefund();

            $refund->customer_id = $request->customer_id;
            $refund->account_id = $request->account_id;
            $refund->amount = $request->amount;
            $refund->date = $request->date;
            $refund->refund_by = $request->refund_by;
            $refund->save();

            UpdateCustomerBalance::dispatch();

            Toastr::success('Refund Added Successfully', 'Success');
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
                'customer_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'details' => 'required',
                'date' => 'required',
                'refund_by' => 'required',
            ]);

            $refund = CustomerRefund::find($id);

            $refund->customer_id = $request->customer_id;
            $refund->account_id = $request->account_id;
            $refund->amount = $request->amount;
            $refund->date = $request->date;
            $refund->refund_by = $request->refund_by;
            $refund->status = $request->status;
            $refund->save();

            UpdateCustomerBalance::dispatch();

            Toastr::success('Refund Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $refund = CustomerRefund::find($id);
            $refund->delete();
            Toastr::success('Refund Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.pages.customer-refund.index')->with('error', 'Invalid status.');
        }

        $refund = CustomerRefund::find($id);

        if (!$refund) {
            return redirect()->back()->with('error', 'Payment not found.');
        }

        $refund->status = $status;
        $refund->update();

        UpdateCustomerBalance::dispatch();

        return redirect()->back()->with('success', 'Refund status updated successfully.');
    }
}
