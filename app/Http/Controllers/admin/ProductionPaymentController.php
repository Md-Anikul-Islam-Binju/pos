<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ProductionHouse;
use App\Models\ProductionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductionPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\ProductionPayment,production_payment')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index()
    {
        $payment = ProductionPayment::orderBy('id', 'DESC')->get();
        $account = Account::all();
        $house = ProductionHouse::all();
        return view('admin.pages.production-payment.index', compact('payment', 'account', 'house'));
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
            'photo' => 'nullable|image',
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
        return redirect()->route('production.payment.section')->with('success','Payment created successfully.');
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
            'photo' => 'nullable|image',
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
        return redirect()->route('production.payment.section')->with('success','Payment updated successfully.');
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
        return redirect()->route('production.payment.section')->with('success','Payment deleted successfully.');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('production.payment.section')->with('error', 'Invalid status.');
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
