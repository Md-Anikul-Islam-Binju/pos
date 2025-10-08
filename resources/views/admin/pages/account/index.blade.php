@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Finance</a></li>
                        <li class="breadcrumb-item active">Accounts</li>
                    </ol>
                </div>
                <h4 class="page-title">Accounts</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="d-flex justify-content-end">

                    @can('account-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">Add Account</button>
                    @endcan

                </div>
            </div>

            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Account Name</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($account as $key => $accountData)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $accountData->name }}</td>
                                <td>{{ $accountData->type }}</td>
                                <td>{{ $accountData->admin->name }}</td>
                                <td>{{ number_format($accountData->balance, 2) }}</td>
                                <td>{{ ucfirst($accountData->status) }}</td>
                                <td style="width: 150px;">
                                    <div class="d-flex justify-content-start gap-1">

                                        @can('account-edit')
                                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editNewModalId{{ $accountData->id }}">Edit</button>
                                        @endcan

                                        @can('account-view')
                                            <a href="{{ route('account.show', $accountData->id) }}" class="btn btn-primary btn-sm">View</a>
                                        @endcan

                                        @can('account-delete')
                                            <a href="{{ route('account.destroy', $accountData->id) }}" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#danger-header-modal{{ $accountData->id }}">Delete</a>
                                        @endcan

                                    </div>
                                </td>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editNewModalId{{ $accountData->id }}" data-bs-backdrop="static" tabindex="-1" role="dialog"
                                     aria-labelledby="editNewModalLabel{{ $accountData->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h4 class="modal-title">Edit Account</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">

                                                <form method="post" action="{{ route('account.update', $accountData->id) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="name" class="form-label">Account Name</label>
                                                                <input type="text" name="name" value="{{ $accountData->name }}" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="admin_id" id="admin_id" class="form-label">Select Account Admin</label>
                                                                <select name="admin_id" id="admin_id" class="form-control" required>
                                                                    @foreach($admins as $admin)
                                                                        <option value="{{ $admin->id }}" {{ $accountData->admin_id == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="type" class="form-label">Select Account Type</label>
                                                                <select name="type" id="type" class="form-control" required>
                                                                    <option value="cash" {{ $accountData->type == 'cash' ? 'selected' : '' }}>Cash</option>
                                                                    <option value="bank" {{ $accountData->type == 'bank' ? 'selected' : '' }}>Bank</option>
                                                                    <option value="mobile" {{ $accountData->type == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                                                    <option value="other" {{ $accountData->type == 'other' ? 'selected' : '' }}>Others</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="mb-3">
                                                                <label for="edit_status" id="edit_status" class="form-label">Select Account Status</label>
                                                                <select name="status" id="edit_status" class="form-control" required>
                                                                    <option value="active" {{ $accountData->status == 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="deactivate" {{ $accountData->status == 'deactivate' ? 'selected' : '' }}>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @can('account-edit')
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
                                <div id="danger-header-modal{{ $accountData->id }}" class="modal fade" tabindex="-1" role="dialog"
                                     aria-labelledby="danger-header-modalLabel{{ $accountData->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header modal-colored-header bg-danger">
                                                <h4 class="modal-title">Delete Account</h4>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>

                                            <div class="modal-body">
                                                <h5 class="mt-0">Do you want to delete this account?</h5>
                                            </div>

                                            @can('account-delete')
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <a href="{{ route('account.destroy', $accountData->id) }}" class="btn btn-danger">Delete</a>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Add</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <form method="post" action="{{ route('account.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Account Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter account Name" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Select Account Admin</label>
                                    <select name="admin_id" id="admin_id" class="form-control" required>
                                        @foreach($admins as $admin)
                                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Select Account Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank</option>
                                        <option value="mobile">Mobile</option>
                                        <option value="other">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Select Account Status</label>
                                    <select name="status" id="add_status" class="form-control" required>
                                        <option value="active">Active</option>
                                        <option value="deactivate">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        @can('account-create')
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
