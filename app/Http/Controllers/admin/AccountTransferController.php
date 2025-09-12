<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AccountTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('account-transfer-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $accountTransfer = AccountTransfer::with(['fromAccount','toAccount'])->get();
        $accounts = Account::all();
        return view('admin.pages.accountTransfer.index', compact('accountTransfer', 'accounts'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'from_account_id' => 'required',
                'to_account_id' => 'required',
                'amount' => 'required',
            ]);

            $fromAccountInfo = Account::findOrFail($request->from_account_id);

            if ($fromAccountInfo->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }
                return redirect()->back()->with('error', 'Insufficient Balance');
            }

            $accountTransfer = new AccountTransfer();
            $accountTransfer->from_account_id = $request->from_account_id;
            $accountTransfer->to_account_id = $request->to_account_id;
            $accountTransfer->amount = $request->amount;
            $accountTransfer->save();
            Toastr::success('Transfer Added Successfully', 'Success');
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
                'from_account_id' => 'required',
                'to_account_id' => 'required',
                'amount' => 'required',
            ]);
            $accountTransfer = AccountTransfer::findOrFail($id);

            $fromAccountInfo = Account::findOrFail($request->from_account_id);

            if ($fromAccountInfo->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }

                return redirect()->back()->with('error', 'Insufficient Balance');
            }

            $accountTransfer->from_account_id = $request->from_account_id;
            $accountTransfer->to_account_id = $request->to_account_id;
            $accountTransfer->amount = $request->amount;
            $accountTransfer->status = $request->status;
            $accountTransfer->save();
            Toastr::success('Transfer Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $accountTransfer = AccountTransfer::find($id);
            $accountTransfer->delete();
            Toastr::success('Transfer Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): \Illuminate\Http\RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('account-transfer.section')->with('error', 'Invalid status.');
        }

        $accountTransfer = AccountTransfer::find($id);

        if (!$accountTransfer) {
            Toastr::error('Transfer not found.', 'Error');
            return redirect()->back();
        }

        $accountTransfer->status = $status;
        $accountTransfer->update();

        Toastr::success('Transfer status updated successfully.', 'Success');
        return redirect()->back();
    }
}
