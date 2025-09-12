<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\admin\OttController;
use App\Http\Controllers\admin\MenuController;
use App\Http\Controllers\admin\NewsController;
use App\Http\Controllers\admin\SellController;
use App\Http\Controllers\admin\SizeController;
use App\Http\Controllers\admin\TeamController;
use App\Http\Controllers\admin\UnitController;
use App\Http\Controllers\admin\AboutController;
use App\Http\Controllers\admin\AssetController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ColorController;
use App\Http\Controllers\admin\VenueController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\SliderController;
use App\Http\Controllers\admin\AccountController;
use App\Http\Controllers\admin\CounterController;
use App\Http\Controllers\admin\DepositController;
use App\Http\Controllers\admin\ExpenseController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\CurrencyController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\EmployeeController;
use App\Http\Controllers\admin\LabSetupController;
use App\Http\Controllers\admin\ShowcaseController;
use App\Http\Controllers\admin\ShowroomController;
use App\Http\Controllers\admin\SupplierController;
use App\Http\Controllers\admin\TrainingController;
use App\Http\Controllers\admin\WithdrawController;
use App\Http\Controllers\admin\WarehouseController;
use App\Http\Controllers\frontend\NoticeController;
use App\Http\Controllers\admin\DepartmentController;
use App\Http\Controllers\admin\ProductionController;
use App\Http\Controllers\admin\NoticeBoardController;
use App\Http\Controllers\admin\ProjectFileController;
use App\Http\Controllers\admin\RawMaterialController;
use App\Http\Controllers\admin\SiteSettingController;
use App\Http\Controllers\admin\UserMessageController;
use App\Http\Controllers\frontend\HomePageController;
use App\Http\Controllers\frontend\NewsPageController;
use App\Http\Controllers\admin\ProductStockController;
use App\Http\Controllers\frontend\AboutPageController;
use App\Http\Controllers\frontend\ContactUsController;
use App\Http\Controllers\admin\AssetCategoryController;
use App\Http\Controllers\admin\PaymentMethodController;
use App\Http\Controllers\frontend\TeamMemberController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\CustomerRefundController;
use App\Http\Controllers\admin\SupplierRefundController;
use App\Http\Controllers\frontend\ProjectPageController;
use App\Http\Controllers\admin\AccountTransferController;
use App\Http\Controllers\admin\CustomerPaymentController;
use App\Http\Controllers\admin\ExpenseCategoryController;
use App\Http\Controllers\admin\ObjectOfProjectController;
use App\Http\Controllers\admin\ProductCategoryController;
use App\Http\Controllers\admin\ProductionHouseController;
use App\Http\Controllers\admin\ProjectCategoryController;
use App\Http\Controllers\admin\SupplierPaymentController;
use App\Http\Controllers\frontend\TrainingPageController;
use App\Http\Controllers\admin\ProductSellPriceController;
use App\Http\Controllers\admin\RawMaterialStockController;
use App\Http\Controllers\admin\TrainingCategoryController;
use App\Http\Controllers\admin\ProductionPaymentController;
use App\Http\Controllers\admin\ProjectFileCategoryController;
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

























    // Account
    Route::get('/account-section', [AccountController::class, 'index'])->name('account.section');
    Route::post('/account-store', [AccountController::class, 'store'])->name('account.store');
    Route::put('/account-update/{id}', [AccountController::class, 'update'])->name('account.update');
    Route::get('/account-delete/{id}', [AccountController::class, 'destroy'])->name('account.destroy');

    // Expense (5)
    Route::get('/expense-section', [ExpenseController::class, 'index'])->name('expense.section');
    Route::post('/expense-store', [ExpenseController::class, 'store'])->name('expense.store');
    Route::put('/expense-update/{id}', [ExpenseController::class, 'update'])->name('expense.update');
    Route::get('/expense-delete/{id}', [ExpenseController::class, 'destroy'])->name('expense.destroy');
    Route::get('/expense-update-status/{id}/{status}', [ExpenseController::class, 'updateStatus'])->name('expense.update.status');

    // Asset Category
    Route::get('/asset-category-section', [AssetCategoryController::class, 'index'])->name('asset.category.section');
    Route::post('/asset-category-store', [AssetCategoryController::class, 'store'])->name('asset.category.store');
    Route::put('/asset-category-update/{id}', [AssetCategoryController::class, 'update'])->name('asset.category.update');
    Route::get('/asset-category-delete/{id}', [AssetCategoryController::class, 'destroy'])->name('asset.category.destroy');

    // Asset (5)
    Route::get('/asset-section', [AssetController::class, 'index'])->name('asset.section');
    Route::post('/asset-store', [AssetController::class, 'store'])->name('asset.store');
    Route::put('/asset-update/{id}', [AssetController::class, 'update'])->name('asset.update');
    Route::get('/asset-delete/{id}', [AssetController::class, 'destroy'])->name('asset.destroy');
    Route::get('/asset-update-status/{id}/{status}', [AssetController::class, 'updateStatus'])->name('asset.update.status');

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

    // Raw Material (5)
    Route::get('/raw-material-section', [RawMaterialController::class, 'index'])->name('raw.material.section');
    Route::post('/raw-material-store', [RawMaterialController::class, 'store'])->name('raw.material.store');
    Route::put('/raw-material-update/{id}', [RawMaterialController::class, 'update'])->name('raw.material.update');
    Route::get('/raw-material-delete/{id}', [RawMaterialController::class, 'destroy'])->name('raw.material.destroy');
    Route::get('/all-raw-materials', [RawMaterialController::class, 'getAllMaterials'])->name('raw.material.all');

    // Raw Material Purchase (6)
    Route::get('/raw-material-purchase-section', [RawMaterialPurchaseController::class, 'index'])->name('raw.material.purchase.section');
    Route::post('/raw-material-purchase-store', [RawMaterialPurchaseController::class, 'store'])->name('raw.material.purchase.store');
    Route::put('/raw-material-purchase-update/{id}', [RawMaterialPurchaseController::class, 'update'])->name('raw.material.purchase.update');
    Route::get('/raw-material-purchase-delete/{id}', [RawMaterialPurchaseController::class, 'destroy'])->name('raw.material.purchase.destroy');
    Route::get('/raw-material-purchase-update-status/{id}/{status}', [RawMaterialPurchaseController::class, 'updateStatus'])->name('raw.material.purchase.update.status');
    Route::get('/raw-material-purchase-print/{id}', [RawMaterialPurchaseController::class, 'printRawMaterialPurchase'])->name('raw.material.purchase.print');

    // Product Category
    Route::get('/product-category-section', [ProductCategoryController::class, 'index'])->name('product.category.section');
    Route::post('/product-category-store', [ProductCategoryController::class, 'store'])->name('product.category.store');
    Route::put('/product-category-update/{id}', [ProductCategoryController::class, 'update'])->name('product.category.update');
    Route::get('/product-category-delete/{id}', [ProductCategoryController::class, 'destroy'])->name('product.category.destroy');

    // Product (5)
    Route::get('/product-section', [ProductController::class, 'index'])->name('product.section');
    Route::post('/product-store', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product-update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/product-delete/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::get('/all-products', [ProductController::class, 'getAllProducts'])->name('product.all');

    // Raw Material Stock
    Route::get('/raw-material-stock-section', [RawMaterialStockController::class, 'index'])->name('raw.material.stock.section');

    // Deposit (5)
    Route::get('/deposit-section', [DepositController::class, 'index'])->name('deposit.section');
    Route::post('/deposit-store', [DepositController::class, 'store'])->name('deposit.store');
    Route::put('/deposit-update/{id}', [DepositController::class, 'update'])->name('deposit.update');
    Route::get('/deposit-delete/{id}', [DepositController::class, 'destroy'])->name('deposit.destroy');
    Route::get('/deposit-update-status/{id}/{status}', [DepositController::class, 'updateStatus'])->name('deposit.update.status');

    // Withdraw (5)
    Route::get('/withdraw-section', [WithdrawController::class, 'index'])->name('withdraw.section');
    Route::post('/withdraw-store', [WithdrawController::class, 'store'])->name('withdraw.store');
    Route::put('/withdraw-update/{id}', [WithdrawController::class, 'update'])->name('withdraw.update');
    Route::get('/withdraw-delete/{id}', [WithdrawController::class, 'destroy'])->name('withdraw.destroy');
    Route::get('/withdraw-update-status/{id}/{status}', [WithdrawController::class, 'updateStatus'])->name('withdraw.update.status');

    // Account Transfer (5)
    Route::get('/account-transfer-section', [AccountTransferController::class, 'index'])->name('account.transfer.section');
    Route::post('/account-transfer-store', [AccountTransferController::class, 'store'])->name('account.transfer.store');
    Route::put('/account-transfer-update/{id}', [AccountTransferController::class, 'update'])->name('account.transfer.update');
    Route::get('/account-transfer-delete/{id}', [AccountTransferController::class, 'destroy'])->name('account.transfer.destroy');
    Route::get('/account-transfer-update-status/{id}/{status}', [AccountTransferController::class, 'updateStatus'])->name('account.transfer.update.status');

    // Production House
    Route::get('/production-house-section', [ProductionHouseController::class, 'index'])->name('production.house.section');
    Route::post('/production-house-store', [ProductionHouseController::class, 'store'])->name('production.house.store');
    Route::put('/production-house-update/{id}', [ProductionHouseController::class, 'update'])->name('production.house.update');
    Route::get('/production-house-delete/{id}', [ProductionHouseController::class, 'destroy'])->name('production.house.destroy');

    // Production (8)
    Route::get('/production-section', [ProductionController::class, 'index'])->name('production.section');
    Route::post('/production-store', [ProductionController::class, 'store'])->name('production.store');
    Route::put('/production-update/{id}', [ProductionController::class, 'update'])->name('production.update');
    Route::get('/production-delete/{id}', [ProductionController::class, 'destroy'])->name('production.destroy');
    Route::get('/production-update-status/{id}/{status}', [ProductionController::class, 'updateStatus'])->name('production.update.status');
    Route::get('/production-print/{id}', [ProductionController::class, 'printProduction'])->name('production.print');
    Route::get('/get-raw-materials/by-warehouse', [ProductionController::class, 'getRawMaterialsByWarehouse'])->name('raw.materials.by.warehouse');
    Route::get('/admin/production/{production}/edit', [ProductionController::class, 'editAjax'])->name('production.edit.ajax');

    // Sell (8)
    Route::get('/sell-section', [SellController::class, 'index'])->name('sell.section');
    Route::post('/sell-store', [SellController::class, 'store'])->name('sell.store');
    Route::put('/sell-update/{id}', [SellController::class, 'update'])->name('sell.update');
    Route::get('/sell-delete/{id}', [SellController::class, 'destroy'])->name('sell.destroy');
    Route::get('/sell-update-status/{id}/{status}', [SellController::class, 'updateStatus'])->name('sell.update.status');
    Route::get('/sell-invoice/{id}', [SellController::class, 'showInvoice'])->name('sell.invoice');
    Route::get('/get-products/by-category', [SellController::class, 'getProductsByCategory'])->name('products.by.category');
    Route::get('/get-all-products', [SellController::class, 'getAllProducts'])->name('get.all.products');

    // Product Stock
    Route::get('/product-stock-section', [ProductStockController::class, 'index'])->name('product.stock.section');

    // Product Stock Transfer (6)
    Route::get('/product-stock-transfer-section', [ProductStockTransferController::class, 'index'])->name('product.stock.transfer.section');
    Route::post('/product-stock-transfer-store', [ProductStockTransferController::class, 'store'])->name('product.stock.transfer.store');
    Route::put('/product-stock-transfer-update/{id}', [ProductStockTransferController::class, 'update'])->name('product.stock.transfer.update');
    Route::get('/product-stock-transfer-delete/{id}', [ProductStockTransferController::class, 'destroy'])->name('product.stock.transfer.destroy');
    Route::get('/product-stocks/{id}', [ProductStockTransferController::class, 'getProductStocksByShowroom'])->name('product.stocks.by.showroom');
    Route::get('/product-stock-transfer-change-status/{id}/{status}', [ProductStockTransferController::class, 'changeStatus'])->name('product.stock.transfer.change.status');

    // Raw Material Stock Transfer (6)
    Route::get('/raw-material-stock-transfer-section', [RawMaterialStockTransferController::class, 'index'])->name('raw.material.stock.transfer.section');
    Route::post('/raw-material-stock-transfer-store', [RawMaterialStockTransferController::class, 'store'])->name('raw.material.stock.transfer.store');
    Route::put('/raw-material-stock-transfer-update/{id}', [RawMaterialStockTransferController::class, 'update'])->name('raw.material.stock.transfer.update');
    Route::get('/raw-material-stock-transfer-delete/{id}', [RawMaterialStockTransferController::class, 'destroy'])->name('raw.material.stock.transfer.destroy');
    Route::get('/raw-material-stocks/{id}', [RawMaterialStockTransferController::class, 'getRawMaterialStocksByWarehouse'])->name('raw.material.stocks.by.warehouse');
    Route::get('/raw-material-stock-transfer-change-status/{id}/{status}', [RawMaterialStockTransferController::class, 'changeStatus'])->name('raw.material.stock.transfer.change.status');

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

    // CustomerPayments (5)
    Route::get('/customer-payment-section', [CustomerPaymentController::class, 'index'])->name('customer.payment.section');
    Route::post('/customer-payment-store', [CustomerPaymentController::class, 'store'])->name('customer.payment.store');
    Route::put('/customer-payment-update/{id}', [CustomerPaymentController::class, 'update'])->name('customer.payment.update');
    Route::get('/customer-payment-delete/{id}', [CustomerPaymentController::class, 'destroy'])->name('customer.payment.destroy');
    Route::get('/customer-payment-update-status/{id}/{status}', [CustomerPaymentController::class, 'updateStatus'])->name('customer.payment.update.status');

    // SupplierPayments (5)
    Route::get('/supplier-payment-section', [SupplierPaymentController::class, 'index'])->name('supplier.payment.section');
    Route::post('/supplier-payment-store', [SupplierPaymentController::class, 'store'])->name('supplier.payment.store');
    Route::put('/supplier-payment-update/{id}', [SupplierPaymentController::class, 'update'])->name('supplier.payment.update');
    Route::get('/supplier-payment-delete/{id}', [SupplierPaymentController::class, 'destroy'])->name('supplier.payment.destroy');
    Route::get('/supplier-payment-update-status/{id}/{status}', [SupplierPaymentController::class, 'updateStatus'])->name('supplier.payment.update.status');

    // ProductionPayments (5)
    Route::get('/production-payment-section', [ProductionPaymentController::class, 'index'])->name('production.payment.section');
    Route::post('/production-payment-store', [ProductionPaymentController::class, 'store'])->name('production.payment.store');
    Route::put('/production-payment-update/{id}', [ProductionPaymentController::class, 'update'])->name('production.payment.update');
    Route::get('/production-payment-delete/{id}', [ProductionPaymentController::class, 'destroy'])->name('production.payment.destroy');
    Route::get('/production-payment-update-status/{id}/{status}', [ProductionPaymentController::class, 'updateStatus'])->name('production.payment.update.status');

    // CustomerRefunds (5)
    Route::get('/customer-refund-section', [CustomerRefundController::class, 'index'])->name('customer.refund.section');
    Route::post('/customer-refund-store', [CustomerRefundController::class, 'store'])->name('customer.refund.store');
    Route::put('/customer-refund-update/{id}', [CustomerRefundController::class, 'update'])->name('customer.refund.update');
    Route::get('/customer-refund-delete/{id}', [CustomerRefundController::class, 'destroy'])->name('customer.refund.destroy');
    Route::get('/customer-refund-update-status/{id}/{status}', [CustomerRefundController::class, 'updateStatus'])->name('customer.refund.update.status');

    // SupplierRefunds (5)
    Route::get('/supplier-refund-section', [SupplierRefundController::class, 'index'])->name('supplier.refund.section');
    Route::post('/supplier-refund-store', [SupplierRefundController::class, 'store'])->name('supplier.refund.store');
    Route::put('/supplier-refund-update/{id}', [SupplierRefundController::class, 'update'])->name('supplier.refund.update');
    Route::get('/supplier-refund-delete/{id}', [SupplierRefundController::class, 'destroy'])->name('supplier.refund.destroy');
    Route::get('/supplier-refund-update-status/{id}/{status}', [SupplierRefundController::class, 'updateStatus'])->name('supplier.refund.update.status');

    // Product Sell Price
    Route::get('/product-stocks/{stock}/get-sell-price-data', [ProductSellPriceController::class, 'getSellPriceData'])->name('product.stock.get.sell.price.data');
    Route::post('/product-stocks/{stock}/update-sell-price', [ProductSellPriceController::class, 'updateSellPrice'])->name('product.stock.update.sell.price');
});

require __DIR__.'/auth.php';
