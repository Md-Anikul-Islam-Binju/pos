@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.product-stock-transfers.index') }}">Product Stock Transfers</a></li>
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
                    <form action="{{ route('admin.product-stock-transfers.update', $transfer->id) }}" method="POST" enctype="multipart/form-data" id="admin-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="from_showroom_id">From Showroom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" value="{{ $transfer->fromShowroom->name ?? 'Deleted' }}" readonly>
                                    <input type="hidden" name="from_showroom_id" value="{{ $transfer->from_showroom_id }}">
                                </div>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <label for="to_showroom_id">To Showroom <span class="text-danger">*</span></label>
                                    <select id="to_showroom_id" name="to_showroom_id" class="form-control" required>
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
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ $transfer->date }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="note">Details</label>
                                    <textarea name="note" id="note" class="form-control" rows="4" placeholder="Enter details...">{{ $transfer->note }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Product Table --}}
                        <div class="row" id="product-table-container">
                            <div class="col-12">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Product Name</th>
                                        <th>Brand</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Available Qty</th>
                                        <th>Transfer Qty</th>
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
                                                <input type="number"
                                                       name="transfer_quantities[{{ $product->id }}]"
                                                       class="form-control transfer-quantity"
                                                       value="{{ old('transfer_quantities.' . $product->id, $transfer->productStocks->firstWhere('id', $product->id)->pivot->quantity ?? 0) }}"
                                                       min="1"
                                                       max="{{ $product->quantity }}"
                                                       @if(!$transfer->productStocks->pluck('id')->contains($product->id)) disabled @endif>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($showroomProducts->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center">No products available in this showroom.</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            @can('productStockTransfers.update')
                                <button class="btn btn-success" type="submit">Update</button>
                            @endcan
                            <a href="{{ route('admin.product-stock-transfers.index') }}" class="btn btn-secondary">Go Back</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Enable or disable transfer quantity input based on checkbox
            $('#product-table-body').on('change', '.product-checkbox', function() {
                const isChecked = $(this).is(':checked');
                const quantityInput = $(this).closest('tr').find('.transfer-quantity');
                quantityInput.prop('disabled', !isChecked).prop('required', isChecked);
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
