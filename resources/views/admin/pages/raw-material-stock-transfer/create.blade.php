@extends('admin.app')

@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                    <li class="breadcrumb-item active">Create Raw Material Stock Transfer</li>
                </ol>
            </div>
            <h4 class="page-title">Create Raw Material Stock Transfer</h4>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('raw.material.stock.transfer.store') }}" method="POST" enctype="multipart/form-data" id="admin-form">
                @csrf

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="from_warehouse_id">From Warehouse <span class="text-danger">*</span></label>
                            <select id="from_warehouse_id" name="from_warehouse_id" class="form-control " required>
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="to_warehouse_id">To Warehouse <span class="text-danger">*</span></label>
                            <select id="to_warehouse_id" name="to_warehouse_id" class="form-control " required>
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="note">Details</label>
                            <textarea name="note" id="summernote" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Raw Material Table -->
                <div class="row" id="raw-material-table-container" style="display:none;">
                    <div class="col-12 mb-2">
                        <input type="text" id="search-raw-material" class="form-control" placeholder="Search raw material...">
                    </div>
                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Raw Material Name</th>
                                    <th>Available Quantity</th>
                                    <th>Transfer Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="raw-material-table-body"></tbody>
                        </table>
                    </div>
                </div>

                @can('raw-material-stock-transfer-create')
                <button class="btn btn-success" type="submit">Create Transfer</button>
                @endcan
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {

    // Hide table initially
    $('#raw-material-table-container').hide();

    // When "from warehouse" changes
    $('#from_warehouse_id').on('change', function() {
        let fromWarehouseId = $(this).val();
        let toWarehouseIdDropdown = $('#to_warehouse_id');

        // Show all options and hide selected "from" warehouse
        toWarehouseIdDropdown.find('option').show();
        if (fromWarehouseId) {
            toWarehouseIdDropdown.find('option[value="' + fromWarehouseId + '"]').hide();
        }

        $('#raw-material-table-body').empty();

        if (!fromWarehouseId) {
            $('#raw-material-table-container').hide();
            return;
        }

        // Fetch raw materials via AJAX
        $.ajax({
            url: '/raw-material-stocks/' + fromWarehouseId,
            type: 'GET',
            success: function(data) {
                if (data.length > 0) {
                    $('#raw-material-table-container').show();

                    data.forEach(function(raw) {
                        $('#raw-material-table-body').append(`
                            <tr>
                                <td><input type="checkbox" class="raw-material-checkbox" name="selected_raw_materials[]" value="${raw.id}"></td>
                                <td>${raw.raw_material_name}</td>
                                <td>${raw.quantity}</td>
                                <td>
                                    <input type="number" name="transfer_quantities[${raw.id}]" class="form-control transfer-quantity" min="1" max="${raw.quantity}" disabled>
                                </td>
                            </tr>
                        `);
                    });

                    // Checkbox toggle
                    $('#raw-material-table-body').on('change', '.raw-material-checkbox', function() {
                        let qtyInput = $(this).closest('tr').find('.transfer-quantity');
                        if ($(this).is(':checked')) {
                            qtyInput.prop('disabled', false).prop('required', true);
                        } else {
                            qtyInput.prop('disabled', true).prop('required', false).val('');
                        }
                    });

                    // Quantity max validation
                    $(document).on('input', '.transfer-quantity', function() {
                        let maxQty = parseInt($(this).attr('max') || 0);
                        let enteredQty = parseInt($(this).val() || 0);
                        if (enteredQty > maxQty) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Quantity Exceeds Available Stock',
                                text: `You can transfer up to ${maxQty} units only.`,
                                confirmButtonText: 'OK'
                            }).then(() => { $(this).val(maxQty); });
                        }
                    });

                } else {
                    $('#raw-material-table-container').show();
                    $('#raw-material-table-body').html('<tr><td colspan="4" class="text-center">No raw materials available</td></tr>');
                }
            },
            error: function() {
                alert('Error fetching raw materials.');
            }
        });
    });

    // Search functionality
    $('#search-raw-material').on('keyup', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#raw-material-table-body tr').each(function() {
            let name = $(this).find('td:eq(1)').text().toLowerCase();
            $(this).toggle(name.indexOf(searchTerm) > -1);
        });
    });

});
</script>
@endsection
