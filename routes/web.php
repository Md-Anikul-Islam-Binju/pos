<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\admin\SellController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\UnitController;
use App\Http\Controllers\admin\AssetController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ColorController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\SliderController;
use App\Http\Controllers\admin\AccountController;
use App\Http\Controllers\admin\DepositController;
use App\Http\Controllers\admin\ExpenseController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\CurrencyController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\EmployeeController;
use App\Http\Controllers\admin\ShowroomController;
use App\Http\Controllers\admin\SupplierController;
use App\Http\Controllers\admin\WithdrawController;
use App\Http\Controllers\admin\WarehouseController;
use App\Http\Controllers\admin\DepartmentController;
use App\Http\Controllers\admin\ProductionController;
use App\Http\Controllers\admin\RawMaterialController;
use App\Http\Controllers\admin\SiteSettingController;
use App\Http\Controllers\admin\ProductStockController;
use App\Http\Controllers\admin\AssetCategoryController;
use App\Http\Controllers\admin\PaymentMethodController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\CustomerRefundController;
use App\Http\Controllers\admin\SupplierRefundController;
use App\Http\Controllers\admin\AccountTransferController;
use App\Http\Controllers\admin\CustomerPaymentController;
use App\Http\Controllers\admin\ExpenseCategoryController;
use App\Http\Controllers\admin\ProductCategoryController;
use App\Http\Controllers\admin\ProductionHouseController;
use App\Http\Controllers\admin\SupplierPaymentController;
use App\Http\Controllers\admin\ProductSellPriceController;
use App\Http\Controllers\admin\RawMaterialStockController;
use App\Http\Controllers\admin\ProductionPaymentController;
use App\Http\Controllers\admin\RawMaterialCategoryController;
use App\Http\Controllers\admin\RawMaterialPurchaseController;
use App\Http\Controllers\admin\ProductStockTransferController;
use App\Http\Controllers\admin\RawMaterialStockTransferController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(callback: function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/unauthorized-action', [AdminDashboardController::class, 'unauthorized'])->name('unauthorized.action');

    // Account
    Route::get('/account-section', [AccountController::class, 'index'])->name('account.section');
    Route::post('/account-store', [AccountController::class, 'store'])->name('account.store');
    Route::put('/account-update/{id}', [AccountController::class, 'update'])->name('account.update');
    Route::get('/account-show/{id}', [AccountController::class, 'show'])->name('account.show');
    Route::get('/account-delete/{id}', [AccountController::class, 'destroy'])->name('account.destroy');

    // Product
    Route::get('/product-section', [ProductController::class, 'index'])->name('product.section');
    Route::post('/product-store', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product-update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product-delete/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::delete('/product/{product}/image/{key}', [ProductController::class, 'deleteImage'])->name('product.delete.image');
    Route::delete('/product/{product}/thumbnail', [ProductController::class, 'deleteThumb'])->name('product.delete.thumb');
    Route::get('/product/all-products', [ProductController::class, 'getAllProducts'])->name('product.get.products');

    // Product Stock
    Route::get('/product-stock', [ProductStockController::class, 'index'])->name('product.stock.section')->middleware('permission:product-stock-list');
    Route::get('/product-stock/{id}', [ProductStockController::class, 'show'])->name('product.stock.show')->middleware('permission:product-stock-view');

    // Asset
    Route::get('/asset-section', [AssetController::class, 'index'])->name('asset.section')->middleware('permission:asset-list');
    Route::post('/asset-store', [AssetController::class, 'store'])->name('asset.store')->middleware('permission:asset-create');
    Route::put('/asset-update/{asset}', [AssetController::class, 'update'])->name('asset.update')->middleware('permission:asset-edit');
    Route::get('/asset-delete/{asset}', [AssetController::class, 'destroy'])->name('asset.destroy')->middleware('permission:asset-delete');
    Route::get('/asset/{asset}/status/{status}',[AssetController::class, 'updateStatus'])->middleware('permission:asset-update-status')->name('asset.update.status');

    // Deposit
    Route::get('/deposit-section', [DepositController::class, 'index'])->name('deposit.section')->middleware('permission:deposit-list');
    Route::post('/deposit-store', [DepositController::class, 'store'])->name('deposit.store')->middleware('permission:deposit-create');
    Route::put('/deposit-update/{deposit}', [DepositController::class, 'update'])->name('deposit.update')->middleware('permission:deposit-edit');
    Route::get('/deposit-delete/{deposit}', [DepositController::class, 'destroy'])->name('deposit.destroy')->middleware('permission:deposit-delete');
    Route::get('/deposit/{deposit}/status/{status}',[DepositController::class, 'updateStatus'])->middleware('permission:deposit-update-status')->name('deposit.update.status');

    // Expense
    Route::get('/expense-section', [ExpenseController::class, 'index'])->name('expense.section')->middleware('permission:expense-list');
    Route::post('/expense-store', [ExpenseController::class, 'store'])->name('expense.store')->middleware('permission:expense-create');
    Route::put('/expense-update/{expense}', [ExpenseController::class, 'update'])->name('expense.update')->middleware('permission:expense-edit');
    Route::get('/expense-delete/{expense}', [ExpenseController::class, 'destroy'])->name('expense.destroy')->middleware('permission:expense-delete');
    Route::get('/expenses/{expense}/status/{status}',[ExpenseController::class, 'updateStatus'])->middleware('permission:expense-update-status')->name('expense.update.status');

    // Withdraw
    Route::get('/withdraw-section', [WithdrawController::class, 'index'])->name('withdraw.section')->middleware('permission:withdraw-list');
    Route::post('/withdraw-store', [WithdrawController::class, 'store'])->name('withdraw.store')->middleware('permission:withdraw-create');
    Route::put('/withdraw-update/{withdraw}', [WithdrawController::class, 'update'])->name('withdraw.update')->middleware('permission:withdraw-edit');
    Route::get('/withdraw-delete/{withdraw}', [WithdrawController::class, 'destroy'])->name('withdraw.destroy')->middleware('permission:withdraw-delete');
    Route::get('/withdraw/{withdraw}/status/{status}',[WithdrawController::class, 'updateStatus'])->middleware('permission:withdraw-update-status')->name('withdraw.update.status');

    // Customer Payment
    Route::get('/customer-payment-section', [CustomerPaymentController::class, 'index'])->name('customer.payment.section')->middleware('permission:customer-payment-list'); Route::post('/customer-payment-store', [CustomerPaymentController::class, 'store'])->name('customer.payment.store')->middleware('permission:customer-payment-create');
    Route::put('/customer-payment-update/{customer_payment}', [CustomerPaymentController::class, 'update'])->name('customer.payment.update')->middleware('permission:customer-payment-edit');
    Route::get('/customer-payment-delete/{customer_payment}', [CustomerPaymentController::class, 'destroy'])->name('customer.payment.destroy')->middleware('permission:customer-payment-delete');
    Route::get('/customer-payment/{customer_payment}/status/{status}',[CustomerPaymentController::class, 'updateStatus'])->middleware('permission:customer-payment-update-status')->name('customer.payment.update.status');

    // Customer Refund
    Route::get('/customer-refund-section', [CustomerRefundController::class, 'index'])->name('customer.refund.section')->middleware('permission:customer-refund-list'); Route::post('/customer-refund-store', [CustomerRefundController::class, 'store'])->name('customer.refund.store')->middleware('permission:customer-refund-create');
    Route::put('/customer-refund-update/{customer_refund}', [CustomerRefundController::class, 'update'])->name('customer.refund.update')->middleware('permission:customer-refund-edit');
    Route::get('/customer-refund-delete/{customer_refund}', [CustomerRefundController::class, 'destroy'])->name('customer.refund.destroy')->middleware('permission:customer-refund-delete');
    Route::get('/customer-refund/{customer_refund}/status/{status}',[CustomerRefundController::class, 'updateStatus'])->middleware('permission:customer-refund-update-status')->name('customer.refund.update.status');

    // Supplier Payment
    Route::get('/supplier-payment-section', [SupplierPaymentController::class, 'index'])->name('supplier.payment.section')->middleware('permission:supplier-payment-list'); Route::post('/supplier-payment-store', [SupplierPaymentController::class, 'store'])->name('supplier.payment.store')->middleware('permission:supplier-payment-create');
    Route::put('/supplier-payment-update/{supplier_payment}', [SupplierPaymentController::class, 'update'])->name('supplier.payment.update')->middleware('permission:supplier-payment-edit');
    Route::get('/supplier-payment-delete/{supplier_payment}', [SupplierPaymentController::class, 'destroy'])->name('supplier.payment.destroy')->middleware('permission:supplier-payment-delete');
    Route::get('/supplier-payment/{supplier_payment}/status/{status}',[SupplierPaymentController::class, 'updateStatus'])->middleware('permission:supplier-payment-update-status')->name('supplier.payment.update.status');

    // Supplier Refund
    Route::get('/supplier-refund-section', [SupplierRefundController::class, 'index'])->name('supplier.refund.section')->middleware('permission:supplier-refund-list'); Route::post('/supplier-refund-store', [SupplierRefundController::class, 'store'])->name('supplier.refund.store')->middleware('permission:supplier-refund-create');
    Route::put('/supplier-refund-update/{supplier_refund}', [SupplierRefundController::class, 'update'])->name('supplier.refund.update')->middleware('permission:supplier-refund-edit');
    Route::get('/supplier-refund-delete/{supplier_refund}', [SupplierRefundController::class, 'destroy'])->name('supplier.refund.destroy')->middleware('permission:supplier-refund-delete');
    Route::get('/supplier-refund/{supplier_refund}/status/{status}',[SupplierRefundController::class, 'updateStatus'])->middleware('permission:supplier-refund-update-status')->name('supplier.refund.update.status');

    // Account Transfer
    Route::get('/account-transfer-section', [AccountTransferController::class, 'index'])->name('account.transfer.section')->middleware('permission:account-transfer-list');
    Route::post('/account-transfer-store', [AccountTransferController::class, 'store'])->name('account.transfer.store')->middleware('permission:account-transfer-create');
    Route::put('/account-transfer-update/{account_transfer}', [AccountTransferController::class, 'update'])->name('account.transfer.update')->middleware('permission:account-transfer-edit');
    Route::get('/account-transfer-delete/{account_transfer}', [AccountTransferController::class, 'destroy'])->name('account.transfer.destroy')->middleware('permission:account-transfer-delete');
    Route::get('/account-transfer/{account_transfer}/status/{status}',[AccountTransferController::class, 'updateStatus'])->middleware('permission:account-transfer-update-status')->name('account.transfer.update.status');

    // Production Payment
    Route::get('/production-payment-section', [ProductionPaymentController::class, 'index'])->name('production.payment.section')->middleware('permission:production-payment-list');
    Route::post('/production-payment-store', [ProductionPaymentController::class, 'store'])->name('production.payment.store')->middleware('permission:production-payment-create');
    Route::put('/production-payment-update/{production_payment}', [ProductionPaymentController::class, 'update'])->name('production.payment.update')->middleware('permission:production-payment-edit');
    Route::get('/production-payment-delete/{production_payment}', [ProductionPaymentController::class, 'destroy'])->name('production.payment.destroy')->middleware('permission:production-payment-delete');
    Route::get('/production-payment/{production_payment}/status/{status}',[ProductionPaymentController::class, 'updateStatus'])->middleware('permission:production-payment-update-status')->name('production.payment.update.status');


    // Slider Section
    Route::get('/slider-section', [SliderController::class, 'index'])->name('slider.section');
    Route::post('/slider-store', [SliderController::class, 'store'])->name('slider.store');
    Route::put('/slider-update/{id}', [SliderController::class, 'update'])->name('slider.update');
    Route::get('/slider-delete/{id}', [SliderController::class, 'destroy'])->name('slider.destroy');

    // Role and User Section
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

    // Site Setting
    Route::get('/site-setting', [SiteSettingController::class, 'index'])->name('site.setting');
    Route::post('/site-settings-store-update/{id?}', [SiteSettingController::class, 'createOrUpdate'])->name('site-settings.createOrUpdate');

    // Brand
    Route::get('/brand-section', [BrandController::class, 'index'])->name('brand.section');
    Route::post('/brand-store', [BrandController::class, 'store'])->name('brand.store');
    Route::put('/brand-update/{id}', [BrandController::class, 'update'])->name('brand.update');
    Route::get('/brand-delete/{id}', [BrandController::class, 'destroy'])->name('brand.destroy');

    // Color
    Route::get('/color-section', [ColorController::class, 'index'])->name('color.section');
    Route::post('/color-store', [ColorController::class, 'store'])->name('color.store');
    Route::put('/color-update/{id}', [ColorController::class, 'update'])->name('color.update');
    Route::get('/color-delete/{id}', [ColorController::class, 'destroy'])->name('color.destroy');

    // Showroom
    Route::get('/showroom-section', [ShowroomController::class, 'index'])->name('showroom.section');
    Route::post('/showroom-store', [ShowroomController::class, 'store'])->name('showroom.store');
    Route::put('/showroom-update/{id}', [ShowroomController::class, 'update'])->name('showroom.update');
    Route::get('/showroom-delete/{id}', [ShowroomController::class, 'destroy'])->name('showroom.destroy');

    // Department
    Route::get('/department-section', [DepartmentController::class, 'index'])->name('department.section');
    Route::post('/department-store', [DepartmentController::class, 'store'])->name('department.store');
    Route::put('/department-update/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::get('/department-delete/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');

    // Currency
    Route::get('/currency-section', [CurrencyController::class, 'index'])->name('currency.section');
    Route::post('/currency-store', [CurrencyController::class, 'store'])->name('currency.store');
    Route::put('/currency-update/{id}', [CurrencyController::class, 'update'])->name('currency.update');
    Route::get('/currency-delete/{id}', [CurrencyController::class, 'destroy'])->name('currency.destroy');

    // Customer
    Route::get('/customer-section', [CustomerController::class, 'index'])->name('customer.section');
    Route::post('/customer-store', [CustomerController::class, 'store'])->name('customer.store');
    Route::put('/customer-update/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::get('/customer-delete/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');

    // Employee
    Route::get('/employee-section', [EmployeeController::class, 'index'])->name('employee.section');
    Route::post('/employee-store', [EmployeeController::class, 'store'])->name('employee.store');
    Route::put('/employee-update/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::get('/employee-delete/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    // Expense Category
    Route::get('/expense-category-section', [ExpenseCategoryController::class, 'index'])->name('expense.category.section');
    Route::post('/expense-category-store', [ExpenseCategoryController::class, 'store'])->name('expense.category.store');
    Route::put('/expense-category-update/{id}', [ExpenseCategoryController::class, 'update'])->name('expense.category.update');
    Route::get('/expense-category-delete/{id}', [ExpenseCategoryController::class, 'destroy'])->name('expense.category.destroy');

    // Asset Category
    Route::get('/asset-category-section', [AssetCategoryController::class, 'index'])->name('asset.category.section');
    Route::post('/asset-category-store', [AssetCategoryController::class, 'store'])->name('asset.category.store');
    Route::put('/asset-category-update/{id}', [AssetCategoryController::class, 'update'])->name('asset.category.update');
    Route::get('/asset-category-delete/{id}', [AssetCategoryController::class, 'destroy'])->name('asset.category.destroy');

    // Unit
    Route::get('/unit-section', [UnitController::class, 'index'])->name('unit.section');
    Route::post('/unit-store', [UnitController::class, 'store'])->name('unit.store');
    Route::put('/unit-update/{id}', [UnitController::class, 'update'])->name('unit.update');
    Route::get('/unit-delete/{id}', [UnitController::class, 'destroy'])->name('unit.destroy');

    // Size
    Route::get('/size-section', [SizeController::class, 'index'])->name('size.section');
    Route::post('/size-store', [SizeController::class, 'store'])->name('size.store');
    Route::put('/size-update/{id}', [SizeController::class, 'update'])->name('size.update');
    Route::get('/size-delete/{id}', [SizeController::class, 'destroy'])->name('size.destroy');

    // Supplier
    Route::get('/supplier-section', [SupplierController::class, 'index'])->name('supplier.section');
    Route::post('/supplier-store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('/supplier-update/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::get('/supplier-delete/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    // Payment Method
    Route::get('/payment-method-section', [PaymentMethodController::class, 'index'])->name('payment.method.section');
    Route::post('/payment-method-store', [PaymentMethodController::class, 'store'])->name('payment.method.store');
    Route::put('/payment-method-update/{id}', [PaymentMethodController::class, 'update'])->name('payment.method.update');
    Route::get('/payment-method-delete/{id}', [PaymentMethodController::class, 'destroy'])->name('payment.method.destroy');

    // Warehouse
    Route::get('/warehouse-section', [WarehouseController::class, 'index'])->name('warehouse.section');
    Route::post('/warehouse-store', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::put('/warehouse-update/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');
    Route::get('/warehouse-delete/{id}', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

    // Raw Material Category
    Route::get('/raw-material-category-section', [RawMaterialCategoryController::class, 'index'])->name('raw.material.category.section');
    Route::post('/raw-material-category-store', [RawMaterialCategoryController::class, 'store'])->name('raw.material.category.store');
    Route::put('/raw-material-category-update/{id}', [RawMaterialCategoryController::class, 'update'])->name('raw.material.category.update');
    Route::get('/raw-material-category-delete/{id}', [RawMaterialCategoryController::class, 'destroy'])->name('raw.material.category.destroy');

    // Product Category
    Route::get('/product-category-section', [ProductCategoryController::class, 'index'])->name('product.category.section');
    Route::post('/product-category-store', [ProductCategoryController::class, 'store'])->name('product.category.store');
    Route::put('/product-category-update/{id}', [ProductCategoryController::class, 'update'])->name('product.category.update');
    Route::get('/product-category-delete/{id}', [ProductCategoryController::class, 'destroy'])->name('product.category.destroy');

    // Production House
    Route::get('/production-house-section', [ProductionHouseController::class, 'index'])->name('production.house.section');
    Route::post('/production-house-store', [ProductionHouseController::class, 'store'])->name('production.house.store');
    Route::put('/production-house-update/{id}', [ProductionHouseController::class, 'update'])->name('production.house.update');
    Route::get('/production-house-delete/{id}', [ProductionHouseController::class, 'destroy'])->name('production.house.destroy');

    // Reports
    Route::get('/raw-material-stock-report', [ReportController::class, 'rawMaterialStockReports'])->name('raw.material.stock.report');
    Route::get('/product-stock-report', [ReportController::class, 'productStockReports'])->name('product.stock.report');
    Route::get('/sell-report', [ReportController::class, 'sellReports'])->name('sell.report');
    Route::get('/asset-report', [ReportController::class, 'assetReports'])->name('asset.report');
    Route::get('/expense-report', [ReportController::class, 'expenseReports'])->name('expense.report');
    Route::get('/raw-material-purchase-report', [ReportController::class, 'rawMaterialPurchaseReports'])->name('raw.material.purchase.report');
    Route::get('/product-transfer-report', [ReportController::class, 'productTransferReports'])->name('product.transfer.report');
    Route::get('/raw-material-transfer-report', [ReportController::class, 'rawMaterialTransferReports'])->name('raw.material.transfer.report');
    Route::get('/account-balance-sheet', [ReportController::class, 'balanceSheetReports'])->name('balance.sheet.report');
    Route::get('/deposit-balance-sheet', [ReportController::class, 'depositBalanceSheet'])->name('deposit.balance.sheet.report');
    Route::get('/withdraw-balance-sheet', [ReportController::class, 'withdrawBalanceSheet'])->name('withdraw.balance.sheet.report');
    Route::get('/transfer-balance-sheet', [ReportController::class, 'transferBalanceSheet'])->name('transfer.balance.sheet.report');
    Route::get('/sell-profit-loss', [ReportController::class, 'sellProfitLoss'])->name('sell.profit.loss.report');
    Route::get('/cron-job-logs', [ReportController::class, 'cronJobLogs'])->name('cron.job.logs.report');












    // Raw Materials
    Route::resource('/materials', RawMaterialController::class)->middleware('permission:materials.list');

    // RawMaterialPurchase
    Route::get('/raw-material-purchases/{raw_material_purchase}/status/{status}', [RawMaterialPurchaseController::class, 'updateStatus'])->name('raw-material-purchases.updateStatus');
    Route::get('/raw-material-purchases/{raw_material_purchase}/print', [RawMaterialPurchaseController::class, 'printRawMaterialPurchase'])->name('raw-material-purchases.print');
    Route::resource('/raw-material-purchases', RawMaterialPurchaseController::class)->middleware('permission:rawMaterialPurchases.list');

    // Raw Material Stock
    Route::resource('/raw-material-stocks', RawMaterialStockController::class)->middleware('permission:rawMaterialStocks.list');

    // Production
    Route::get('/productions/{production}/status/{status}',[ProductionController::class, 'updateStatus'])->name('productions.updateStatus')->middleware('permission:productions.updateStatus');
    Route::get('/productions/{production}/print',[ProductionController::class, 'printProduction'])->name('productions.print');
    Route::resource('/productions', ProductionController::class)->middleware('permission:productions.list');

    // Sell
    Route::get('/sells/{sell}/status/{status}',[SellController::class, 'updateStatus'])->name('sells.updateStatus')->middleware('permission:sells.updateStatus');
    Route::get('/sells/{id}/invoice', [SellController::class, 'showInvoice'])->name('sells.invoiceTemplate');
    Route::resource('/sells', SellController::class)->middleware('permission:sells.list');

    // ShowroomTransfer
    Route::get('/product-stock-transfers/{product_stock_transfer}/status',[ProductStockTransferController::class, 'changeStatus'])->name('product-stock-transfers.changeStatus');
    Route::resource('/product-stock-transfers', ProductStockTransferController::class)->middleware('permission:productStockTransfers.list');

    // WarehouseTransfer
    Route::get('/raw-material-stock-transfers/{raw_material_stock_transfer}/status',[RawMaterialStockTransferController::class, 'changeStatus'])->name('raw-material-stock-transfers.changeStatus');
    Route::resource('/raw-material-stock-transfers', RawMaterialStockTransferController::class)->middleware('permission:rawMaterialStockTransfers.list');

});


require __DIR__.'/auth.php';
