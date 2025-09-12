@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item active">Expense</li>
                    </ol>
                </div>
                <h4 class="page-title">Expense</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('expense-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addExpenseModal">Add New Expense</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($expense as $key => $exp)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $exp->title }}</td>
                            <td>{{ $exp->category?->name ?? '-' }}</td>
                            <td>{{ $exp->account?->name ?? '-' }}</td>
                            <td>{{ number_format($exp->amount,2) }}</td>
                            <td>
                                <span class="badge {{ $exp->status=='approved'?'bg-success':($exp->status=='pending'?'bg-warning':'bg-danger') }}">
                                    {{ ucfirst($exp->status) }}
                                </span>
                            </td>
                            <td style="width: 150px;">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('expense-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{$exp->id}}">Edit</button>
                                    @endcan
                                    @can('expense-delete')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteExpenseModal{{$exp->id}}">Delete</button>
                                    @endcan
                                </div>
                            </td>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editExpenseModal{{$exp->id}}" tabindex="-1" aria-labelledby="editExpenseModalLabel{{$exp->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Expense</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="{{ route('expense.update', $exp->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" name="title" value="{{ $exp->title }}" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Category</label>
                                                            <select name="category_id" class="form-select" required>
                                                                <option value="">Select Category</option>
                                                                @foreach($expenseCategory as $cat)
                                                                    <option value="{{ $cat->id }}" {{ $cat->id==$exp->category_id?'selected':'' }}>{{ $cat->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Account</label>
                                                            <select name="account_id" class="form-select" required>
                                                                <option value="">Select Account</option>
                                                                @foreach($account as $acc)
                                                                    <option value="{{ $acc->id }}" {{ $acc->id==$exp->account_id?'selected':'' }}>{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Amount</label>
                                                            <input type="number" step="0.01" name="amount" value="{{ $exp->amount }}" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="pending" {{ $exp->status=='pending'?'selected':'' }}>Pending</option>
                                                                <option value="approved" {{ $exp->status=='approved'?'selected':'' }}>Approved</option>
                                                                <option value="rejected" {{ $exp->status=='rejected'?'selected':'' }}>Rejected</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteExpenseModal{{$exp->id}}" tabindex="-1" aria-labelledby="deleteExpenseModalLabel{{$exp->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h4 class="modal-title">Delete Expense</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Are you sure you want to delete this expense?</h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <a href="{{ route('expense.destroy', $exp->id) }}" class="btn btn-danger">Delete</a>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Expense</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('expense.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Category</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($expenseCategory as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Account</label>
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
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
