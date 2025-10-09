@extends('admin.app')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Reports</h4>
        </div>
    </div>
</div>

{{-- Report Selector --}}
<div class="row mt-3">
    <div class="col-md-4">
        <select id="reportType" class="form-control">
            <option value="">-- Select Report --</option>
            <option value="rawMaterialStock">Raw Material Stock</option>
            <option value="productStock">Product Stock</option>
            <option value="sell">Sell</option>
            <option value="asset">Asset</option>
            <option value="expense">Expense</option>
            <option value="rawMaterialPurchase">Raw Material Purchase</option>
            <option value="productTransfer">Product Transfer</option>
            <option value="rawMaterialTransfer">Raw Material Transfer</option>
            <option value="balanceSheet">Balance Sheet</option>
            <option value="depositBalanceSheet">Deposit Balance Sheet</option>
            <option value="withdrawBalanceSheet">Withdraw Balance Sheet</option>
            <option value="transferBalanceSheet">Transfer Balance Sheet</option>
            <option value="sellProfitLoss">Sell Profit/Loss</option>
            <option value="cronJobLogs">Cron Job Logs</option>
        </select>
    </div>
</div>

<hr>

<div class="report-section mt-3">

    {{-- ================= Raw Material Stock Report ================= --}}
    <div id="rawMaterialStock" class="report-block d-none">
        <h5>Raw Material Stock Report</h5>
        <form method="GET" action="{{ route('raw.material.stock.report') }}">
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="materialId" class="form-control">
                        <option value="">-- Material --</option>
                        @foreach($rawMaterials as $material)
                        <option value="{{ $material->id }}" {{ request('materialId') == $material->id ? 'selected' : '' }}>{{ $material->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="warehouseId" class="form-control">
                        <option value="">-- Warehouse --</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouseId') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="colorId" class="form-control">
                        <option value="">-- Color --</option>
                        @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ request('colorId') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="brandId" class="form-control">
                        <option value="">-- Brand --</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brandId') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sizeId" class="form-control">
                        <option value="">-- Size --</option>
                        @foreach($sizes as $size)
                        <option value="{{ $size->id }}" {{ request('sizeId') == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Material</th>
                        <th>Warehouse</th>
                        <th>Color</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stock->raw_material->name ?? '' }}</td>
                        <td>{{ $stock->warehouse->name ?? '' }}</td>
                        <td>{{ $stock->color->name ?? '' }}</td>
                        <td>{{ $stock->brand->name ?? '' }}</td>
                        <td>{{ $stock->size->name ?? '' }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ $stock->created_at->format('d-m-Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= Product Stock Report ================= --}}
    <div id="productStock" class="report-block d-none mt-5">
        <h5>Product Stock Report</h5>
        <form method="GET" action="{{ route('product.stock.report') }}">
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="productId" class="form-control">
                        <option value="">-- Product --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('productId') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="showroomId" class="form-control">
                        <option value="">-- Showroom --</option>
                        @foreach($showrooms as $showroom)
                        <option value="{{ $showroom->id }}" {{ request('showroomId') == $showroom->id ? 'selected' : '' }}>{{ $showroom->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="colorId" class="form-control">
                        <option value="">-- Color --</option>
                        @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ request('colorId') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="brandId" class="form-control">
                        <option value="">-- Brand --</option>
                        @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brandId') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sizeId" class="form-control">
                        <option value="">-- Size --</option>
                        @foreach($sizes as $size)
                        <option value="{{ $size->id }}" {{ request('sizeId') == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Showroom</th>
                        <th>Color</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $stock->product->name ?? '' }}</td>
                        <td>{{ $stock->showroom->name ?? '' }}</td>
                        <td>{{ $stock->color->name ?? '' }}</td>
                        <td>{{ $stock->brand->name ?? '' }}</td>
                        <td>{{ $stock->size->name ?? '' }}</td>
                        <td>{{ $stock->quantity }}</td>
                        <td>{{ $stock->created_at->format('d-m-Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= Sell Report ================= --}}
    <div id="sell" class="report-block d-none mt-5">
        <h5>Sell Report</h5>
        <form method="GET" action="{{ route('sell.report') }}">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="customerId" class="form-control">
                        <option value="">-- Customer --</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customerId') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="salesmanId" class="form-control">
                        <option value="">-- Salesman --</option>
                        @foreach($salesmen as $salesman)
                        <option value="{{ $salesman->id }}" {{ request('salesmanId') == $salesman->id ? 'selected' : '' }}>{{ $salesman->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="accountId" class="form-control">
                        <option value="">-- Account --</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ request('accountId') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Salesman</th>
                        <th>Account</th>
                        <th>Net Total ({{ $defaultCurrency->name ?? '' }})</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sells as $sell)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sell->customer->name ?? '' }}</td>
                        <td>{{ $sell->salesman->name ?? '' }}</td>
                        <td>{{ $sell->account->name ?? '' }}</td>
                        <td>{{ number_format(getDefaultCurrencyConvertedPrice($sell)['net_total'] ?? $sell->net_total, 2) }}</td>
                        <td>{{ $sell->created_at->format('d-m-Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center">No data found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= Other Reports ================= --}}
    {{-- You repeat similar blocks for asset, expense, rawMaterialPurchase, productTransfer,
         rawMaterialTransfer, balanceSheet, depositBalanceSheet, withdrawBalanceSheet,
         transferBalanceSheet, sellProfitLoss, cronJobLogs --}}
    {{-- The structure is identical: filters form + table displaying related data --}}

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportSelect = document.getElementById('reportType');
        reportSelect.addEventListener('change', function() {
            document.querySelectorAll('.report-block').forEach(function(el) {
                el.classList.add('d-none');
            });
            if(this.value) {
                document.getElementById(this.value).classList.remove('d-none');
            }
        });
    });
</script>
@endsection
