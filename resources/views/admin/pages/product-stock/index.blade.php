@extends('admin.app')
@section('admin_content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="#">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                    <li class="breadcrumb-item active">Product Stocks</li>
                </ol>
            </div>
            <h4 class="page-title">Product Stocks</h4>
        </div>
    </div>
</div>

<div class="col-12">
    @can('product-stock-list')
    <div class="card">
        <div class="card-body table-responsive">
            <table id="productStocksTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Total Avg Cost</th>
                        <th>Showroom</th>
                        <th>Color</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Sell Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stocks as $key => $s)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $s->product->name ?? '' }}</td>
                        <td>{{ $s->quantity ?? '' }}</td>
                        <td>{{ $s->total_cost_price ? number_format($s->total_cost_price, 2) : '' }}</td>
                        <td>{{ $s->showroom->name ?? '' }}</td>
                        <td>{{ $s->color->color_name ?? '' }}</td>
                        <td>{{ $s->brand->name ?? '' }}</td>
                        <td>{{ $s->size->name ?? '' }}</td>
                        <td>{{ $s->product_sell_prices->count() ?? 'Not Set yet' }}</td>
                        <td class="text-center">
                            @can('product-stock-view')
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewProductStockModal{{ $s->id }}">
                                View
                            </button>
                            @endcan
                            <button type="button" class="btn btn-primary btn-sm px-1 py-0"
                                onclick="openSellPriceModal({{ $s->id }}, '{{ $s->product->name }}')">
                                Update Sell Price
                            </button>
                        </td>
                    </tr>

                    {{-- VIEW MODAL --}}
                    <div class="modal fade" id="viewProductStockModal{{ $s->id }}" tabindex="-1" aria-labelledby="viewProductStockModalLabel{{ $s->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewProductStockModalLabel{{ $s->id }}">View Stock - {{ $s->product->name ?? '' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered w-100">
                                        <tr>
                                            <th style="width:30%;">Product</th>
                                            <td>{{ $s->product->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Quantity</th>
                                            <td>{{ $s->quantity ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Price</th>
                                            <td>{{ $s->price ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Warehouse</th>
                                            <td>{{ $s->showroom->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Color</th>
                                            <td>{{ $s->color->color_name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Brand</th>
                                            <td>{{ $s->brand->name ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Size</th>
                                            <td>{{ $s->size->name ?? '' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endcan
</div>

{{-- SELL PRICE MODAL --}}
<div class="modal fade" id="globalSellPriceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Sell Price for <span id="modalProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="globalSellPriceForm" method="POST">@csrf
                    <div class="mb-3">
                        <label for="currency_id" class="form-label">Currency</label>
                        <select class="form-select" id="currency_id" name="currency_id"></select>
                    </div>
                    <div class="mb-3">
                        <label for="sell_price" class="form-label">Sell Price</label>
                        <input type="number" step="0.01" class="form-control" name="sell_price" id="sell_price" required>
                    </div>
                    <input type="hidden" id="modalProductStockId" name="product_stock_id">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="updateSellPrice()">Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function openSellPriceModal(stockId, productName) {
        document.getElementById('modalProductName').innerText = productName;
        document.getElementById('modalProductStockId').value = stockId;
        document.getElementById('sell_price').value = '';
        document.getElementById('currency_id').innerHTML = '';

        fetch(`/product-stocks/${stockId}/get-sell-price-data`)
            .then(res => res.json())
            .then(response => {
                if (Array.isArray(response.currencies)) {
                    response.currencies.forEach(c => {
                        let opt = document.createElement('option');
                        opt.value = c.id;
                        opt.text = `${c.name} (${c.code})`;
                        document.getElementById('currency_id').appendChild(opt);
                    });
                }
                new bootstrap.Modal(document.getElementById('globalSellPriceModal')).show();
            })
            .catch(() => alert('Error fetching data'));
    }

    function updateSellPrice() {
        let stockId = document.getElementById('modalProductStockId').value;
        let formData = {
            currency_id: document.getElementById('currency_id').value,
            sell_price: document.getElementById('sell_price').value
        };
        fetch(`/product-stocks/${stockId}/update-sell-price`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        }).then(res => res.json()).then(res => {
            if (res.success) location.reload();
            else alert(res.message);
        }).catch(() => alert('Error'));
    }
</script>
@endsection
