@extends('admin.app')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                {{-- Header --}}
                <div class="text-center mb-4">
                    <h3>Production Invoice</h3>
                    <p>Production ID: <strong>{{ $production->id }}</strong></p>
                    <p>Date: <strong>{{ $production->production_date->format('d-m-Y') }}</strong></p>
                    <p>Status: <strong>{{ ucfirst($production->status) }}</strong></p>
                </div>

                {{-- General Info --}}
                <h5>General Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Production House</th>
                        <td>{{ $production->productionHouse->name ?? 'N/A' }}</td>
                        <th>Showroom</th>
                        <td>{{ $production->showroom->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Account</th>
                        <td>{{ $production->account->name ?? 'N/A' }}</td>
                        <th>Payment Type</th>
                        <td>{{ ucfirst($production->payment_type) }}</td>
                    </tr>
                </table>

                {{-- Cost Details --}}
                <h5 class="mt-4">Cost Details</h5>
                @php $costs = json_decode($production->cost_details, true) @endphp
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Detail</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($costs as $cost)
                        <tr>
                            <td>{{ $cost['detail'] ?? '' }}</td>
                            <td>{{ number_format($cost['amount'] ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center">No cost details</td>
                        </tr>
                        @endforelse
                        <tr>
                            <th>Total Cost</th>
                            <th>{{ number_format($production->total_cost, 2) }}</th>
                        </tr>
                    </tbody>
                </table>

                {{-- Raw Materials --}}
                <h5 class="mt-4">Raw Materials</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Raw Material</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Warehouse</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($production->rawMaterials as $raw)
                        <tr>
                            <td>{{ $raw->rawMaterial->name ?? 'N/A' }}</td>
                            <td>{{ $raw->brand->name ?? '-' }}</td>
                            <td>{{ $raw->size->name ?? '-' }}</td>
                            <td>{{ $raw->color->name ?? '-' }}</td>
                            <td>{{ $raw->warehouse->name ?? '-' }}</td>
                            <td>{{ number_format($raw->price, 2) }}</td>
                            <td>{{ $raw->quantity }}</td>
                            <td>{{ number_format($raw->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="7" class="text-end">Total Raw Material Cost</th>
                            <th>{{ number_format($production->total_raw_material_cost, 2) }}</th>
                        </tr>
                    </tbody>
                </table>

                {{-- Products --}}
                <h5 class="mt-4">Products</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Per Unit Cost</th>
                            <th>Quantity</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($production->products as $prod)
                        <tr>
                            <td>{{ $prod->product->name ?? 'N/A' }}</td>
                            <td>{{ $prod->brand->name ?? '-' }}</td>
                            <td>{{ $prod->size->name ?? '-' }}</td>
                            <td>{{ $prod->color->name ?? '-' }}</td>
                            <td>{{ number_format($prod->per_pc_cost, 2) }}</td>
                            <td>{{ $prod->quantity }}</td>
                            <td>{{ number_format($prod->sub_total, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <th colspan="6" class="text-end">Total Product Cost</th>
                            <th>{{ number_format($production->total_product_cost, 2) }}</th>
                        </tr>
                        <tr>
                            <th colspan="6" class="text-end">Net Total</th>
                            <th>{{ number_format($production->net_total, 2) }}</th>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center mt-4">
                    <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
                    <a href="{{ route('production.section') }}" class="btn btn-secondary">Back to List</a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
