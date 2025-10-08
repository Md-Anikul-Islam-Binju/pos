<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateCustomerBalance;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerRefund;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\CustomerRefund,customer_refund')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index()
    {
        UpdateCustomerBalance::dispatch();
        $refund = CustomerRefund::orderBy('id', 'DESC')->get();
        $account = Account::all();
        $customer = Customer::all();
        return view('admin.pages.customer-refund.index', compact('refund', 'account', 'customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'refund_by' => 'nullable',
            'image' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('customerRefund-photo');
        }
        CustomerRefund::create([
            'customer_id' => $request->customer_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'refund_by' => $request->refund_by,
            'image' => $image ? 'uploads/' . $image : null
        ]);
        UpdateCustomerBalance::dispatch();
        return redirect()->route('customer.refund.section')->with('success', 'Refund created successfully.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $refund = CustomerRefund::find($id);
        $request->validate([
            'customer_id' => 'required',
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
            $image = 'uploads/' . $request->file('photo')->store('customerRefund-photo');
        }
        $accountId = $request->account_id ?? $refund->account_id;
        $refund->update([
            'customer_id' => $request->customer_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'refund_by' => $request->refund_by,
            'image' => $image ? 'uploads/' . $image : null,
            'status' => $request->status,
        ]);
        UpdateCustomerBalance::dispatch();

        return redirect()->route('customer.refund.section')->with('success', 'Refund updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $refund = CustomerRefund::find($id);

        if ($refund->image) {
            $previousImages = json_decode($refund->images, true);
            if ($previousImages) {
                foreach ($previousImages as $previousImage) {
                    $imagePath = public_path('uploads/' . $previousImage);
                    if (file_exists($imagePath)) {
                        unlink($imagePath); // Delete the image file
                    }
                }
            }
        }
        $refund->delete();
        UpdateCustomerBalance::dispatch();

        return redirect()->route('customer.refund.section')->with('success', 'Refund deleted successfully.');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('customer.refund.section')->with('error', 'Invalid status.');
        }
        $refund = CustomerRefund::find($id);
        if (!$refund) {
            return redirect()->back()->with('error', 'Refund not found.');
        }
        $refund->status = $status;
        $refund->update();
        UpdateCustomerBalance::dispatch();

        return redirect()->back()->with('success', 'Refund status updated successfully.');
    }
}
