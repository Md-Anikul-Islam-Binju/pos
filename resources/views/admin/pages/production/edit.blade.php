@extends('admin.app')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Production</a></li>
                    <li class="breadcrumb-item active">Edit Production</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Production</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @can('production-edit')
                <form action="{{ route('production.update', $production->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">

                        {{-- Production House --}}
                        <div class="col-md-4 mb-3">
                            <label for="production_house_id">Production House <span class="text-danger">*</span></label>
                            <select name="production_house_id" id="production_house_id" class="form-control" required>
                                <option value="">Select Production House</option>
                                @foreach($houses as $house)
                                <option value="{{ $house->id }}" {{ $production->production_house_id == $house->id ? 'selected' : '' }}>{{ $house->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Showroom --}}
                        <div class="col-md-4 mb-3">
                            <label for="showroom_id">Showroom <span class="text-danger">*</span></label>
                            <select name="showroom_id" id="showroom_id" class="form-control" required>
                                <option value="">Select Showroom</option>
                                @foreach($showrooms as $showroom)
                                <option value="{{ $showroom->id }}" {{ $production->showroom_id == $showroom->id ? 'selected' : '' }}>{{ $showroom->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Account --}}
                        <div class="col-md-4 mb-3">
                            <label for="account_id">Account <span class="text-danger">*</span></label>
                            <select name="account_id" id="account_id" class="form-control" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ $production->account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Production Date --}}
                        <div class="col-md-4 mb-3">
                            <label for="production_date">Production Date <span class="text-danger">*</span></label>
                            <input type="date" name="production_date" id="production_date" class="form-control" value="{{ $production->production_date->format('Y-m-d') }}" required>
                        </div>

                        {{-- Warehouse --}}
                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control" required>
                                <option value="">Select Warehouse</option>
                                @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $production->warehouse_id == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment Type --}}
                        <div class="col-md-4 mb-3">
                            <label for="payment_type">Payment Type</label>
                            <select name="payment_type" id="payment_type" class="form-control">
                                <option value="full_paid" {{ $production->payment_type == 'full_paid' ? 'selected' : '' }}>Full Paid</option>
                                <option value="partial_paid" {{ $production->payment_type == 'partial_paid' ? 'selected' : '' }}>Partial Paid</option>
                            </select>
                        </div>

                    </div>

                    {{-- Cost Details --}}
                    <hr>
                    <h5>Cost Details</h5>
                    <div id="cost-details-wrapper">
                        @foreach(json_decode($production->cost_details, true) as $cost)
                        <div class="row cost-detail-row mb-2">
                            <div class="col-md-6">
                                <input type="text" name="cost_details[]" class="form-control" placeholder="Cost Detail" value="{{ $cost['detail'] }}">
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="cost_amount[]" class="form-control" placeholder="Amount" step="0.01" value="{{ $cost['amount'] }}">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-cost">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-cost">Add More Cost</button>

                    {{-- Raw Materials --}}
                    <hr>
                    <h5>Raw Materials</h5>
                    <div id="raw-material-wrapper">
                        @foreach($existingRawMaterials as $raw)
                        <div class="row raw-material-row mb-2">
                            <div class="col-md-3">
                                <select name="raw_material_id[]" class="form-control">
                                    <option value="">Select Raw Material</option>
                                    @foreach($rawMaterialStocks as $stock)
                                    <option value="{{ $stock->id }}" {{ $raw->raw_material_id == $stock->id ? 'selected' : '' }}>{{ $stock->raw_material->name }} ({{ $stock->warehouse->name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="raw_material_brand_id[]" class="form-control">
                                    <option value="">Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $raw->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="raw_material_size_id[]" class="form-control">
                                    <option value="">Size</option>
                                    @foreach($sizes as $size)
                                    <option value="{{ $size->id }}" {{ $raw->size_id == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="raw_material_color_id[]" class="form-control">
                                    <option value="">Color</option>
                                    @foreach($colors as $color)
                                    <option value="{{ $color->id }}" {{ $raw->color_id == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="raw_material_quantity[]" class="form-control" placeholder="Qty" step="0.01" value="{{ $raw->quantity }}">
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="raw_material_price[]" class="form-control" placeholder="Price" step="0.01" value="{{ $raw->price }}">
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="raw_material_total_price[]" class="form-control" placeholder="Total" step="0.01" value="{{ $raw->total_price }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-raw-material">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-raw-material">Add More Raw Material</button>

                    {{-- Products --}}
                    <hr>
                    <h5>Products</h5>
                    <div id="product-wrapper">
                        @foreach($existingProducts as $prod)
                        <div class="row product-row mb-2">
                            <div class="col-md-3">
                                <select name="product_id[]" class="form-control">
                                    <option value="">Select Product</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $prod->product_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="brand_id[]" class="form-control">
                                    <option value="">Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ $prod->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="size_id[]" class="form-control">
                                    <option value="">Size</option>
                                    @foreach($sizes as $size)
                                    <option value="{{ $size->id }}" {{ $prod->size_id == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="color_id[]" class="form-control">
                                    <option value="">Color</option>
                                    @foreach($colors as $color)
                                    <option value="{{ $color->id }}" {{ $prod->color_id == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="quantity[]" class="form-control" placeholder="Qty" step="0.01" value="{{ $prod->quantity }}">
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="price[]" class="form-control" placeholder="Cost/pc" step="0.01" value="{{ $prod->per_pc_cost }}">
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="total_price[]" class="form-control" placeholder="Sub Total" step="0.01" value="{{ $prod->sub_total }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger remove-product">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-primary mb-3" id="add-product">Add More Product</button>

                    <button type="submit" class="btn btn-success">Update Production</button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Add / Remove Cost Details
    $('#add-cost').click(function() {
        $('#cost-details-wrapper').append(`
            <div class="row cost-detail-row mb-2">
                <div class="col-md-6">
                    <input type="text" name="cost_details[]" class="form-control" placeholder="Cost Detail">
                </div>
                <div class="col-md-4">
                    <input type="number" name="cost_amount[]" class="form-control" placeholder="Amount" step="0.01">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-cost">Remove</button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.remove-cost', function() {
        $(this).closest('.cost-detail-row').remove();
    });

    // Add / Remove Raw Material
    $('#add-raw-material').click(function() {
        var row = $('.raw-material-row:first').clone();
        row.find('input, select').val('');
        $('#raw-material-wrapper').append(row);
    });
    $(document).on('click', '.remove-raw-material', function() {
        $(this).closest('.raw-material-row').remove();
    });

    // Add / Remove Product
    $('#add-product').click(function() {
        var row = $('.product-row:first').clone();
        row.find('input, select').val('');
        $('#product-wrapper').append(row);
    });
    $(document).on('click', '.remove-product', function() {
        $(this).closest('.product-row').remove();
    });
</script>
@endsection
