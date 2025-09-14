@extends('admin.app')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                    <li class="breadcrumb-item active">Deposit</li>
                </ol>
            </div>
            <h4 class="page-title">Deposit</h4>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-end">
                @can('deposit-create')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addDepositModal">Add Deposit</button>
                @endcan
            </div>
        </div>

        <div class="card-body">
            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deposit as $key => $dep)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $dep->account->name ?? 'N/A' }}</td>
                        <td>{{ number_format($dep->amount, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('deposit.update.status', ['id' => $dep->id, 'status' => $dep->status]) }}" id="statusForm{{ $dep->id }}">
                                @csrf
                                <input type="hidden" name="status" id="statusInput{{ $dep->id }}">
                                <select class="form-select form-select-sm" onchange="document.getElementById('statusInput{{ $dep->id }}').value=this.value; document.getElementById('statusForm{{ $dep->id }}').submit();">
                                    <option value="pending" {{ $dep->status=='pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $dep->status=='approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ $dep->status=='rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </form>
                        </td>
                        <td style="width: 150px;">
                            <div class="d-flex justify-content-end gap-1">
                                @can('deposit-edit')
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editDepositModal{{ $dep->id }}">Edit</button>
                                @endcan
                                @can('deposit-delete')
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteDepositModal{{ $dep->id }}">Delete</button>
                                @endcan
                            </div>
                        </td>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editDepositModal{{ $dep->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Deposit</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('deposit.update', $dep->id) }}" id="editForm{{ $dep->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="account_id" class="form-label">Account</label>
                                                        <select name="account_id" class="form-select" required>
                                                            @foreach($account as $acc)
                                                            <option value="{{ $acc->id }}" {{ $dep->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="amount" class="form-label">Amount</label>
                                                        <input type="number" name="amount" value="{{ $dep->amount }}" step="0.01" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ $dep->status=='pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $dep->status=='approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="rejected" {{ $dep->status=='rejected' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                    </div>
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

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteDepositModal{{ $dep->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-colored-header bg-danger">
                                        <h4 class="modal-title">Delete Deposit</h4>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Do you want to delete this deposit?</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('deposit.destroy',$dep->id) }}" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Deposit Modal -->
<div class="modal fade" id="addDepositModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Deposit</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('deposit.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="account_id" class="form-label">Account</label>
                                <select name="account_id" class="form-select" required>
                                    <option value="">Select Account</option>
                                    @foreach($account as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" name="amount" step="0.01" class="form-control" required>
                            </div>
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

<!-- Inline Status Update Script -->
@push('scripts')
<script>
    function updateStatus(id, value) {
        document.getElementById('statusInput' + id).value = value;
        document.getElementById('statusForm' + id).submit();
    }
</script>
@endpush

@endsection
