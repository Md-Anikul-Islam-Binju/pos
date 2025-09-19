<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateCustomerBalance;
use App\Models\Account;
use App\Models\AdminActivity;
use App\Models\Customer;
use App\Models\CustomerRefund;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerRefundController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\CustomerRefund,customer_refund')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        UpdateCustomerBalance::dispatch();
        $refunds = CustomerRefund::orderBy('id', 'DESC')->get();
        return view('admin.pages.customer-refund.index', compact('refunds'));
    }

    public function create(): View|Factory|Application
    {
        $accounts = Account::all();
        $customers = Customer::all();
        UpdateCustomerBalance::dispatch();
        return view('admin.pages.customer-refund.create', compact('accounts', 'customers'));
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
        return redirect()->route('customer-refunds.index')->with('success', 'Refund created successfully.');
    }

    public function edit($id): View|Factory|Application
    {
        $refund = CustomerRefund::find($id);
        $customers = Customer::all();
        $accounts = Account::all();
        UpdateCustomerBalance::dispatch();
        return view('admin.pages.customer-refund.edit', compact('refund', 'accounts', 'customers'));
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

        return redirect()->route('customer-refunds.index')->with('success', 'Refund updated successfully.');
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

        return redirect()->route('customer-refunds.index')->with('success', 'Refund deleted successfully.');
    }

    public function show($id): View|Factory|Application
    {
        UpdateCustomerBalance::dispatch();
        $refund = CustomerRefund::findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(CustomerRefund::class, $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('admin.pages.customer-refund.show', compact('refund', 'admins', 'activities'));
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('customer-refunds.index')->with('error', 'Invalid status.');
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
