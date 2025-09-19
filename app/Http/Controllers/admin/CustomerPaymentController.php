<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateCustomerBalance;
use App\Models\Account;
use App\Models\AdminActivity;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\CustomerPayment,customer_payment')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        UpdateCustomerBalance::dispatch();
        $payments = CustomerPayment::orderBy('id', 'DESC')->get();
        return view('admin.pages.customer-payment.index', compact('payments'));
    }

    public function create(): View|Factory|Application
    {
        $accounts = Account::all();
        $customers = Customer::all();
        UpdateCustomerBalance::dispatch();
        return view('admin.pages.customer-payment.create', compact('accounts', 'customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'received_by' => 'nullable',
            'image' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('customerPayment-photo');
        }
        CustomerPayment::create([
            'customer_id' => $request->customer_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'received_by' => $request->received_by,
            'image' => $image ? 'uploads/' . $image : null
        ]);
        UpdateCustomerBalance::dispatch();
        return redirect()->route('customer-payments.index')->with('success', 'Payment created successfully.');
    }

    public function edit($id): View|Factory|Application
    {
        $payment = CustomerPayment::find($id);
        $customers = Customer::all();
        $accounts = Account::all();
        UpdateCustomerBalance::dispatch();
        return view('admin.pages.customer-payment.edit', compact('payment', 'accounts', 'customers'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $payment = CustomerPayment::find($id);
        $request->validate([
            'customer_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'received_by' => 'nullable',
            'image' => 'nullable',
            'status' => 'required'
        ]);
        $image = $payment->image ?? null;
        if ($request->hasFile('photo')) {
            if($payment->image) {
                $prev_image = $payment->image;
                if (file_exists($prev_image)) {
                    unlink($prev_image);
                }
            }
            $image = 'uploads/' . $request->file('photo')->store('customerPayment-photo');
        }
        $accountId = $request->account_id ?? $payment->account_id;
        $payment->update([
            'customer_id' => $request->customer_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'received_by' => $request->received_by,
            'image' => $image ? 'uploads/' . $image : null,
            'status' => $request->status,
        ]);
        UpdateCustomerBalance::dispatch();

        return redirect()->route('customer-payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $payment = CustomerPayment::find($id);

        if ($payment->image) {
            $previousImages = json_decode($payment->images, true);
            if ($previousImages) {
                foreach ($previousImages as $previousImage) {
                    $imagePath = public_path('uploads/' . $previousImage);
                    if (file_exists($imagePath)) {
                        unlink($imagePath); // Delete the image file
                    }
                }
            }
        }
        $payment->delete();
        UpdateCustomerBalance::dispatch();

        return redirect()->route('customer-payments.index')->with('success', 'Payment deleted successfully.');
    }

    public function show($id): View|Factory|Application
    {
        UpdateCustomerBalance::dispatch();
        $payment = CustomerPayment::findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(CustomerPayment::class, $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('admin.pages.customer-payment.show', compact('payment', 'admins', 'activities'));
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('customer-payments.index')->with('error', 'Invalid status.');
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
