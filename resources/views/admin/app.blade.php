<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard | CoderNetix POS</title>
    <style>
        /* Scrollable container for sidebar menu */
        .side-nav-wrapper {
            max-height: 100vh;      /* Full viewport height */
            overflow-y: auto;       /* Enable vertical scrolling */
            padding-right: 4px;     /* Optional: avoids content hiding behind scrollbar */
        }

        /* Simple thin scrollbar for modern browsers */
        .side-nav-wrapper::-webkit-scrollbar {
            width: 6px;             /* Thin width */
        }

        .side-nav-wrapper::-webkit-scrollbar-track {
            background: transparent; /* Make track transparent */
        }

        .side-nav-wrapper::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.3); /* Dark translucent thumb */
            border-radius: 3px;
        }

        /* Firefox scrollbar */
        .side-nav-wrapper {
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.3) transparent;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully responsive admin theme which can be used to build CRM, CMS,ERP etc." name="description" />
    <link rel="shortcut icon" href="{{asset('backend/images/favicon.ico')}}">
    <!-- Select2 css -->
    <link href="{{asset('backend/vendor/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Datatables css -->
    <link href="{{asset('backend/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/vendor/datatables.net-fixedcolumns-bs5/css/fixedColumns.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/vendor/datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/vendor/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('backend/vendor/datatables.net-select-bs5/css/select.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{asset('backend/vendor/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('backend/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css')}}">
    <script src="{{asset('backend/js/config.js')}}"></script>
    <link href="{{asset('backend/css/app.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{asset('backend/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    {{-- Custom Css File here --}}
    <link rel="stylesheet" href="{{ asset('backend/css/sdmg.min.css') }}">
    <script src="{{asset('backend/js/chart.js')}}"></script>
    <script src="{{asset('backend/js/echarts.min.js')}}"></script>

</head>

<body>
<div class="wrapper">
    <div class="navbar-custom">
        <div class="topbar container-fluid">
            <div class="d-flex align-items-center gap-1">
                <!-- Sidebar Menu Toggle Button -->
                <button class="button-toggle-menu">
                    <i class="ri-menu-line"></i>
                </button>
            </div>
            <ul class="topbar-menu d-flex align-items-center gap-3">
                <li class="dropdown d-lg-none">
                    <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <i class="ri-search-line fs-22"></i>
                    </a>
                </li>
                <li class="d-none d-sm-inline-block">
                    <div class="nav-link" id="light-dark-mode">
                        <i class="ri-moon-line fs-22"></i>
                    </div>
                </li>
                <li class="dropdown">
                    @php
                       $admin = auth()->user();
                    @endphp
                    <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <span class="d-lg-block d-none">
                              <h5 class="my-0 fw-normal">{{$admin->name}}
                                  <i class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i>
                              </h5>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                        <div class=" dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div>
                        <a href="#" class="dropdown-item">
                            <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                            <span>My Account</span>
                        </a>
                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                            <span>Logout</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <div class="leftside-menu">
        <a href="{{route('dashboard')}}" class="logo logo-light">
            <span class="logo-lg">
{{--                <img src="{{URL::to('backend/images/etl_logo.png')}}" alt="logo" style="height: 50px;">--}}
                 <h1>POS</h1>
            </span>
            <span class="logo-sm">
{{--                <img src="{{URL::to('backend/images/etl_logo.png')}}" alt="small logo">--}}
                <h1>POS</h1>
            </span>
        </a>


        <div class="side-nav-wrapper">
            <ul class="side-nav">
                <li class="side-nav-title">Main</li>

{{--                 @can('resource-list') --}}
{{--                    <li class="side-nav-item"> --}}
{{--                        <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false" aria-controls="sidebarPages" class="side-nav-link"> --}}
{{--                            <i class="ri-pages-line"></i> --}}
{{--                            <span> Resource </span> --}}
{{--                            <span class="menu-arrow"></span> --}}
{{--                        </a>  --}}
{{--                        <div class="collapse" id="sidebarPages"> --}}
{{--                            <ul class="side-nav-second-level"> --}}
{{--                                <li>--}}
{{--                                    <a href="#">Test</a> --}}
{{--                                </li> --}}
{{--                            </ul> --}}
{{--                        </div> --}}
{{--                    </li> --}}
{{--                 @endcan --}}

{{--                @can('slider-list') --}}
{{--                    <li class="side-nav-item"> --}}
{{--                        <a href="{{route('slider.section')}}" class="side-nav-link"> --}}
{{--                            <i class="ri-slideshow-line"></i> --}}
{{--                            <span> Slider </span> --}}
{{--                        </a> --}}
{{--                    </li> --}}
{{--                @endcan--}}

                {{-- Dashboard --}}
                <li class="side-nav-item">
                    <a href="{{route('dashboard')}}" class="side-nav-link">
                        <i class="ri-dashboard-3-line"></i>
                        <span> Dashboard </span>
                    </a>
                </li>

                {{-- Raw Material --}}
                @can('resource-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#rawMaterial" class="side-nav-link">
                            <i class="ri-box-3-line"></i>
                            <span> Raw Material </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="rawMaterial">
                            <ul class="side-nav-second-level">
                                @can('material-category-list') <li><a href="{{ route('raw.material.category.section') }}">Raw Material Category</a></li>@endcan


                            </ul>
                        </div>
                    </li>
                @endcan

                {{-- Product --}}
                @can('resource-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#product" class="side-nav-link">
                            <i class="ri-box-3-line"></i>
                            <span> Product </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="product">
                            <ul class="side-nav-second-level">
                                @can('product-category-list') <li><a href="{{ route('product.category.section') }}">Product Category</a></li>@endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                {{-- Production --}}

                {{-- Master --}}
                @can('resource-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#master" class="side-nav-link">
                            <i class="ri-box-3-line"></i>
                            <span> Master </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="master">
                            <ul class="side-nav-second-level">
                                @can('warehouse-list') <li><a href="{{ route('warehouse.section') }}">Warehouse</a></li>@endcan
                                @can('unit-list') <li><a href="{{ route('unit.section') }}">Unit</a></li>@endcan
                                @can('size-list') <li><a href="{{ route('size.section') }}">Size</a></li>@endcan
                                @can('brand-list') <li><a href="{{route('brand.section')}}">Brand</a></li>@endcan
                                @can('color-list') <li><a href="{{route('color.section')}}">Color</a></li>@endcan
                                @can('customer-list') <li><a href="{{route('customer.section')}}">Customer</a></li>@endcan
                                @can('supplier-list') <li><a href="{{ route('supplier.section') }}">Supplier</a></li>@endcan
                                @can('employee-list') <li><a href="{{route('employee.section')}}">Employee</a></li>@endcan
                                @can('department-list') <li><a href="{{route('department.section')}}">Department</a></li>@endcan
                                @can('production-house-list') <li><a href="{{ route('production.house.section') }}">Production House</a></li>@endcan
                                @can('showroom-list') <li><a href="{{route('showroom.section')}}">Showroom</a></li>@endcan
                                @can('payment-method-list') <li><a href="{{ route('payment.method.section') }}">Payment Method</a></li>@endcan
                                @can('currency-list') <li><a href="{{route('currency.section')}}">Currency</a></li>@endcan
                                @can('expense-category-list') <li><a href="{{ route('expense.category.section') }}">Expense Category</a></li>@endcan
                                @can('asset-category-list') <li><a href="{{ route('asset.category.section') }}">Asset Category</a></li>@endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                {{-- Finance --}}
                @can('resource-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#finance" class="side-nav-link">
                            <i class="ri-bank-line"></i>
                            <span> Finance </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="finance">
                            <ul class="side-nav-second-level">
                                @can('account-list')<li><a href="{{ route('account.section') }}">Accounts</a></li>@endcan
                                @can('expense-list')<li><a href="{{ route('expense.section') }}">Expenses</a></li>@endcan
                                @can('asset-list')<li><a href="{{ route('asset.section') }}">Assets</a></li>@endcan
                                @can('deposit-list')<li><a href="{{ route('deposit.section') }}">Deposits</a></li>@endcan
                                @can('withdraw-list')<li><a href="{{ route('withdraw.section') }}">Withdraws</a></li>@endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                {{-- Reports --}}
                @can('resource-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#report" class="side-nav-link">
                            <i class="ri-bar-chart-2-line"></i>
                            <span> Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="report">
                            <ul class="side-nav-second-level">
                                @can('material-stock-report-list') <li><a href="{{ route('raw.material.stock.report') }}">Raw Material Stock</a></li>@endcan
                                @can('product-stock-report-list') <li><a href="{{ route('product.stock.report') }}">Product Stock</a></li>@endcan
                                @can('sell-report-list') <li><a href="{{ route('sell.report') }}">Sell</a></li>@endcan
                                @can('asset-report-list') <li><a href="{{ route('asset.report') }}">Asset</a></li>@endcan
                                @can('expense-report-list') <li><a href="{{ route('expense.report') }}">Expense</a></li>@endcan
                                @can('material-purchase-report-list') <li><a href="{{ route('raw.material.purchase.report') }}">Raw Material Purchase</a></li>@endcan
                                @can('balance-sheet-list') <li><a href="{{ route('balance.sheet.report') }}">Account Balance Sheet</a></li>@endcan
                                @can('deposit-balance-list') <li><a href="{{ route('deposit.balance.sheet.report') }}">Deposit Balance</a></li>@endcan
                                @can('withdraw-balance-list') <li><a href="{{ route('withdraw.balance.sheet.report') }}">Withdraw Balance</a></li>@endcan
                                @can('transfer-balance-list') <li><a href="{{ route('transfer.balance.sheet.report') }}">Transfer Balance</a></li>@endcan
                                @can('product-transfer-report-list') <li><a href="{{ route('product.transfer.report') }}">Product Transfer</a></li>@endcan
                                @can('material-transfer-report-list') <li><a href="{{ route('raw.material.transfer.report') }}">Raw Material Transfer</a></li>@endcan
                                @can('sell-profit-loss-list') <li><a href="{{ route('sell.profit.loss.report') }}">Sell Profit / Loss</a></li>@endcan
                                @can('cron-job-log-list') <li><a href="{{ route('cron.job.logs.report') }}">Cron Job Log</a></li>@endcan
                            </ul>
                        </div>
                    </li>
                @endcan
















                @can('account-transfer-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAccountTransfers" aria-expanded="false" aria-controls="sidebarAccountTransfers" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Account Transfer </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarAccountTransfers">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('account-transfers.index') }}">Account Transfers</a></li>
                                <li><a href="{{ route('account-transfers.create') }}">Create Account Transfer</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('customer-payment-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarCustomerPayments" aria-expanded="false" aria-controls="sidebarCustomerPayments" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Customer Payment </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarCustomerPayments">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('customer-payments.index') }}">Customer Payments</a></li>
                                <li><a href="{{ route('customer-payments.create') }}">Create Payment</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('customer=refund-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarCustomerRefunds" aria-expanded="false" aria-controls="sidebarCustomerRefunds" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Customer Refund </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarCustomerRefunds">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('customer-refunds.index') }}">Customer Refunds</a></li>
                                <li><a href="{{ route('customer-refunds.create') }}">Create Refund</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan



                @can('product-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarProducts" aria-expanded="false" aria-controls="sidebarProducts" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Product </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarProducts">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('products.index') }}">Products</a></li>
                                <li><a href="{{ route('products.create') }}">Create Product</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('production-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarProductions" aria-expanded="false" aria-controls="sidebarProductions" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Production </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarProductions">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('productions.index') }}">Productions</a></li>
                                <li><a href="{{ route('productions.index') }}">Create Production</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('production-payment-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarProductionPayments" aria-expanded="false" aria-controls="sidebarProductionPayments" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Production Payment </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarProductionPayments">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('production-payments.index') }}">Payments</a></li>
                                <li><a href="{{ route('production-payments.create') }}">Create Payment</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('product-stock-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarProductStocks" aria-expanded="false" aria-controls="sidebarProductStocks" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Product Stock </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarProductStocks">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('product-stocks.index') }}">Stocks</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('product-stock-transfer.list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarProductStockTransfers" aria-expanded="false" aria-controls="sidebarProductStockTransfers" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Product Stock Transfer </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarProductStockTransfers">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('product-stock-transfers.index') }}">Transfers</a></li>
                                <li><a href="{{ route('product-stock-transfers.create') }}">Create Transfer</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('raw-material-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarMaterials" aria-expanded="false" aria-controls="sidebarMaterials" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Raw Material </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarMaterials">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('materials.index') }}">Raw Materials</a></li>
                                <li><a href="{{ route('materials.create') }}">Create Raw Material</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('raw-material-purchase-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarRawMaterialPurchases" aria-expanded="false" aria-controls="sidebarRawMaterialPurchases" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Raw Material Purchase </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarRawMaterialPurchases">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('raw-material-purchases.index') }}">Purchase</a></li>
                                <li><a href="{{ route('raw-material-purchases.create') }}">Create Purchase</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('raw-material-stock-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarRawMaterialStocks" aria-expanded="false" aria-controls="sidebarRawMaterialStocks" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Raw Material Stock </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarRawMaterialStocks">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('raw-material-stocks.index') }}">Stocks</a></li>
                                <li><a href="{{ route('raw-material-stocks.create') }}">Create Stock</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('raw-material-stock-transfer-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarRawMaterialStockTransfers" aria-expanded="false" aria-controls="sidebarRawMaterialStockTransfers" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Raw Material Stock Transfer </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarRawMaterialStockTransfers">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('raw-material-stock-transfers.index') }}">Transfers</a></li>
                                <li><a href="{{ route('raw-material-stock-transfers.create') }}">Create Transfer</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('sell-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarSells" aria-expanded="false" aria-controls="sidebarSells" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Sell </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarSells">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('sells.index') }}">Sells</a></li>
                                <li><a href="{{ route('sells.create') }}">Create Sell</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('supplier-payment-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarSupplierPayments" aria-expanded="false" aria-controls="sidebarSupplierPayments" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Supplier Payment </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarSupplierPayments">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('supplier-payments.index') }}">Payments</a></li>
                                <li><a href="{{ route('supplier-payments.create') }}">Create Payment</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('supplier-refund-list')
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarSupplierRefunds" aria-expanded="false" aria-controls="sidebarSupplierRefunds" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Supplier Refund </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarSupplierRefunds">
                            <ul class="side-nav-second-level">
                                <li><a href="{{ route('supplier-refunds.index') }}">Refunds</a></li>
                                <li><a href="{{ route('supplier-refunds.create') }}">Create Refund</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan
















                {{-- Settings --}}
                <li class="side-nav-item">
                    <a data-bs-toggle="collapse" href="#settings" class="side-nav-link">
                        <i class="ri-settings-3-line"></i>
                        <span> Settings </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="settings">
                        <ul class="side-nav-second-level">
                            @can('site-setting') <li><a href="{{route('site.setting')}}">Site Setting</a></li>@endcan
                            @can('role-and-permission-list')
                                <li><a href="{{url('users')}}">Create User</a></li>
                                <li><a href="{{url('roles')}}">Role & Permission</a></li>
                            @endcan
                        </ul>
                    </div>
                </li>
            </ul>
        </div>

    </div>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
              @yield('admin_content')
            </div>
        </div>
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 text-center">
                        <script>document.write(new Date().getFullYear())</script> © CoderNetix POS</b>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>


<script src="{{asset('backend/js/vendor.min.js')}}"></script>
<!-- Dropzone File Upload js -->
<script src="{{asset('backend/vendor/dropzone/min/dropzone.min.js')}}"></script>
<script src="{{asset('backend/js/pages/fileupload.init.js')}}"></script>

<!--  Select2 Plugin Js -->
<script src="{{asset('backend/vendor/select2/js/select2.min.js')}}"></script>
<script src="{{asset('backend/vendor/daterangepicker/moment.min.js')}}"></script>
<script src="{{asset('backend/vendor/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('backend/vendor/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('backend/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('backend/vendor/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- Ckeditor Here -->
<script src="{{asset('backend/js/sdmg.ckeditor.js')}}"></script>
<!-- Datatables js -->
<script src="{{asset('backend/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-fixedcolumns-bs5/js/fixedColumns.bootstrap5.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-buttons/js/buttons.flash.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-keytable/js/dataTables.keyTable.min.js')}}"></script>
<script src="{{asset('backend/vendor/datatables.net-select/js/dataTables.select.min.js')}}"></script>

<!-- Datatable Demo Aapp js -->
<script src="{{asset('backend/js/pages/datatable.init.js')}}"></script>
<script src="{{asset('backend/js/pages/dashboard.js')}}"></script>
<script src="{{asset('backend/js/app.min.js')}}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor.create(document.querySelector('#content'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor.create(document.querySelector('#contentAdd'))
            .catch(error => {
                console.error(error);
            });
    });
</script>


</body>
</html>
