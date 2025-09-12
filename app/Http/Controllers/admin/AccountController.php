<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Models\AccountTransfer;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('account-list')) {
                return redirect()->route('unauthorized.action');
            }

            return $next($request);
        })->only('index');
    }

    public function index()
    {
        $account = Account::all();
        $user = User::all();
        return view('admin.pages.account.index', compact('account', 'user'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'type' => 'required',
                'user_id' => 'required',
            ]);

            $account = new Account();
            $account->name = $request->name;
            $account->type = $request->type;
            $account->user_id = $request->user_id;
            $account->save();
            Toastr::success('Account Added Successfully', 'Success');
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
                'name' => 'required',
                'type' => 'required',
                'user_id' => 'required',
                'status' => 'required|in:active,inactive,pending'
            ]);
            $account = Account::findOrFail($id);
            $account->name = $request->name;
            $account->type = $request->type;
            $account->user_id = $request->user_id;
            $account->status = $request->status;
            $account->save();
            Toastr::success('Account Updated Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->delete();
            Toastr::success('Account Deleted Successfully', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function showTransaction($id) {
        $account = Account::findOrFail($id);

        $totalDebit = AccountTransaction::where('account_id', $id)
            ->where('transaction_type', 'out')
            ->sum('amount');
        $totalCredit = AccountTransaction::where('account_id', $id)
            ->where('transaction_type', 'in')
            ->sum('amount');
        $data = [
            'labels' => ['Debit', 'Credit'],
            'data' => [$totalDebit, $totalCredit],
        ];

        // Prepare arrays for monthly data
        $months = [];
        $monthlyDebit = [];
        $monthlyCredit = [];

        for ($i = 1; $i <= 12; $i++) {
            // Format month name
            $months[] = date('F', mktime(0, 0, 0, $i, 1));

            // Calculate total debit for the month
            $monthlyDebit[] = AccountTransaction::where('account_id', $id)
                ->where('transaction_type', 'out')
                ->whereMonth('created_at', $i)
                ->sum('amount');

            // Calculate total credit for the month
            $monthlyCredit[] = AccountTransaction::where('account_id', $id)
                ->where('transaction_type', 'in')
                ->whereMonth('created_at', $i)
                ->sum('amount');
        }

        $lineData = $this->getMonthlyModelWiseTransactions($id);
        $months = $lineData['months'];
        $monthlyData = $lineData['monthlyData'];

        $datasets = []; // To hold datasets for each model

        // Create datasets for each model
        foreach ($monthlyData as $model => $transactions) {
            $modelName = class_basename($model);
            $colors = generateColor($modelName);

            // Check if there's any debit transaction for the model
            if (array_sum($transactions['debit']) > 0) {
                // Prepare debit dataset for the model
                $datasets[] = [
                    'label' => $modelName . ' Debit',
                    'data' => array_values($transactions['debit']), // Get debit amounts for each month
                    'backgroundColor' => $colors['backgroundColor'], // Customize as needed
                    'borderColor' => $colors['borderColor'], // Customize as needed
                    'borderWidth' => 1,
                    'fill' => false,
                ];
            }

            // Check if there's any credit transaction for the model
            if (array_sum($transactions['credit']) > 0) {
                // Prepare credit dataset for the model
                $datasets[] = [
                    'label' => $modelName . ' Credit',
                    'data' => array_values($transactions['credit']), // Get credit amounts for each month
                    'backgroundColor' => $colors['backgroundColor'], // Customize as needed
                    'borderColor' => $colors['borderColor'], // Customize as needed
                    'borderWidth' => 1,
                    'fill' => false,
                ];
            }
        }

        $lineChartData = [
            'labels' => $months,
            'datasets' => $datasets,
        ];

        $transactionInfo = DB::table('account_transactions')->where('account_id', $account->id)->get();
        $pendingTransactions = AccountTransfer::where('from_account_id', $id)->where('status', '=', 'pending')->get();
        $accounts = Account::all();
        return view('admin.pages.account.showTransaction', compact('account', 'data', 'lineChartData', 'transactionInfo', 'accounts', 'pendingTransactions'));
    }

    public function getMonthlyModelWiseTransactions($accountId)
    {
        // Prepare arrays for month names and model transactions
        $months = [];
        $monthlyData = []; // To hold total amounts grouped by model

        // Get transactions grouped by month and model
        for ($i = 1; $i <= 12; $i++) {
            // Format month name
            $months[] = date('F', mktime(0, 0, 0, $i, 1));

            // Retrieve monthly transactions for the specific account, grouped by model and transaction type
            $monthlyTransactions = AccountTransaction::select(DB::raw('model, transaction_type, SUM(amount) as total_amount'))
                ->where('account_id', $accountId)
                ->whereMonth('created_at', $i)
                ->groupBy('model', 'transaction_type')
                ->get();

            // Store the total amounts in the array
            foreach ($monthlyTransactions as $transaction) {
                // Initialize model data if it doesn't exist
                if (!isset($monthlyData[$transaction->model])) {
                    $monthlyData[$transaction->model] = [
                        'debit' => array_fill(1, 12, 0),  // Initialize debit amounts
                        'credit' => array_fill(1, 12, 0), // Initialize credit amounts
                    ];
                }

                // Assign total amount to the corresponding model and type
                if ($transaction->transaction_type === 'out') {
                    $monthlyData[$transaction->model]['debit'][$i] = $transaction->total_amount; // For 'out', it's debit
                } else {
                    $monthlyData[$transaction->model]['credit'][$i] = $transaction->total_amount; // For 'in', it's credit
                }
            }
        }

        return [
            'months' => $months,
            'monthlyData' => $monthlyData,
        ];
    }
}
