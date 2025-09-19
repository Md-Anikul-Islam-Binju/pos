@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Finance</a></li>
                        <li class="breadcrumb-item active">Withdraw</li>
                    </ol>
                </div>
                <h4 class="page-title">Withdraw</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">

                    @can('withdraw-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addWithdrawModal">Add Withdraw</button>
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
                    @foreach($withdraw as $key => $withdrawData)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $withdrawData->account->name ?? 'N/A' }}</td>
                            <td>{{ number_format($withdrawData->amount, 2) }}</td>
                            <td>
                            <span class="px-2 py-1 badge {{ $withdrawData->status == 'approved' ? 'bg-success' : ($withdrawData->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($withdrawData->status) }}
                            </span>
                            </td>
                            <td style="width: 150px;">
                                <div class="d-flex justify-content-end gap-1">

                                    @can('withdraw-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editWithdrawModal{{ $withdrawData->id }}">Edit</button>
                                    @endcan

                                    @can('withdraw-delete')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteWithdrawModal{{ $withdrawData->id }}">Delete</button>
                                    @endcan

                                </div>
                            </td>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editWithdrawModal{{ $withdrawData->id }}" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Withdraw</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form method="POST" action="{{ route('withdraw.update', $withdrawData->id) }}" id="editForm{{ $withdrawData->id }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="account_id" class="form-label">Account</label>
                                                            <select name="account_id" class="form-select" required>
                                                                @if(!empty($account))
                                                                    @foreach($account as $accountData)
                                                                        <option value="{{ $accountData->id }}" {{ $withdrawData->account_id == $accountData->id ? 'selected' : '' }}>
                                                                            {{ $accountData->name }}
                                                                        </option>
                                                                    @endforeach
                                                                @else
                                                                    <option>No account available</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="amount" class="form-label">Amount</label>
                                                            <input type="number" name="amount" value="{{ $withdrawData->amount }}" step="0.01" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="notes" class="form-label">Notes</label>
                                                            <textarea name="notes" id="notes" class="form-control">{{ $withdrawData->notes }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="photo">Select photo</label>
                                                                <input name="old_photo" value="{{ asset($withdrawData->image) ?? '' }}" class="d-none">
                                                                <input name="photo" type="file" class="form-control" id="photo">
                                                            </div>
                                                            @if($withdrawData->image)
                                                                <div class="form-group mb-2">
                                                                    <img src="{{ asset($withdrawData->image) }}" alt="Selected Image" id="selected-image">
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @can('withdraw-update-status')
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label for="status" class="form-label">Status</label>
                                                                <select name="status" id="edit_status" class="form-select">
                                                                    <option value="pending" {{ $withdrawData->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                    <option value="approved" {{ $withdrawData->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                                    <option value="rejected" {{ $withdrawData->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcan

                                                @can('withdraw-edit')
                                                    <div class="d-flex justify-content-end">
                                                        <button class="btn btn-primary" type="submit">Update</button>
                                                    </div>
                                                @endcan
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteWithdrawModal{{ $withdrawData->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header modal-colored-header bg-danger">
                                            <h4 class="modal-title">Delete Withdraw</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <h5>Do you want to delete this withdraw?</h5>
                                        </div>

                                        @can('withdraw-delete')
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <a href="{{ route('withdraw.destroy', $withdrawData->id) }}" class="btn btn-danger">Delete</a>
                                            </div>
                                        @endcan

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

    <!-- Add Withdraw Modal -->
    <div class="modal fade" id="addWithdrawModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Add Withdraw</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('withdraw.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">Account</label>
                                    <select name="account_id" id="account_id" class="form-select" required>
                                        <option value="">Select Account</option>
                                        @foreach($account as $accountData)
                                            <option value="{{ $accountData->id }}">{{ $accountData->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" name="amount" id="amount" step="0.01" class="form-control" placeholder="Enter amount" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <div class="form-group">
                                        <label for="photo">Select photo</label>
                                        <input name="old_photo" value="" class="d-none">
                                        <input name="photo" type="file" class="form-control" id="photo">
                                    </div>
                                    <div class="form-group mb-2">
                                        <img src="" alt="Selected Image" id="selected-image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @can('withdraw-create')
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        @endcan

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
