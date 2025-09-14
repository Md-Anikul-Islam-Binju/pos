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
            $deposit->status = $request->status ?? 'pending';
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
            if ($deposit->status === 'approved') {
                Toastr::error('Approved deposit cannot be deleted', 'Error');
                return redirect()->route('deposit.section'); // redirect back to index page
            }
            $deposit->delete();
            Toastr::success('Deposit Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $deposit = Deposit::findOrFail($id);
        $originalStatus = $deposit->status;
        $newStatus = $request->status;

        if (!in_array($newStatus, ['pending', 'approved', 'rejected'])) {
            return redirect()->back()->with('error', 'Invalid status.');
        }

        $deposit->status = $newStatus;
        $deposit->save();

        // Trigger transaction/job if needed
        if (($originalStatus === 'pending' || $originalStatus === 'rejected') && $newStatus === 'approved') {
            $deposit->handleAccountTransaction($deposit, $deposit->account_id, $deposit->amount);
        }

        if ($originalStatus === 'approved' && ($newStatus === 'pending' || $newStatus === 'rejected')) {
            $deposit->handleAccountTransaction($deposit, $deposit->account_id, $deposit->amount);
        }

        return redirect()->back()->with('success', 'Deposit status updated successfully.');
    }
}
