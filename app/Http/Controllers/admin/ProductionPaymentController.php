<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ProductionHouse;
use App\Models\ProductionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class ProductionPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('production-payment-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $productionPayment = ProductionPayment::all();
        $account = Account::all();
        $productionHouse = ProductionHouse::all();
        return view('admin.pages.production-payment.index', compact('productionPayment', 'account', 'productionHouse'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'production_house_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'received_by' => 'required',
            ]);

            $account = Account::findOrFail($request->account_id);
            if ($account->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }
                return redirect()->back()->with('error', 'Insufficient Balance');
            }

            $productionPayment = new ProductionPayment();
            $productionPayment->production_house_id = $request->production_house_id;
            $productionPayment->account_id = $request->account_id;
            $productionPayment->amount = $request->amount;
            $productionPayment->date = $request->date;
            $productionPayment->received_by = $request->received_by;

            $productionPayment->save();

            Toastr::success('Production Payment Added Successfully', 'Success');
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
                'production_house_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'date' => 'required',
                'received_by' => 'required',
            ]);

            $productionPayment = ProductionPayment::find($id);
            $account = Account::findOrFail($request->account_id);

            if ($account->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }
                return redirect()->back()->with('error', 'Insufficient Balance');
            }

            $accountId = $request->account_id ?? $productionPayment->account_id;

            $productionPayment->production_house_id = $request->production_house_id;
            $productionPayment->account_id = $request->account_id;
            $productionPayment->amount = $request->amount;
            $productionPayment->date = $request->date;
            $productionPayment->received_by = $request->received_by;
            $productionPayment->status = $request->status;

            $productionPayment->save();

            Toastr::success('Production payment Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $productionPayment = ProductionPayment::find($id);
            $productionPayment->delete();

            Toastr::success('Production Payment Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.pages.production-payment.index')->with('error', 'Invalid status.');
        }

        $productionPayment = ProductionPayment::find($id);

        if (!$productionPayment) {
            return redirect()->back()->with('error','Payment not found.');
        }

        $productionPayment->status = $status;
        $productionPayment->update();

        return redirect()->back()->with('success','Production Payment status updated successfully.');
    }
}
