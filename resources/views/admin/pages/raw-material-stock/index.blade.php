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
    <div class="card">
        <div class="card-body table-responsive">
            <table id="rawMaterialStocksTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Raw Material</th>
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
                    @foreach($stocks as $key => $s)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $s->raw_material->name ?? '' }}</td>
                        <td>{{ $s->quantity ?? '' }}</td>
                        <td>{{ $s->price ?? '' }}</td>
                        <td>{{ $s->warehouse->name ?? '' }}</td>
                        <td>{{ $s->color->color_name ?? '' }}</td>
                        <td>{{ $s->brand->name ?? '' }}</td>
                        <td>{{ $s->size->name ?? '' }}</td>
                        <td class="text-center">
                            @can('raw-material-stock-view')
                            <button type="button" class="btn btn-info btn-sm px-1 py-0"
                                data-bs-toggle="modal" data-bs-target="#viewRawMaterialStockModal{{ $s->id }}">
                                View
                            </button>
                            @endcan
                        </td>
                    </tr>

                    {{-- VIEW MODAL --}}
                    <div class="modal fade" id="viewRawMaterialStockModal{{ $s->id }}" tabindex="-1" aria-labelledby="viewRawMaterialStockModalLabel{{ $s->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewRawMaterialStockModalLabel{{ $s->id }}">View Stock - {{ $s->raw_material->name ?? '' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered w-100">
                                        <tr>
                                            <th style="width:30%;">Raw Material</th>
                                            <td>{{ $s->raw_material->name ?? '' }}</td>
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
                                            <td>{{ $s->warehouse->name ?? '' }}</td>
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

@endsection

@section('js')
<script>
    $(document).ready(function(){});
</script>
@endsection
