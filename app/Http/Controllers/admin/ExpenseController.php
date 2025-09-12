<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yoeunes\Toastr\Facades\Toastr;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('expense-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $expense = Expense::all();
        $expenseCategory = ExpenseCategory::all();
        $account = Account::all();
        return view('admin.pages.expense.index', compact('expense', 'expenseCategory', 'account'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'category_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
            ]);

            $expense = new Expense();
            $expense->title = $request->title;
            $expense->category_id = $request->category_id;
            $expense->account_id = $request->account_id;
            $expense->amount = $request->amount;
            $expense->save();

            Toastr::success('Expense Added Successfully', 'Success');
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
                'title' => 'required',
                'category_id' => 'required',
                'account_id' => 'required',
                'amount' => 'required',
                'status' => 'required|in:pending,approved,rejected',
            ]);

            $expense = Expense::findOrFail($id);
            $expense->title = $request->title;
            $expense->category_id = $request->category_id;
            $expense->account_id = $request->account_id;
            $expense->amount = $request->amount;
            $expense->status = $request->status ?? $expense->status;
            $expense->save();

            Toastr::success('Expense Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            $expense->delete();
            Toastr::success('Expense Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function updateStatus($id, $status): RedirectResponse
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return redirect()->route('expense.section')->with('error', 'Invalid status.');
        }
        $expense = Expense::find($id);

        if (!$expense) {
            return redirect()->back()->with('error', 'Expense not found.');
        }
        $expense->status = $status;
        $expense->update();
        return redirect()->back()->with('success', 'Expense status updated successfully.');
    }
}
