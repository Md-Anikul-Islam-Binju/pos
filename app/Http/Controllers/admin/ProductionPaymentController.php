<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\AdminActivity;
use App\Models\ProductionHouse;
use App\Models\ProductionPayment;
use App\Models\Supplier;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductionPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\ProductionPayment,production_payment')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        $payments = ProductionPayment::orderBy('id', 'DESC')->get();
        return view('admin.pages.production-payment.index', compact('payments'));
    }

    public function create(): View|Factory|Application
    {
        $accounts = Account::all();
        $houses = ProductionHouse::all();
        return view('admin.pages.production-payment.create', compact('accounts', 'houses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'house_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'date' => 'required',
            'received_by' => 'nullable',
            'image' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('ProductionPayment-photo');
        }
        $account = Account::findOrFail($request->account_id);
        if ($account->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }
            return redirect()->back()->with('error', 'Insufficient Balance');
        }
        ProductionPayment::create([
            'house_id' => $request->house_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'received_by' => $request->received_by,
            'image' => $image ? 'uploads/' . $image : null
        ]);
        return redirect()->route('production-payments.index')->with('success','Payment created successfully.');
    }

    public function edit($id): View|Factory|Application
    {
        $payment = ProductionPayment::find($id);
        $houses = ProductionHouse::all();
        $accounts = Account::all();
        return view('admin.pages.production-payment.edit', compact('payment', 'accounts', 'houses'));
    }

    public function update(Request $request, $id)
    {
        $payment = ProductionPayment::find($id);
        $request->validate([
            'house_id' => 'required',
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
            $image = 'uploads/' . $request->file('photo')->store('ProductionPayment-photo');
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
            'house_id' => $request->house_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'date' => $request->date,
            'received_by' => $request->received_by,
            'image' => $image ? 'uploads/' . $image : null,
            'status' => $request->status,
        ]);
        return redirect()->route('production-payments.index')->with('success','Payment updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $payment = ProductionPayment::find($id);
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
        return redirect()->route('production-payments.index')->with('success','Payment deleted successfully.');
    }

    public function show($id): View|Factory|Application
    {
        $payment = ProductionPayment::findOrFail($id);
        $admins = User::all();
        $activities = AdminActivity::getActivities(ProductionPayment::class, $id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        return view('admin.pages.production-payment.show', compact('payment', 'admins', 'activities'));
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('production-payments.index')->with('error', 'Invalid status.');
        }
        $payment = ProductionPayment::find($id);
        if (!$payment) {
            return redirect()->back()->with('error','Payment not found.');
        }
        $payment->status = $status;
        $payment->update();
        return redirect()->back()->with('success','Payment status updated successfully.');
    }
}
