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
