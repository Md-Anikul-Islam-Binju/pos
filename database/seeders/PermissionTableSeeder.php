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

            // For Account
            'account-list',
            'account-create',
            'account-edit',
            'account-delete',

            // For Expense
            'expense-list',
            'expense-create',
            'expense-edit',
            'expense-delete',
            'expense-updateStatus',

            // For Asset
            'asset-list',
            'asset-create',
            'asset-edit',
            'asset-delete',
            'asset-updateStatus',

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

            // For Raw Material
            'material-list',
            'material-create',
            'material-edit',
            'material-delete',

            // For Raw Material Purchase
            'material-purchase-list',
            'material-purchase-create',
            'material-purchase-edit',
            'material-purchase-delete',
            'material-purchase-updateStatus',

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

            // For Product
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',

            // For Raw Material Stock
            'material-stock-list',

            // For Deposit
            'deposit-list',
            'deposit-create',
            'deposit-edit',
            'deposit-delete',
            'deposit-updateStatus',

            // For Withdraw
            'withdraw-list',
            'withdraw-create',
            'withdraw-edit',
            'withdraw-delete',
            'withdraw-updateStatus',

            // For Account Transfer
            'account-transfer-list',
            'account-transfer-create',
            'account-transfer-edit',
            'account-transfer-delete',
            'account-transfer-updateStatus',

            // For Production House
            'production-house-list',
            'production-house-create',
            'production-house-edit',
            'production-house-delete',

            // For Production
            'production-list',
            'production-create',
            'production-edit',
            'production-delete',
            'production-updateStatus',

            // For Sell
            'sell-list',
            'sell-create',
            'sell-edit',
            'sell-delete',
            'sell-updateStatus',

            // For Product Stock
            'product-stock-list',

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
            'transfer-balance-list',
            'sell-profit-loss-list',
            'product-transfer-report-list',
            'material-transfer-report-list',

            // For Product Stock Transfer
            'product-stock-transfer-list',
            'product-stock-transfer-create',
            'product-stock-transfer-edit',
            'product-stock-transfer-delete',

            // For Raw Material Stock Transfer
            'raw-material-stock-transfer-list',
            'raw-material-stock-transfer-create',
            'raw-material-stock-transfer-edit',
            'raw-material-stock-transfer-delete',

            // For Customer Payment
            'customer-payment-list',
            'customer-payment-create',
            'customer-payment-edit',
            'customer-payment-delete',
            'customer-payment-updateStatus',

            // For Supplier Payment
            'supplier-payment-list',
            'supplier-payment-create',
            'supplier-payment-edit',
            'supplier-payment-delete',
            'supplier-payment-updateStatus',

            // For Customer Refund
            'customer-refund-list',
            'customer-refund-create',
            'customer-refund-edit',
            'customer-refund-delete',
            'customer-refund-updateStatus',

            // For Supplier Refund
            'supplier-refund-list',
            'supplier-refund-create',
            'supplier-refund-edit',
            'supplier-refund-delete',
            'supplier-refund-updateStatus',

            // For Production Payment
            'production-payment-list',
            'production-payment-create',
            'production-payment-edit',
            'production-payment-delete',
            'production-payment-updateStatus',

            // For Report
            'sell-price-list',
            'sell-price-create',
            'sell-price-edit',
            'sell-price-delete',

            // Site Setting
            'site-setting',

            // Dashboard
            'login-log-list',

        ];
        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }
    }
}
