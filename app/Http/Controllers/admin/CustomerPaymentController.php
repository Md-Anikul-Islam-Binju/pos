<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateCustomerBalance;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class CustomerPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('customer-payment-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $payment = CustomerPayment::all();
        $account = Account::all();
        $customer = Customer::all();
        return view('admin.pages.customer-payment.index', compact('payment', 'account', 'customer'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'received_by' => 'required',
            ]);

            $payment = new CustomerPayment();
            $payment->customer_id = $request->customer_id;
            $payment->account_id = $request->account_id;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->received_by = $request->received_by;
            $payment->save();

            UpdateCustomerBalance::dispatch();

            Toastr::success('Payment Added Successfully', 'Success');
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
                'date' => 'required',
                'received_by' => 'required',
            ]);

            $payment = CustomerPayment::find($id);
            if (!$payment) return redirect()->back()->with('error', 'Payment not found.');
            $payment->customer_id = $request->customer_id;
            $payment->account_id = $request->account_id;
            $payment->amount = $request->amount;
            $payment->date = $request->date;
            $payment->received_by = $request->received_by;
            $payment->status = $request->status;
            $payment->save();

            UpdateCustomerBalance::dispatch();

            Toastr::success('Payment Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $payment = CustomerPayment::find($id);
            if (!$payment) return redirect()->back()->with('error', 'Payment not found.');
            $payment->delete();
            UpdateCustomerBalance::dispatch();
            Toastr::success('Payment Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('customer.payment.section')->with('error', 'Invalid status.');
        }
        $payment = CustomerPayment::find($id);
        if (!$payment) {
            return redirect()->back()->with('error', 'Payment not found.');
        }
        $payment->status = $status;
        $payment->update();
        UpdateCustomerBalance::dispatch();
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }
}
