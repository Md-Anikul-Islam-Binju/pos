@extends('admin.app')
@section('admin_content')

<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4>Sell Details - {{ $sell->unique_sale_id }}</h4>
        <a href="{{ route('sells.index') }}" class="btn btn-primary">Back to List</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <p><strong>Customer:</strong> {{ $sell->customer->name ?? '-' }}</p>
        <p><strong>Salesman:</strong> {{ $sell->salesman->name ?? '-' }}</p>
        <p><strong>Account:</strong> {{ $sell->account->name ?? '-' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($sell->status) }}</p>
        <p><strong>Total Amount:</strong> {{ $sell->total_amount }}</p>
        <p><strong>Total Discount:</strong> {{ $sell->total_discount }}</p>
        <p><strong>Net Total:</strong> {{ $sell->net_total }}</p>
        <p><strong>Paid Amount:</strong> {{ $sell->amount }}</p>
        <p><strong>Created At:</strong> {{ $sell->created_at->format('d M, Y H:i') }}</p>
    </div>
</div>

<h5>Products</h5>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sell->sellProducts as $item)
        <tr>
            <td>{{ $item->stock->product->name ?? '-' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->price }}</td>
            <td>{{ $item->discount_type == 'percentage' ? $item->discount_amount.'%' : $item->discount_amount }}</td>
            <td>{{ $item->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@can('sell-view')
<a href="{{ route('sells.invoice', $sell->id) }}" target="_blank" class="btn btn-success">View Invoice</a>
@endcan

@endsection
