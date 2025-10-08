<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AccountTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\AccountTransfer,account_transfer')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index()
    {
        $transfer = AccountTransfer::orderBy('id', 'DESC')->get();
        $account = Account::all();
        return view('admin.pages.account-transfer.index', compact('transfer', 'account'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required',
            'to_account_id' => 'required',
            'amount' => 'required',
            'notes' => 'nullable',
            'photo' => 'nullable|image',
            'status' => 'required',
        ]);

        $image = '';

        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('transfer-photo');
        }

        $fromAccountInfo = Account::findOrFail($request->from_account_id);

        if ($fromAccountInfo->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }
            return redirect()->back()->with('error', 'Insufficient Balance');
        }

        AccountTransfer::create([
            'from_account_id' => $request->from_account_id,
            'to_account_id' => $request->to_account_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'image' => $image ? 'uploads/' . $image : null,
            'status' => $request->status,
        ]);

        return redirect()->route('account.transfer.section')->with('success', 'Transfer created successfully.');
    }

    public function update(Request $request, $id)
    {
        $transfer = AccountTransfer::find($id);

        $request->validate([
            'from_account_id' => 'required',
            'to_account_id' => 'required',
            'amount' => 'required',
            'notes' => 'nullable',
            'image' => 'nullable',
            'status' => 'required'
        ]);

        $fromAccountInfo = Account::findOrFail($request->from_account_id);

        if ($fromAccountInfo->balance < $request->amount) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Insufficient Balance'], 400);
            }

            return redirect()->back()->with('error', 'Insufficient Balance');
        }

        $image = $transfer->image ?? null;

        if ($request->hasFile('photo')) {
            if($transfer->image) {
                $prev_image = $transfer->image;
                if (file_exists($prev_image)) {
                    unlink($prev_image);
                }
            }

            $image = 'uploads/' . $request->file('photo')->store('transfer-photo');
        }

        $transfer->update([
            'from_account_id' => $request->from_account_id,
            'to_account_id' => $request->to_account_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
            'image' =>  $image,
            'status' => $request->status,
        ]);

        return redirect()->route('account.transfer.section')->with('success', 'Transfer updated successfully.');
    }


    public function destroy($id): RedirectResponse
    {
        $transfer = AccountTransfer::find($id);

        if ($transfer->image) {
            $imagePath = public_path($transfer->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $transfer->delete();

        return redirect()->route('account.transfer.section')->with('success', 'Transfer deleted successfully.');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('account.transfer.section')->with('error', 'Invalid status.');
        }

        $transfer = AccountTransfer::find($id);

        if (!$transfer) {
            return redirect()->back()->with('error', 'Transfer not found.');
        }

        $transfer->status = $status;
        $transfer->update();

        return redirect()->back()->with('success', 'Transfer status updated successfully.');
    }
}
