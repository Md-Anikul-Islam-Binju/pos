<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use App\Models\AdminActivity;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkModelStatus:App\Models\Expense,expense')
            ->only(['edit', 'update', 'updateStatus', 'destroy']);
    }

    public function index(): View|Factory|Application
    {
        $expense = Expense::orderBy('id', 'DESC')->latest()->get();
        $categories = ExpenseCategory::orderBy('id', 'DESC')->get();
        $accounts = Account::where('status', '=', 'active')->orderBy('id', 'DESC')->get();
        return view('admin.pages.expense.index', compact('expense', 'categories', 'accounts'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required',
            'category_id' => 'required',
            'account_id' => 'required',
            'amount' => 'required',
            'details' => 'nullable',
            'images' => 'nullable',
        ]);
        $image = '';
        if ($request->hasFile('photo')) {
            $image = $request->file('photo')->store('expense-photo');
        }
        $expense = Expense::create([
            'title' => $request->title,
            'expense_category_id' => $request->category_id,
            'account_id' => $request->account_id,
            'amount' => $request->amount,
            'details' => $request->details,
            'images' => $image ? 'uploads/' . $image : null
        ]);
        return redirect()->route('expense.section')->with('success', 'Expense Created Successfully');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $expense = Expense::find($id);
        $request->validate([
            'title' => 'required',
            'category_id' => 'required',
            'account_id' => 'required',
            'details' => 'nullable',
            'status' => 'required',
            'images' => 'nullable',
        ]);
        $image = $expense->images ?? null;
        if ($request->hasFile('photo')) {
            // Delete previous image
            if($expense->images) {
                $prev_image = $expense->images;
                if (file_exists($prev_image)) {
                    unlink($prev_image);
                }
            }
            $image = 'uploads/' . $request->file('photo')->store('expense-photo');
        }

        $accountId = $request->account_id ?? $expense->account_id;

        $expense->update([
            'title' => $request->title,
            'expense_category_id' => $request->category_id,
            'account_id' => $accountId,
            'amount' => $request->amount,
            'status' => $request->status,
            'details' => $request->details,
            'images' => 'uploads/' . $image,
        ]);

        return redirect()->route('expense.section')->with('success', 'Expense Updated Successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $expense = Expense::find($id);
        if ($expense->status == 'approved') {
            return redirect()->back()->with('error', "Approved Expense can't be deleted.");
        }
        if ($expense->images) {
            $previousImages = json_decode($expense->images, true);
            if ($previousImages) {
                foreach ($previousImages as $previousImage) {
                    $imagePath = public_path('uploads/' . $previousImage);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }
        }
        $expense->delete();
        return redirect()->route('expense.section')->with('success', 'Expense Deleted Successfully');
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        // Validate the status
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('expense.section')->with('error', 'Invalid status.');
        }
        // Find the asset
        $asset = Expense::find($id);
        if (!$asset) {
            return redirect()->back()->with('error', 'Expense not found.');
        }
        // Update the asset status
        $asset->status = $status;
        $asset->update();
        return redirect()->back()->with('success', 'Expense status updated successfully.');
    }
}
