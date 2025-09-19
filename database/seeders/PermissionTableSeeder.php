<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            // For roll and permission
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            // For Role and permission
            'role-and-permission-list',

            // For Resource
            'resource-list',

            // For User
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',

            // For Slider
            'slider-list',
            'slider-create',
            'slider-edit',
            'slider-delete',

            // For Brand
            'brand-list',
            'brand-create',
            'brand-edit',
            'brand-delete',

            // For Color
            'color-list',
            'color-create',
            'color-edit',
            'color-delete',

            // For Showroom
            'showroom-list',
            'showroom-create',
            'showroom-edit',
            'showroom-delete',

            // For Department
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',

            // For Currency
            'currency-list',
            'currency-create',
            'currency-edit',
            'currency-delete',

            // For Customer
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',

            // For Employee
            'employee-list',
            'employee-create',
            'employee-edit',
            'employee-delete',

            // For Expense Category
            'expense-category-list',
            'expense-category-create',
            'expense-category-edit',
            'expense-category-delete',

            // For Asset Category
            'asset-category-list',
            'asset-category-create',
            'asset-category-edit',
            'asset-category-delete',

            // For Unit
            'unit-list',
            'unit-create',
            'unit-edit',
            'unit-delete',

            // For Size
            'size-list',
            'size-create',
            'size-edit',
            'size-delete',

            // For Supplier
            'supplier-list',
            'supplier-create',
            'supplier-edit',
            'supplier-delete',

            // For Customer
            'customer-list',
            'customer-create',
            'customer-edit',
            'customer-delete',

            // For Payment Method
            'payment-method-list',
            'payment-method-create',
            'payment-method-edit',
            'payment-method-delete',

            // For Warehouse
            'warehouse-list',
            'warehouse-create',
            'warehouse-edit',
            'warehouse-delete',

            // For Material Category
            'material-category-list',
            'material-category-create',
            'material-category-edit',
            'material-category-delete',

            // For Showroom
            'showroom-list',
            'showroom-create',
            'showroom-edit',
            'showroom-delete',

            // For Employee
            'employee-list',
            'employee-create',
            'employee-edit',
            'employee-delete',

            // For Department
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',

            // For Product Category
            'product-category-list',
            'product-category-create',
            'product-category-edit',
            'product-category-delete',

            // For Production House
            'production-house-list',
            'production-house-create',
            'production-house-edit',
            'production-house-delete',

            // For Currency
            'currency-list',
            'currency-create',
            'currency-edit',
            'currency-delete',

            // For Report
            'material-stock-report-list',
            'product-stock-report-list',
            'sell-report-list',
            'asset-report-list',
            'expense-report-list',
            'material-purchase-report-list',
            'balance-sheet-list',
            'deposit-balance-list',
            'withdraw-balance-list',
            'transfer-balance-list',
            'sell-profit-loss-list',
            'product-transfer-report-list',
            'material-transfer-report-list',
            'cron-job-log-list',

            // For Sell Price
            'sell-price-list',
            'sell-price-create',
            'sell-price-edit',
            'sell-price-delete',

            // Site Setting
            'site-setting',

            // Dashboard
            'login-log-list',












            // For Account
            'account-list',
            'account-create',
            'account-view',
            'account-edit',
            'account-delete',
            'account-activity',

            // For Account Transfer
            'account-transfer-list',
            'account-transfer-create',
            'account-transfer-view',
            'account-transfer-edit',
            'account-transfer-delete',
            'account-transfer-activity',
            'account-transfer-update-status',

            // For Asset
            'asset-list',
            'asset-create',
            'asset-view',
            'asset-edit',
            'asset-delete',
            'asset-activity',
            'asset-update-status',

            // For Customer Payment
            'customer-payment-list',
            'customer-payment-create',
            'customer-payment-view',
            'customer-payment-edit',
            'customer-payment-delete',
            'customer-payment-activity',
            'customer-payment-update-status',

            // For Customer Refund
            'customer-refund-list',
            'customer-refund-create',
            'customer-refund-view',
            'customer-refund-edit',
            'customer-refund-delete',
            'customer-refund-activity',
            'customer-refund-update-status',

            // For Deposit
            'deposit-list',
            'deposit-create',
            'deposit-view',
            'deposit-edit',
            'deposit-delete',
            'deposit-activity',
            'deposit-update-status',

            // For Expense
            'expense-list',
            'expense-create',
            'expense-view',
            'expense-edit',
            'expense-delete',
            'expense-activity',
            'expense-update-status',

            // For Product
            'product-list',
            'product-create',
            'product-view',
            'product-edit',
            'product-delete',
            'product-activity',

            // For Production
            'production-list',
            'production-create',
            'production-view',
            'production-edit',
            'production-delete',
            'production-activity',
            'production-update-status',

            // For Production Payment
            'production-payment-list',
            'production-payment-create',
            'production-payment-view',
            'production-payment-edit',
            'production-payment-delete',
            'production-payment-activity',
            'production-payment-update-status',

            // For Product Stock
            'product-stock-list',
            'product-stock-view',
            'product-stock-activity',

            // For Product Stock Transfer
            'product-stock-transfer-list',
            'product-stock-transfer-create',
            'product-stock-transfer-view',
            'product-stock-transfer-edit',
            'product-stock-transfer-delete',
            'product-stock-transfer-activity',

            // For Raw Material
            'raw-material-list',
            'raw-material-create',
            'raw-material-view',
            'raw-material-edit',
            'raw-material-delete',
            'raw-material-activity',

            // For Raw Material Purchase
            'rawMaterial-purchase-list',
            'rawMaterial-purchase-create',
            'rawMaterial-purchase-view',
            'rawMaterial-purchase-edit',
            'rawMaterial-purchase-delete',
            'rawMaterial-purchase-activity',
            'rawMaterial-purchase-update-status',

            // For Raw Material Stock
            'raw-material-stock-list',
            'raw-material-stock-view',
            'raw-material-stock-activity',

            // For Raw Material Stock Transfer
            'raw-material-stock-transfer-list',
            'raw-material-stock-transfer-create',
            'raw-material-stock-transfer-view',
            'raw-material-stock-transfer-edit',
            'raw-material-stock-transfer-delete',
            'raw-material-stock-transfer-activity',

            // For Sell
            'sell-list',
            'sell-create',
            'sell-view',
            'sell-edit',
            'sell-delete',
            'sell-activity',
            'sell-update-status',

            // For Supplier Payment
            'supplier-payment-list',
            'supplier-payment-create',
            'supplier-payment-view',
            'supplier-payment-edit',
            'supplier-payment-delete',
            'supplier-payment-activity',
            'supplier-payment-update-status',

            // For Supplier Refund
            'supplier-refund-list',
            'supplier-refund-create',
            'supplier-refund-view',
            'supplier-refund-edit',
            'supplier-refund-delete',
            'supplier-refund-activity',
            'supplier-refund-update-status',

            // For Withdraw
            'withdraw-list',
            'withdraw-create',
            'withdraw-view',
            'withdraw-edit',
            'withdraw-delete',
            'withdraw-activity',
            'withdraw-update-status'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
