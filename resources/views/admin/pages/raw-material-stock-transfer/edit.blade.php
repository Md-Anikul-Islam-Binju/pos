@extends('admin.app')

@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                    <li class="breadcrumb-item active">Edit Raw Material Stock Transfer</li>
                </ol>
            </div>
            <h4 class="page-title">Edit Raw Material Stock Transfer</h4>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('raw.material.stock.transfer.update', $transfer->id) }}" method="POST" enctype="multipart/form-data" id="admin-form">
                @csrf
                @method('PUT')

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
                            <label for="from_warehouse_id">From Warehouse</label>
                            <select id="from_warehouse_id" name="from_warehouse_id" class="form-control" readonly>
                                <option value="{{ $transfer->from_warehouse_id }}">{{ $transfer->fromWarehouse->name ?? 'Deleted' }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="to_warehouse_id">To Warehouse <span class="text-danger">*</span></label>
                            <select id="to_warehouse_id" name="to_warehouse_id" class="form-control" required>
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ $warehouse->id == $transfer->to_warehouse_id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ $transfer->date }}" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label for="note">Details</label>
                            <textarea name="note" id="note" class="form-control">{{ strip_tags($transfer->note) ?? '' }}</textarea>
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
                            <tbody id="raw-material-table-body">
                                <!-- Filled dynamically via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                @can('raw-material-stock-transfer-edit')
                    <button type="submit" class="btn btn-success mt-2">Update Transfer</button>
                @endcan
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    const selectedRawMaterialIds = @json($transfer->rawMaterialStocks->pluck('id'));

    function fetchRawMaterials(warehouseId) {
        const $container = $('#raw-material-table-container');
        const $tbody = $('#raw-material-table-body');

        if (!warehouseId) {
            $container.hide();
            $tbody.empty();
            return;
        }

        $.ajax({
            url: '/raw-material-stocks/' + warehouseId,
            type: 'GET',
            success: function(data) {
                $tbody.empty();
                if (data.length > 0) {
                    $container.show();
                    data.forEach(material => {
                        const isChecked = selectedRawMaterialIds.includes(material.id);
                        $tbody.append(`
                            <tr>
                                <td>
                                    <input type="checkbox" class="raw-material-checkbox" name="selected_raw_materials[]" value="${material.id}" ${isChecked ? 'checked' : ''}>
                                </td>
                                <td>${material.raw_material_name}</td>
                                <td>${material.quantity}</td>
                                <td>
                                    <input type="number" name="transfer_quantities[${material.id}]" class="form-control transfer-quantity"
                                        value="${isChecked ? material.transfer_quantity : 0}" min="1" max="${material.quantity}" ${isChecked ? '' : 'disabled'}>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $container.hide();
                }
            }
        });
    }

    // Initial load for existing transfer
    fetchRawMaterials({{ $transfer->to_warehouse_id }});

    // On warehouse change
    $('#to_warehouse_id').change(function() {
        fetchRawMaterials($(this).val());
    });

    const $tbody = $('#raw-material-table-body');

    // Toggle quantity input based on checkbox
    $tbody.on('change', '.raw-material-checkbox', function() {
        const row = $(this).closest('tr');
        const qtyInput = row.find('.transfer-quantity');
        qtyInput.prop('disabled', !$(this).is(':checked'));
        if (!$(this).is(':checked')) qtyInput.val(0);
    });

    // Ensure transfer quantity does not exceed available stock
    $tbody.on('input', '.transfer-quantity', function() {
        const $input = $(this);
        const max = parseInt($input.attr('max') || 0, 10);
        let val = parseInt($input.val() || 0, 10);
        if (val > max) $input.val(max);
        if (val < 1) $input.val(1);
    });

    // Search/filter table rows
    $('#search-raw-material').on('input', function() {
        const term = $(this).val().toLowerCase();
        $tbody.find('tr').each(function() {
            const name = $(this).find('td:eq(1)').text().toLowerCase();
            $(this).toggle(name.includes(term));
        });
    });
});
</script>
@endsection
