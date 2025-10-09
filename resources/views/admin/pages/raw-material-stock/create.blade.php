@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item active">Raw Material Stocks</li>
                    </ol>
                </div>
                <h4 class="page-title">Raw Material Stocks</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        @can('raw-material-stock-list')
            @if(isset($stock))
                {{-- Show Single Stock Details --}}
                <div class="card">
                    <div class="card-header"><h4>Stock Details</h4></div>
                    <div class="card-body">
                        <table class="table table-bordered w-100 text-left mb-3">
                            <tr><th style="width:30%;">Raw Material</th><td>{{ $stock->raw_material->name ?? '' }}</td></tr>
                            <tr><th>Quantity</th><td>{{ $stock->quantity ?? '' }}</td></tr>
                            <tr><th>Price</th><td>{{ $stock->price ?? '' }}</td></tr>
                            <tr><th>Warehouse</th><td>{{ $stock->warehouse->name ?? '' }}</td></tr>
                            <tr><th>Color</th><td>{{ $stock->color->color_name ?? '' }}</td></tr>
                            <tr><th>Brand</th><td>{{ $stock->brand->name ?? '' }}</td></tr>
                            <tr><th>Size</th><td>{{ $stock->size->name ?? '' }}</td></tr>
                        </table>
                        <a href="{{ route('raw.material.stock.section') }}" class="btn btn-success">Go Back</a>
                    </div>
                </div>
            @else
                {{-- Show All Raw Material Stocks --}}
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-end">
                            {{-- Optional buttons can go here --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Warehouse</th>
                                    <th>Color</th>
                                    <th>Brand</th>
                                    <th>Size</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $s)
                                    <tr>
                                        <td>{{ $s->raw_material->name ?? '' }}</td>
                                        <td>{{ $s->quantity ?? '' }}</td>
                                        <td>{{ $s->price ?? '' }}</td>
                                        <td>{{ $s->warehouse->name ?? '' }}</td>
                                        <td>{{ $s->color->color_name ?? '' }}</td>
                                        <td>{{ $s->brand->name ?? '' }}</td>
                                        <td>{{ $s->size->name ?? '' }}</td>
                                        <td class="text-center">
                                            @can('raw-material-stock-view')
                                                <a href="{{ route('raw.material.stock.show', $s->id) }}" class="btn btn-info btn-sm px-1 py-0">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endcan
    </div>

@endsection
