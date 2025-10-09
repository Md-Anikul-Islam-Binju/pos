@extends('admin.app')
@section('admin_content')

<div class="row">
    <div class="col-12 text-center mb-3">
        <h2>Invoice</h2>
        <h4>Sell ID: {{ $sell->unique_sale_id }}</h4>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <h5>Customer Info</h5>
        <p><strong>Name:</strong> {{ $sell->customer->name ?? '-' }}</p>
        <p><strong>Account:</strong> {{ $sell->account->name ?? '-' }}</p>
    </div>
    <div class="col-md-6 text-end">
        <h5>Salesman Info</h5>
        <p><strong>Name:</strong> {{ $sell->salesman->name ?? '-' }}</p>
        <p><strong>Date:</strong> {{ $sell->created_at->format('d M, Y') }}</p>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sell->sellProducts as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item->stock->product->name ?? '-' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->discount_type == 'percentage' ? $item->discount_amount.'%' : $item->discount_amount }}</td>
            <td>{{ $item->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="row mt-3">
    <div class="col-md-4 offset-md-8">
        <p><strong>Total Amount:</strong> {{ $sell->total_amount }}</p>
        <p><strong>Total Discount:</strong> {{ $sell->total_discount }}</p>
        <p><strong>Net Total:</strong> {{ $sell->net_total }}</p>
        <p><strong>Paid Amount:</strong> {{ $sell->amount }}</p>
        <p><strong>Status:</strong> {{ ucfirst($sell->status) }}</p>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12 text-center">
        <p>Thank you for your business!</p>
    </div>
</div>

@endsection
