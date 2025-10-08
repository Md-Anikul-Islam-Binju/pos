@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.product-stock-transfers.index') }}">Product Stock Transfers</a></li>
                        <li class="breadcrumb-item active">Create Product Stock Transfer</li>
                    </ol>
                </div>
                <h4 class="page-title">Create Product Stock Transfer</h4>
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
                    <form action="{{ route('admin.product-stock-transfers.store') }}" method="POST" enctype="multipart/form-data" id="admin-form">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="from_showroom_id">From Showroom <span class="text-danger">*</span></label>
                                    <select id="from_showroom_id" name="from_showroom_id" class="form-control" required>
                                        <option value="">Select Showroom</option>
                                        @foreach ($showrooms as $showroom)
                                            <option value="{{ $showroom->id }}">{{ $showroom->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="to_showroom_id">To Showroom <span class="text-danger">*</span></label>
                                    <select id="to_showroom_id" name="to_showroom_id" class="form-control" required>
                                        <option value="">Select Showroom</option>
                                        @foreach ($showrooms as $showroom)
                                            <option value="{{ $showroom->id }}">{{ $showroom->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note">Details</label>
                                    <textarea name="note" id="note" class="form-control" rows="4" placeholder="Enter details..."></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Product Table --}}
                        <div class="row" id="product-table-container" style="display:none;">
                            <div class="col-12">
                                <input type="text" id="search-product" class="form-control mb-2" placeholder="Search for a product...">
                                <table class="table table-bordered">
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
                                    <tbody id="product-table-body"></tbody>
                                </table>
                            </div>
                        </div>

                        @can('productStockTransfers.create')
                            <button class="btn btn-success" type="submit">Create</button>
                        @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function () {

            // Cache the original options of the 'to_showroom_id' dropdown
            var originalToShowroomOptions = $('#to_showroom_id').html();

            // Listen for changes in 'from_showroom_id'
            $('#from_showroom_id').on('change', function () {
                var selectedFromShowroomId = $(this).val();

                // Restore the original options to 'to_showroom_id'
                $('#to_showroom_id').html(originalToShowroomOptions);

                if (selectedFromShowroomId) {
                    // Remove the selected 'from' showroom option from 'to_showroom_id'
                    $('#to_showroom_id option[value="' + selectedFromShowroomId + '"]').remove();

                    // Fetch products for the selected showroom
                    $.ajax({
                        url: '/api/product-stocks/' + selectedFromShowroomId,
                        type: 'GET',
                        success: function (data) {
                            $('#product-table-body').empty();

                            if (data.length > 0) {
                                $('#product-table-container').show();

                                $.each(data, function (index, product) {
                                    $('#product-table-body').append(`
                                <tr>
                                    <td><input type="checkbox" class="product-checkbox" name="selected_products[]" value="${product.id}"></td>
                                    <td>${product.product_name}</td>
                                    <td>${product.brand_name}</td>
                                    <td>${product.color_name}</td>
                                    <td>${product.size_name}</td>
                                    <td>${product.quantity}</td>
                                    <td>
                                        <input type="number" name="transfer_quantities[${product.id}]" class="form-control transfer-quantity"
                                            min="1" max="${product.quantity}" disabled>
                                    </td>
                                </tr>
                            `);
                                });

                                // Enable or disable transfer quantity inputs
                                $('#product-table-body').on('change', '.product-checkbox', function () {
                                    const isChecked = $(this).is(':checked');
                                    const quantityInput = $(this).closest('tr').find('.transfer-quantity');
                                    quantityInput.prop('required', isChecked).prop('disabled', !isChecked);
                                });
                            } else {
                                $('#product-table-body').html('<tr><td colspan="7" class="text-center">No products available</td></tr>');
                            }
                        },
                        error: function () {
                            alert('Error fetching products.');
                        }
                    });
                }
            });

            // Search filter
            $('#search-product').on('keyup', function () {
                var searchTerm = $(this).val().toLowerCase();
                $('#product-table-body tr').each(function () {
                    var productName = $(this).find('td').eq(1).text().toLowerCase();
                    var brand = $(this).find('td').eq(2).text().toLowerCase();
                    var color = $(this).find('td').eq(3).text().toLowerCase();
                    var size = $(this).find('td').eq(4).text().toLowerCase();

                    if (productName.includes(searchTerm) || brand.includes(searchTerm) || color.includes(searchTerm) || size.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Validate transfer quantity
            $(document).on('input', '.transfer-quantity', function () {
                let maxQuantity = parseInt($(this).attr('max'));
                let enteredQuantity = parseInt($(this).val());

                if (enteredQuantity > maxQuantity) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Quantity Exceeds Available Stock',
                        text: `You can transfer up to ${maxQuantity} units only.`,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $(this).val(maxQuantity);
                    });
                }
            });

        });
    </script>
@endsection
