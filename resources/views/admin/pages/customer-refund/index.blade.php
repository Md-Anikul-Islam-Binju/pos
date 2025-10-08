@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">POS System</a></li>
                        <li class="breadcrumb-item active">Customer Refunds</li>
                    </ol>
                </div>
                <h4 class="page-title">Customer Refunds</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('customer-refund-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addRefundModal">Add Customer Refund</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Customer</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Refund By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($refund as $key => $r)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $r->customer?->name ?? 'N/A' }}</td>
                            <td>{{ $r->account?->name ?? 'N/A' }}</td>
                            <td>{{ number_format($r->amount, 2) }}</td>
                            <td>{{ $r->date }}</td>
                            <td>{{ $r->refund_by }}</td>
                            <td>
                                <form action="{{ route('customer.refund.update.status', [$r->id, '']) }}" method="GET">
                                    <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                                        <option value="pending" {{ $r->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $r->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $r->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td style="width: 120px;">
                                <div class="d-flex justify-content-end gap-1">

                                    @can('customer-refund-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editRefundModal{{ $r->id }}">Edit</button>
                                    @endcan

                                    @can('customer-refund-delete')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteRefundModal{{ $r->id }}">Delete</button>
                                    @endcan

                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editRefundModal{{ $r->id }}" tabindex="-1" aria-labelledby="editRefundLabel{{ $r->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="editRefundLabel{{ $r->id }}">Edit Refund</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('customer.refund.update', $r->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Customer</label>
                                                    <select name="customer_id" class="form-select" required>
                                                        <option value="">Select Customer</option>
                                                        @foreach($customer as $c)
                                                            <option value="{{ $c->id }}" {{ $c->id == $r->customer_id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Account</label>
                                                    <select name="account_id" class="form-select" required>
                                                        <option value="">Select Account</option>
                                                        @foreach($account as $a)
                                                            <option value="{{ $a->id }}" {{ $a->id == $r->account_id ? 'selected' : '' }}>{{ $a->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Amount</label>
                                                    <input type="number" name="amount" class="form-control" value="{{ $r->amount }}" required>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input type="date" name="date" class="form-control" value="{{ $r->date }}" required>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Refund By</label>
                                                    <input type="text" name="refund_by" class="form-control" value="{{ $r->refund_by }}">
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="pending" {{ $r->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="approved" {{ $r->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="rejected" {{ $r->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Details</label>
                                                    <textarea name="details" class="form-control">{{ $r->details }}</textarea>
                                                </div>

                                                <div class="col-6 mb-3">
                                                    <div class="form-group">
                                                        <label for="photo">Change photo</label>
                                                        <input name="old_photo" value="{{ $r->image }}" class="d-none">
                                                        <input name="photo" type="file" class="form-control" id="photo">
                                                    </div>
                                                    @if($r->image)
                                                        <div class="form-group mb-2">
                                                            <img src="{{ asset($r->image) }}" alt="Current Image" width="100">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                @can('customer-refund-edit')
                                                    <button type="submit" class="btn btn-primary">Update Refund</button>
                                                @endcan
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteRefundModal{{ $r->id }}" tabindex="-1" aria-labelledby="deleteRefundLabel{{ $r->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteRefundLabel{{ $r->id }}">Delete Refund</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this refund?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        @can('customer-refund-delete')
                                            <a href="{{ route('customer.refund.destroy', $r->id) }}" class="btn btn-danger">Delete</a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Refund Modal -->
    <div class="modal fade" id="addRefundModal" tabindex="-1" aria-labelledby="addRefundLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addRefundLabel">Add Refund</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('customer.refund.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Customer</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customer as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Account</label>
                                <select name="account_id" class="form-select" required>
                                    <option value="">Select Account</option>
                                    @foreach($account as $a)
                                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Amount</label>
                                <input type="number" name="amount" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Refund By</label>
                                <input type="text" name="refund_by" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Details</label>
                                <textarea name="details" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-6 mb-3">
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
                        <div class="d-flex justify-content-end">
                            @can('customer-refund-create')
                                <button type="submit" class="btn btn-primary">Add Refund</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
