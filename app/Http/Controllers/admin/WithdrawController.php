<?php

namespace App\Http\Controllers\admin;

use App\Models\Account;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class WithdrawController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('withdraw-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $withdraws = Withdraw::all();
        return view('admin.pages.withdraw.index', compact('withdraws'));
    }

    public function create()
    {
        $accounts = Account::all();
        return view('admin.withdraws.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'account_id' => 'required',
                'amount' => 'required',
                'notes' => 'nullable',
            ]);

            $account = Account::findOrFail($request->account_id);
            if ($account->balance < $request->amount) {
                if ($request->ajax()) {
                    return response()->json(['message' => 'Insufficient Balance'], 400);
                }
                return redirect()->back()->with('error', 'Insufficient Balance');
            }
            Withdraw::create([
                'account_id' => $request->account_id,
                'amount' => $request->amount,
                'notes' => $request->notes,
            ]);
            Toastr::success('Withdraw Added Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            // Handle the exception here
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $withdraw = Withdraw::find($id);
        $accounts = Account::all();
        return view('admin.pages.withdraw.edit', compact('withdraw', 'accounts'));
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'account_id' => 'required',
                'amount' => 'required',
                'notes' => 'nullable',
                'status' => 'required',
            ]);

            $account = Account::findOrFail($request->account_id);
        if ($account->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }
            return redirect()->back()->with('error', 'Insufficient Balance');
        }
        $accountId = $request->account_id ?? $withdraw->account_id;
        $withdraw->update([
            'account_id' => $accountId,
            'amount' => $request->amount,
            'status' => $request->status,
            'notes' => $request->notes,
            'image' =>  $image,
        ]);
            Toastr::success('Withdraw Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $withdraw = Withdraw::findOrFail($id);
        return view('admin.pages.withdraw.show', compact('withdraw'));
    }

    public function destroy($id)
    {
        try {
            $withdraw = Withdraw::find($id);
            $withdraw->delete();
            Toastr::success('Withdraw Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('admin.pages.withdraw.index')->with('error', 'Invalid status.');
        }
        $withdraw = Withdraw::find($id);
        if (!$withdraw) {
            return redirect()->back()->with('error','Withdraw not found.');
        }
        $withdraw->status = $status;
        $withdraw->update();
        return redirect()->back()->with('success','Withdraw status updated successfully.');
    }
}
