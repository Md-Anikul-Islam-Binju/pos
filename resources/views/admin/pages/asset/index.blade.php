@extends('admin.app')
@section('admin_content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                    <li class="breadcrumb-item active">Asset!</li>
                </ol>
            </div>
            <h4 class="page-title">Asset!</h4>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Assets</h5>
            <div class="d-flex gap-2">
                <select id="statusFilter" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="approved">Approved</option>
                    <option value="pending">Pending</option>
                    <option value="rejected">Rejected</option>
                </select>
                @can('asset-create')
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addAssetModal">Add New Asset</button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <table id="assetTable" class="table table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asset as $key => $assetData)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $assetData->name }}</td>
                            <td>{{ $assetData->category ? $assetData->category->name : '-' }}</td>
                            <td>{{ number_format($assetData->amount, 2) }}</td>
                            <td>{{ $assetData->account ? $assetData->account->name : '-' }}</td>
                            <td class="status-column">
                                @if($assetData->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($assetData->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    @can('asset-edit')
                                        <button class="btn btn-info btn-sm edit-btn"
                                                data-id="{{ $assetData->id }}"
                                                data-name="{{ $assetData->name }}"
                                                data-category="{{ $assetData->asset_category_id }}"
                                                data-account="{{ $assetData->account_id }}"
                                                data-amount="{{ $assetData->amount }}"
                                                data-status="{{ $assetData->status }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editAssetModal">
                                            Edit
                                        </button>
                                    @endcan
                                    @can('asset-delete')
                                        <button class="btn btn-danger btn-sm delete-btn"
                                                data-id="{{ $assetData->id }}"
                                                data-name="{{ $assetData->name }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteAssetModal">
                                            Delete
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Asset</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('asset.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="add_name" class="form-label">Name</label>
                            <input type="text" id="add_name" name="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label for="add_category" class="form-label">Category</label>
                            <select id="add_category" name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label for="add_account" class="form-label">Account</label>
                            <select id="add_account" name="account_id" class="form-select" required>
                                <option value="">Select Account</option>
                                @foreach($account as $acc)
                                    <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                @endforeach
                            </select>
                            @error('account_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label for="add_amount" class="form-label">Amount</label>
                            <input type="number" id="add_amount" name="amount" step="0.01" class="form-control" value="{{ old('amount') }}" required>
                            @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-6 mb-3">
                            <label for="add_status" class="form-label">Status</label>
                            <select id="add_status" name="status" class="form-select">
                                <option value="pending" selected>Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Asset Modal -->
<div class="modal fade" id="editAssetModal" tabindex="-1" aria-labelledby="editAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Asset</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editAssetForm" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" id="edit_name" name="name" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Category</label>
                            <select id="edit_category" name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Account</label>
                            <select id="edit_account" name="account_id" class="form-select" required>
                                <option value="">Select Account</option>
                                @foreach($account as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" id="edit_amount" name="amount" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Status</label>
                            <select id="edit_status" name="status" class="form-select">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Asset Modal -->
<div class="modal fade" id="deleteAssetModal" tabindex="-1" aria-labelledby="deleteAssetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title">Delete Asset</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteAssetName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <a href="#" id="deleteAssetBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Include DataTables JS -->
<script>
    $(document).ready(function() {
        var table = $('#assetTable').DataTable({
            responsive: true,
            pageLength: 25,
            columnDefs: [
                { orderable: false, targets: 6 } // Disable sorting on Action column
            ]
        });

        // Status filter
        $('#statusFilter').on('change', function () {
            var val = $(this).val();
            table.column('.status-column').search(val, true, false).draw();
        });

        // Populate Edit Modal
        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            $('#editAssetForm').attr('action', `/asset-update/${id}`);
            $('#edit_id').val(id);
            $('#edit_name').val($(this).data('name'));
            $('#edit_category').val($(this).data('category'));
            $('#edit_account').val($(this).data('account'));
            $('#edit_amount').val($(this).data('amount'));
            $('#edit_status').val($(this).data('status'));
        });

        // Populate Delete Modal
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#deleteAssetName').text(name);
            $('#deleteAssetBtn').attr('href', `/asset-delete/${id}`);
        });
    });
</script>
@endsection
