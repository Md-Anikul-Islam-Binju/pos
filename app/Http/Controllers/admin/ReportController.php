<?php

namespace App\Http\Controllers\admin;

use App\Models\Sell;
use App\Models\Size;
use App\Models\User;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Account;
use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Showroom;
use App\Models\Supplier;
use App\Models\Withdraw;
use App\Models\Warehouse;
use App\Models\CronJobLog;
use App\Models\RawMaterial;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\AccountTransfer;
use App\Models\ExpenseCategory;
use App\Models\RawMaterialStock;
use App\Models\AccountTransaction;
use App\Models\RawMaterialPurchase;
use App\Http\Controllers\Controller;
use App\Models\ProductStockTransfer;
use App\Models\RawMaterialStockTransfer;

class ReportController extends Controller
{
    public function rawMaterialStockReports(Request $request)
    {
        // Get all relevant data for filters
        $rawMaterials = RawMaterial::all();
        $warehouses = Warehouse::all();
        $colors = Color::all();
        $brands = Brand::all();
        $sizes = Size::all();

        // Start building the query
        $query = RawMaterialStock::with(['raw_material', 'warehouse', 'color', 'brand', 'size']);

        // Apply filters based on the request
        if ($request->filled('materialId')) {
            $query->where('raw_material_id', $request->materialId);
        }
        if ($request->filled('warehouseId')) {
            $query->where('warehouse_id', $request->warehouseId);
        }
        if ($request->filled('colorId')) {
            $query->where('color_id', $request->colorId);
        }
        if ($request->filled('brandId')) {
            $query->where('brand_id', $request->brandId);
        }
        if ($request->filled('sizeId')) {
            $query->where('size_id', $request->sizeId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $stocks = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['stocks' => $stocks]);
        }

        return view('admin.reports.rawMaterialStocks', compact('stocks', 'rawMaterials', 'warehouses', 'colors', 'brands', 'sizes'));
    }

    public function productStockReports(Request $request)
    {
        // Get all relevant data for filters
        $products = Product::all();
        $showrooms = Showroom::all();
        $colors = Color::all();
        $brands = Brand::all();
        $sizes = Size::all();

        // Start building the query
        $query = ProductStock::with(['product', 'color', 'brand', 'size', 'showroom']);

        // Apply filters based on the request
        if ($request->filled('productId')) {
            $query->where('product_id', $request->productId);
        }
        if ($request->filled('showroomId')) {
            $query->where('showroom_id', $request->showroomId);
        }
        if ($request->filled('colorId')) {
            $query->where('color_id', $request->colorId);
        }
        if ($request->filled('brandId')) {
            $query->where('brand_id', $request->brandId);
        }
        if ($request->filled('sizeId')) {
            $query->where('size_id', $request->sizeId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $stocks = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['stocks' => $stocks]);
        }

        return view('admin.reports.productStocks', compact('stocks', 'products', 'showrooms', 'colors', 'brands', 'sizes'));
    }

    public function sellReports(Request $request)
    {
        // Get all relevant data for filters
        $customers = Customer::all();
        $salesmen = User::where('type', '=', 'salesman')->get();
        $accounts = Account::all();
        $defaultCurrency = Currency::where('is_default', true)->first();

        // Start building the query
        $query = Sell::with(['customer', 'salesman', 'account', 'currency']);

        // Apply filters based on the request
        if ($request->filled('customerId')) {
            $query->where('customer_id', $request->customerId);
        }
        if ($request->filled('salesmanId')) {
            $query->where('salesman_id', $request->salesmanId);
        }
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $sells = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['sells' => $sells]);
        }

        return view('admin.reports.sells', compact(['sells', 'customers', 'accounts', 'salesmen', 'defaultCurrency']));
    }

    public function assetReports(Request $request)
    {
        $categories = AssetCategory::all();
        $accounts = Account::all();

        // Start building the query
        $query = Asset::with(['category', 'account']);

        // Apply filters based on the request
        if ($request->filled('categoryId')) {
            $query->where('asset_category_id', $request->categoryId);
        }
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $assets = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['assets' => $assets]);
        }

        return view('admin.reports.assets', compact('assets', 'categories', 'accounts'));
    }

    public function expenseReports(Request $request)
    {
        $categories = ExpenseCategory::all();
        $accounts = Account::all();

        // Start building the query
        $query = Expense::with(['category', 'account']);

        // Apply filters based on the request
        if ($request->filled('categoryId')) {
            $query->where('asset_category_id', $request->categoryId);
        }
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $expenses = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['expenses' => $expenses]);
        }

        return view('admin.reports.expenses', compact('expenses', 'categories', 'accounts'));
    }

    public function rawMaterialPurchaseReports(Request $request)
    {
        $suppliers = Supplier::all();
        $warehouses = Warehouse::all();
        $accounts = Account::all();

        // Start building the query
        $query = RawMaterialPurchase::with(['supplier', 'warehouse', 'account']);

        // Apply filters based on the request
        if ($request->filled('supplierId')) {
            $query->where('supplier_id', $request->supplierId);
        }
        if ($request->filled('warehouseId')) {
            $query->where('warehouse_id', $request->warehouseId);
        }
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Filter by PurchaseStart and PurchaseEnd dates
        if ($request->filled('purchaseStartDate') && $request->filled('purchaseEndDate')) {
            $query->whereBetween('purchase_date', [$request->purchaseStartDate, $request->purchaseEndDate]);
        } elseif ($request->filled('purchaseStartDate')) {
            $query->whereDate('purchase_date', '>=', $request->purchaseStartDate);
        } elseif ($request->filled('purchaseEndDate')) {
            $query->whereDate('purchase_date', '<=', $request->purchaseEndDate);
        }

        // Get the filtered stocks
        $purchases = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['purchases' => $purchases]);
        }

        return view('admin.reports.rawMaterialPurchases', compact('purchases', 'suppliers', 'warehouses', 'accounts'));
    }

    public function balanceSheetReports(Request $request)
    {
        $accounts = Account::all();

        // Start building the query with account and transaction data
        $query = AccountTransaction::with(['account']);

        // Apply filters based on the request
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }
        if ($request->filled('transactionType')) {
            $query->where('transaction_type', $request->transactionType);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Fetch the filtered transactions
        $transactions = $query->get();

        // Calculate totals, balance, and latest date, filtering out accounts without transactions
        $accountsWithBalances = $accounts->map(function ($account) use ($transactions) {
            $accountTransactions = $transactions->where('account_id', $account->id);

            // Only proceed if there are transactions for this account
            if ($accountTransactions->isEmpty()) {
                return null; // No transactions, so we skip this account
            }

            // Calculate total in, total out, and balance
            $totalIn = $accountTransactions->where('transaction_type', 'in')->sum('amount');
            $totalOut = $accountTransactions->where('transaction_type', 'out')->sum('amount');
            $balance = $totalIn - $totalOut;

            // Get the latest transaction date for the account
            $latestDate = $accountTransactions->max('created_at');

            return [
                'account' => $account,
                'total_in' => $totalIn,
                'total_out' => $totalOut,
                'balance' => $balance,
                'latest_date' => $latestDate
            ];
        })->filter(); // Remove null values from the collection

        // Calculate overall totals for Total In, Total Out, and Balance from all transactions
        $totalInSum = $transactions->where('transaction_type', 'in')->sum('amount');
        $totalOutSum = $transactions->where('transaction_type', 'out')->sum('amount');
        $balanceSum = $totalInSum - $totalOutSum;

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['accountsWithBalances' => $accountsWithBalances]);
        }

        return view('admin.reports.balanceSheets', compact('accountsWithBalances', 'transactions', 'accounts', 'totalInSum', 'totalOutSum', 'balanceSum'));
    }

    public function depositBalanceSheet(Request $request)
    {
        $accounts = Account::all();

        // Start building the query
        $query = Deposit::with(['account']);

        // Apply filters based on the request
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $deposits = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['deposits' => $deposits]);
        }

        return view('admin.reports.depositBalanceSheets', compact('deposits', 'accounts'));
    }

    public function withdrawBalanceSheet(Request $request)
    {
        $accounts = Account::all();

        // Start building the query
        $query = Withdraw::with(['account']);

        // Apply filters based on the request
        if ($request->filled('accountId')) {
            $query->where('account_id', $request->accountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $withdraws = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['withdraws' => $withdraws]);
        }

        return view('admin.reports.withdrawBalanceSheets', compact('withdraws', 'accounts'));
    }

    public function transferBalanceSheet(Request $request)
    {
        $accounts = Account::all();

        // Start building the query
        $query = AccountTransfer::with(['fromAccount', 'toAccount', 'accountTransaction']);


        // Apply filters based on the request
        if ($request->filled('fromAccountId')) {
            $query->where('from_account_id', $request->fromAccountId);
        }
        if ($request->filled('toAccountId')) {
            $query->where('to_account_id', $request->toAccountId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $transfers = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['transfers' => $transfers]);
        }

        return view('admin.reports.transferBalanceSheets', compact('transfers', 'accounts'));
    }

    public function sellProfitLoss(Request $request)
    {
        $defaultCurrency = Currency::where('is_default', true)->first();
        // Start building the query
        $query = Sell::with(['productStock', 'sell_stocks', 'account', 'currency']);

        // Apply filters based on the request
        if ($request->filled('status')) {
            // Get the default currency
            $defaultCurrency = Currency::where('is_default', true)->first();

            if ($defaultCurrency) {
                // Perform the query with currency conversion
                $query->join('sell_stocks', 'sells.id', '=', 'sell_stocks.sell_id')
                    ->join('currencies', 'sells.currency_id', '=', 'currencies.id') // Join with currencies table
                    ->selectRaw('sells.id, SUM(sell_stocks.cost) as total_cost, sells.net_total, sells.currency_id, currencies.rate')
                    ->groupBy('sells.id', 'sells.net_total', 'sells.currency_id', 'currencies.rate');

                if (strtolower($request->status) === 'profit') {
                    // Compare the converted net_total to cost
                    $query->havingRaw('SUM(sell_stocks.cost) < (sells.net_total * currencies.rate)');
                } elseif (strtolower($request->status) === 'loss') {
                    // Compare the converted net_total to cost
                    $query->havingRaw('SUM(sell_stocks.cost) >= (sells.net_total * currencies.rate)');
                }

                // Apply conversion to cost (if needed)
                $query->selectRaw('SUM(sell_stocks.cost * currencies.rate) as converted_cost');
            }
        }




        // Apply date filters if provided
        if ($request->filled('startDate')) {
            $query->whereDate('created_at', '>=', $request->startDate);
        }
        if ($request->filled('endDate')) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Apply "Today's Data" filter if selected from the dropdown
        if ($request->dataRange === 'today') {
            $query->whereDate('created_at', \Carbon\Carbon::today());
        }

        // Get the filtered stocks
        $sells = $query->get();


        // Sum of net totals converted to default currency
        $totalNetTotal = $sells->sum(function ($sell) {
            $converted = getDefaultCurrencyConvertedPrice($sell);
            return $converted ? $converted['net_total'] : 0;
        });

        // Sum of costs converted to default currency
        $totalCost = $sells->sum(function ($sell) {
            return $sell->sell_stocks->first()->cost ?? 0;
        });

        $totalAmount = $sells->sum(function ($sell) {
            // Convert net_total to default currency
            $converted = getDefaultCurrencyConvertedPrice($sell);

            // Get the cost (without conversion)
            $cost = $sell->sell_stocks->first()->cost ?? 0;

            // If the conversion was successful, calculate the amount
            if ($converted) {
                $amount = $cost < $converted['net_total'] ? $converted['net_total'] - $cost : $cost - $converted['net_total'];
                return $amount;
            }

            // If no conversion data, return 0
            return 0;
        });

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['sells' => $sells]);
        }

        return view('admin.reports.sellProfitLoss', compact(['sells', 'totalNetTotal', 'totalCost', 'totalAmount', 'defaultCurrency']));
    }

    public function cronJobLogs()
    {
        $logs = CronJobLog::orderBy('id', 'desc')->get();

        return view('admin.reports.cronJobLogs', compact('logs'));
    }

    public function productTransferReports(Request $request)
    {
        $showrooms = Showroom::all();
        // Start building the query
        $query = ProductStockTransfer::with(['fromShowroom', 'toShowroom']);

        // Apply filters based on the request
        if ($request->filled('fromShowroomId')) {
            $query->where('from_showroom_id', $request->fromShowroomId);
        }
        if ($request->filled('toShowroomId')) {
            $query->where('to_showroom_id', $request->toShowroomId);
        }


        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('date', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('date', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $transfers = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['transfers' => $transfers]);
        }

        return view('admin.reports.productTransferReports', compact('transfers', 'showrooms'));
    }

    public function rawMaterialTransferReports(Request $request)
    {
        $warehouses = Warehouse::all();
        // Start building the query
        $query = RawMaterialStockTransfer::with(['fromWarehouse', 'toWarehouse']);

        // Apply filters based on the request
        if ($request->filled('fromWarehouseId')) {
            $query->where('from_warehouse_id', $request->fromWarehouseId);
        }
        if ($request->filled('toWarehouseId')) {
            $query->where('to_warehouse_id', $request->toWarehouseId);
        }

        // Filter by start and end dates
        if ($request->filled('startDate') && $request->filled('endDate')) {
            $query->whereBetween('date', [$request->startDate, $request->endDate]);
        } elseif ($request->filled('startDate')) {
            $query->whereDate('date', '>=', $request->startDate);
        } elseif ($request->filled('endDate')) {
            $query->whereDate('date', '<=', $request->endDate);
        }

        // Get the filtered stocks
        $transfers = $query->get();

        // Check if request is AJAX
        if ($request->ajax()) {
            return response()->json(['transfers' => $transfers]);
        }

        return view('admin.reports.rawMaterialTransferReports', compact('transfers', 'warehouses'));
    }
}
