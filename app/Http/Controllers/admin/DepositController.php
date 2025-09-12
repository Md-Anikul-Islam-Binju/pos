<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Deposit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('deposit-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }
    public function index()
    {
        $deposit = Deposit::all();
        $account = Account::all();
        return view('admin.pages.deposit.index', compact('deposit', 'account'));
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'account_id' => 'required',
                'amount' => 'required|numeric|min:0',
            ]);

            $deposit = new Deposit();
            $deposit->account_id = $request->account_id;
            $deposit->amount = $request->amount;
            $deposit->save();

            Toastr::success('Deposit Added Successfully', 'Success');
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
                'account_id' => 'required',
                'amount' => 'required|numeric|min:0',
                'status' => 'required',
            ]);

            $deposit = Deposit::findOrFail($id);
            $deposit->account_id = $request->account_id ?? $deposit->account_id;
            $deposit->amount = $request->amount;
            $deposit->status = $request->status;
            $deposit->save();

            Toastr::success('Deposit Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $deposit = Deposit::findOrFail($id);
            $deposit->delete();
            Toastr::success('Deposit Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.pages.deposit.index')->with('error', 'Invalid status.');
        }
        $deposit = Deposit::findOrFail($id);
        if (!$deposit) {
            return redirect()->back()->with('error', 'Deposit not found.');
        }
        $deposit->status = $status;
        $deposit->update();
        return redirect()->back()->with('success', 'Deposit status updated successfully.');
    }
}
