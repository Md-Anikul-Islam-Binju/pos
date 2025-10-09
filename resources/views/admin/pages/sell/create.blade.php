@extends('admin.app')
@section('admin_content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">Create Sell</h4>
            <div class="page-title-right">
                <a href="{{ route('sells.index') }}" class="btn btn-primary">Back to List</a>
            </div>
        </div>
    </div>
</div>

{{-- Currency Selection --}}
<div class="mb-3">
    <label for="currency">Select Currency</label>
    <form action="{{ route('sells.set.currency') }}" method="POST" class="d-flex">
        @csrf
        <select name="currency_id" id="currency" class="form-control me-2" required>
            @foreach($currencies as $currency)
                <option value="{{ $currency->id }}" {{ session('currency.id') == $currency->id ? 'selected' : '' }}>
                    {{ $currency->name }} ({{ $currency->symbol }})
                </option>
            @endforeach
        </select>
        <button class="btn btn-secondary" type="submit">Set</button>
    </form>
</div>

<form action="{{ route('sells.store') }}" method="POST" id="sellForm">
    @csrf
    {{-- Customer, Salesman, Account --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="customer">Customer</label>
            <select name="customer" id="customer" class="form-control" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="salesman">Salesman</label>
            <select name="salesman" id="salesman" class="form-control" required>
                <option value="">Select Salesman</option>
                @foreach($salesman as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="account">Account</label>
            <select name="account_id" id="account" class="form-control" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Product Category Filter --}}
    <div class="mb-3">
        <label for="product_category">Filter by Category</label>
        <select id="product_category" class="form-control">
            <option value="">All Categories</option>
            @foreach($productCategories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Products Table --}}
    <table class="table table-bordered" id="productsTable">
        <thead>
            <tr>
                <th>Product</th>
                <th>Available Qty</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Discount Type</th>
                <th>Discount Amount</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <button type="button" class="btn btn-success mb-3" id="addProductBtn">Add Product</button>

    {{-- Totals --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Total Amount</label>
            <input type="text" id="total_amount" class="form-control" readonly>
        </div>
        <div class="col-md-4">
            <label>Total Discount</label>
            <input type="text" id="total_discount" class="form-control" readonly>
        </div>
        <div class="col-md-4">
            <label>Net Total</label>
            <input type="text" id="net_total" class="form-control" readonly>
        </div>
    </div>

    {{-- Paid Amount Options --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Paid Amount Option</label>
            <select name="paidAmountOption" id="paidAmountOption" class="form-control">
                <option value="paid_in_full">Paid in Full</option>
                <option value="custom_amount">Custom Amount</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Paid Amount</label>
            <input type="number" name="amount" id="paid_amount_input" class="form-control" disabled>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Create Sell</button>
</form>

{{-- JS Section --}}
@section('scripts')
<script>
let products = @json($stockProducts);

function updateTotals() {
    let totalAmount = 0, totalDiscount = 0, netTotal = 0;

    $('#productsTable tbody tr').each(function() {
        let price = parseFloat($(this).find('.product_price').val()) || 0;
        let qty = parseFloat($(this).find('.product_quantity').val()) || 0;
        let discountType = $(this).find('.discount_type').val();
        let discountAmount = parseFloat($(this).find('.discount_amount').val()) || 0;

        let initialTotal = price * qty;
        let discount = discountType === 'percentage' ? initialTotal * (discountAmount / 100) : discountAmount;
        let total = initialTotal - discount;

        $(this).find('.product_total').val(total.toFixed(2));

        totalAmount += initialTotal;
        totalDiscount += discount;
        netTotal += total;
    });

    $('#total_amount').val(totalAmount.toFixed(2));
    $('#total_discount').val(totalDiscount.toFixed(2));
    $('#net_total').val(netTotal.toFixed(2));

    if ($('#paidAmountOption').val() === 'paid_in_full') {
        $('#paid_amount_input').val(netTotal.toFixed(2));
    }
}

$('#paidAmountOption').change(function() {
    if ($(this).val() === 'custom_amount') {
        $('#paid_amount_input').prop('disabled', false);
    } else {
        $('#paid_amount_input').prop('disabled', true);
        $('#paid_amount_input').val($('#net_total').val());
    }
});

$('#addProductBtn').click(function() {
    let productOptions = products.map(p => `<option value="${p.id}" data-price="${p.price}" data-qty="${p.quantity}">${p.product.name}</option>`).join('');
    let row = `<tr>
        <td><select name="stock_id[]" class="form-control stock_select">${productOptions}</select></td>
        <td class="available_qty">0</td>
        <td><input type="number" name="product_price[]" class="form-control product_price" value="0" min="0"></td>
        <td><input type="number" name="product_quantity[]" class="form-control product_quantity" value="1" min="1"></td>
        <td>
            <select name="discount_type[]" class="form-control discount_type">
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed</option>
            </select>
        </td>
        <td><input type="number" name="discount_amount[]" class="form-control discount_amount" value="0" min="0"></td>
        <td><input type="text" name="product_total[]" class="form-control product_total" readonly></td>
        <td><button type="button" class="btn btn-danger removeProduct">Remove</button></td>
    </tr>`;
    $('#productsTable tbody').append(row);
    updateTotals();
});

$(document).on('change', '.stock_select, .product_price, .product_quantity, .discount_type, .discount_amount', function() {
    updateTotals();
});

$(document).on('click', '.removeProduct', function() {
    $(this).closest('tr').remove();
    updateTotals();
});

$('#product_category').change(function() {
    let categoryId = $(this).val();
    $.get("{{ route('sells.get.product.by.category') }}", { category_id: categoryId }, function(res) {
        products = res.products.map(p => ({
            id: p.stock.id,
            product: p.product,
            price: p.sell_prices.length ? p.sell_prices[0].price : 0,
            quantity: p.quantity
        }));
    });
});

updateTotals(); // Initialize totals
</script>
@endsection

@endsection
