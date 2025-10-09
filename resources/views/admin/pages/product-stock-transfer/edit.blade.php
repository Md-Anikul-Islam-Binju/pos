@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                   <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                        <li class="breadcrumb-item active">Edit Product Stock Transfer</li>
                    </ol>
                </div>
                <h4 class="page-title">Edit Product Stock Transfer</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('product.stock.transfer.update', $transfer->id) }}" method="POST" enctype="multipart/form-data" id="admin-form">
                        @csrf
                        @method('PUT')
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="from_showroom_id">From Showroom <span class="text-danger font-weight-bolder">*</span></label>
                                    <select id="from_showroom_id" name="from_showroom_id" class="form-control " readonly required>
                                        <option value="{{ $transfer->from_showroom_id}}">{{$transfer->fromShowroom->name??'Deleted'}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="to_showroom_id">To Showroom <span class="text-danger font-weight-bolder">*</span></label>
                                    <select id="to_showroom_id" name="to_showroom_id" class="form-control " required>
                                        <option value="">Select Showroom</option>
                                        @foreach ($showrooms as $showroom)
                                            <option value="{{ $showroom->id }}" {{ $showroom->id == $transfer->to_showroom_id ? 'selected' : '' }}>
                                                {{ $showroom->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="date">Date <span class="text-danger font-weight-bolder">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ $transfer->date }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note">Details</label>
                                    <textarea name="note" class="form-control">{{ strip_tags($transfer->note ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Table for displaying product info and quantity input -->
                        <div class="row" id="product-table-container">
                            <div class="col-12">
                                <table id="activityTable" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Product Name</th>
                                        <th>Brand</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Quantity</th>
                                        <th>Transfer Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody id="product-table-body">
                                    @foreach($showroomProducts as $product)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="product-checkbox" name="selected_products[]" value="{{ $product->id }}"
                                                       @if($transfer->productStocks->pluck('id')->contains($product->id)) checked @endif>
                                            </td>
                                            <td>{{ $product->product->name }}</td>
                                            <td>{{ $product->brand->name }}</td>
                                            <td>{{ $product->color->color_name }}</td>
                                            <td>{{ $product->size->name }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>
                                                <input type="number" name="transfer_quantities[{{ $product->id }}]" class="form-control transfer-quantity"
                                                       value="{{ old('transfer_quantities.' . $product->id, $transfer->productStocks->firstWhere('id', $product->id)->pivot->quantity ?? 0) }}"
                                                       min="1" max="{{ $product->quantity }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($showroomProducts->isEmpty())
                                        <tr>
                                            <td colspan="7">No products available in this showroom.</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @can('productStockTransfers.update')
                            <button class="btn btn-success" type="submit">Update</button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Get the current 'to_showroom_id' from the transfer data
            var selectedToShowroomId = {{ $transfer->to_showroom_id }};

            // Get selected product IDs from the transfer
            var selectedProductIds = @json($transfer->productStocks->pluck('id'));

            // Fetch products for the selected showroom when page loads
            $.ajax({
                url: '/product-stocks/' + selectedToShowroomId,
                type: 'GET',
                success: function(data) {
                    // Check if there are products available
                    if (data.length > 0) {
                        // Show the product table container
                        $('#product-table-container').show();

                        // Populate the product table with fetched data
                        $.each(data, function(index, product) {
                            // Check if product is selected in the transfer
                            let isChecked = @json($transfer->productStocks->pluck('id')->toArray()).includes(product.id);

                            $('#product-table-body').append(`
                                <tr>
                                    <td><input type="checkbox" class="product-checkbox" name="selected_products[]" value="${product.id}" ${isChecked ? 'checked' : ''}></td>
                                    <td>${product.product_name}</td>
                                    <td>${product.brand_name}</td>
                                    <td>${product.color_name}</td>
                                    <td>${product.size_name}</td>
                                    <td>${product.quantity}</td>
                                    <td>
                                        <input type="number" name="transfer_quantities[${product.id}]" class="form-control transfer-quantity"
                                            value="${product.transfer_quantity || 0}" min="1" max="${product.quantity}" ${isChecked ? '' : 'disabled'}>
                                    </td>
                                </tr>
                            `);
                        });

                        // Add event listener for toggling 'required' on transfer quantity input
                        $('#product-table-body').on('change', '.product-checkbox', function() {
                            const isChecked = $(this).is(':checked');
                            const row = $(this).closest('tr');
                            row.find('.transfer-quantity').prop('disabled', !isChecked);
                        });
                    } else {
                        $('#product-table-body').html('<tr><td colspan="7">No products available in this showroom.</td></tr>');
                    }
                }
            });

            // Prevent form submission if required fields are not selected
            $('#admin-form').submit(function(e) {
                var selectedProducts = $('input[name="selected_products[]"]:checked').length;
                if (selectedProducts == 0) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
