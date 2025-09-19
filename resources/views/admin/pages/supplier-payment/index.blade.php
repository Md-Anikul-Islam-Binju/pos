@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">POS System</a></li>
                        <li class="breadcrumb-item active">Supplier Payments!</li>
                    </ol>
                </div>
                <h4 class="page-title">Supplier Payments!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('supplier-payment-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addPaymentModal">Add Payment</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Supplier</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Received By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payment as $key => $pay)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $pay->supplier?->name ?? 'N/A' }}</td>
                            <td>{{ $pay->account?->name ?? 'N/A' }}</td>
                            <td>{{ number_format($pay->amount, 2) }}</td>
                            <td>{{ $pay->date }}</td>
                            <td>{{ $pay->received_by }}</td>
                            <td>
                                <form action="{{ route('supplier.payment.update.status', [$pay->id, $pay->status]) }}" method="POST">
                                    @csrf
                                    <select class="form-select form-select-sm"
                                            name="status"
                                            onchange="this.form.submit()">
                                        <option value="pending" {{ $pay->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $pay->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $pay->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td style="width: 120px;">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('supplier-payment-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $pay->id }}">Edit</button>
                                    @endcan
                                    @can('supplier-payment-delete')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePaymentModal{{ $pay->id }}">Delete</button>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editPaymentModal{{ $pay->id }}" tabindex="-1" aria-labelledby="editPaymentLabel{{ $pay->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="editPaymentLabel{{ $pay->id }}">Edit Payment</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('supplier.payment.update', $pay->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">supplier</label>
                                                    <select name="supplier_id" class="form-select" required>
                                                        <option value="">Select supplier</option>
                                                        @foreach($supplier as $c)
                                                            <option value="{{ $c->id }}" {{ $c->id == $pay->supplier_id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Account</label>
                                                    <select name="account_id" class="form-select" required>
                                                        <option value="">Select Account</option>
                                                        @foreach($account as $a)
                                                            <option value="{{ $a->id }}" {{ $a->id == $pay->account_id ? 'selected' : '' }}>{{ $a->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Amount</label>
                                                    <input type="number" name="amount" class="form-control" value="{{ $pay->amount }}" required>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input type="date" name="date" class="form-control" value="{{ $pay->date }}" required>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Received By</label>
                                                    <input type="text" name="received_by" class="form-control" value="{{ $pay->received_by }}" required>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="pending" {{ $pay->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="approved" {{ $pay->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="rejected" {{ $pay->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary">Update Payment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deletePaymentModal{{ $pay->id }}" tabindex="-1" aria-labelledby="deletePaymentLabel{{ $pay->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deletePaymentLabel{{ $pay->id }}">Delete Payment</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this payment?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <a href="{{ route('supplier.payment.destroy', $pay->id) }}" class="btn btn-danger">Delete</a>
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

    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addPaymentLabel">Add Payment</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('supplier.payment.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select supplier</option>
                                    @foreach($supplier as $c)
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
                                <label class="form-label">Received By</label>
                                <input type="text" name="received_by" class="form-control" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Add Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
