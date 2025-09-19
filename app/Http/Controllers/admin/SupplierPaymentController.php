<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\AdminActivity;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\SupplierPayment,supplier_payment')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        $payments = SupplierPayment::orderBy('id', 'DESC')->get();
        return view('admin.pages.supplier-payment.index', compact('payments'));
    }

    public function create(): View|Factory|Application
    {
        $accounts = Account::all();
        $suppliers = Supplier::all();
        return view('admin.pages.supplier-payment.create', compact('accounts', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'received_by' => 'nullable',
            'image' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('SupplierPayment-photo');
        }
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
            'image' => $image ? 'uploads/' . $image : null
        ]);
        return redirect()->route('supplier-payments.index')->with('success','Payment created successfully.');
    }

    public function edit($id): View|Factory|Application
    {
        $payment = SupplierPayment::find($id);
        $suppliers = Supplier::all();
        $accounts = Account::all();
        return view('admin.pages.supplier-payment.edit', compact('payment', 'accounts', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $payment = SupplierPayment::find($id);
        $request->validate([
            'supplier_id' => 'required',
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
            $image = 'uploads/' . $request->file('photo')->store('supplierPayment-photo');
        }
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
            'image' => $image ? 'uploads/' . $image : null,
            'status' => $request->status,
        ]);
        return redirect()->route('supplier-payments.index')->with('success','Payment updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $payment = SupplierPayment::find($id);
        if ($payment->image) {
            $previousImages = json_decode($payment->image, true);
            if ($previousImages) {
                foreach ($previousImages as $previousImage) {
                    $imagePath = public_path('uploads/' . $previousImage);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
        }
        $payment->delete();
        return redirect()->route('supplier-payments.index')->with('success','Payment deleted successfully.');
    }

    public function show($id): View|Factory|Application
    {
        $payment = SupplierPayment::findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(SupplierPayment::class, $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('admin.pages.supplier-payment.show', compact('payment', 'admins', 'activities'));
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('supplier-payments.index')->with('error', 'Invalid status.');
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
