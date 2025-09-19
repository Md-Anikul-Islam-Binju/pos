@extends('admin.app')
@section('admin_content')
    {{-- CKEditor CDN --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                        <li class="breadcrumb-item active">Account Transfer!</li>
                    </ol>
                </div>
                <h4 class="page-title">Account Transfer!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    <!-- Large modal -->
                    @can('account-transfer-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">Add New</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Debited Account</th>
                        <th>Credited Account</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accountTransfer as $key => $accountTransferData)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $accountTransferData->fromAccount->name }}</td>
                            <td>{{ $accountTransferData->toAccount->name }}</td>
                            <td>{{ $accountTransferData->amount }}</td>
                            <td>
                                <form method="POST" action="{{ route('account.transfer.update.status', $accountTransferData->id) }}" id="statusForm{{ $accountTransferData->id }}">
                                    @csrf
                                    <input type="hidden" name="status" id="statusInput{{ $accountTransferData->id }}">
                                    <select class="form-select form-select-sm"
                                            onchange="document.getElementById('statusInput{{ $accountTransferData->id }}').value=this.value; document.getElementById('statusForm{{ $accountTransferData->id }}').submit();">
                                        <option value="pending" {{ $accountTransferData->status=='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $accountTransferData->status=='approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $accountTransferData->status=='rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td style="width: 100px;">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('account-transfer-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editNewModalId{{ $accountTransferData->id }}">Edit</button>
                                    @endcan
                                    @can('account-transfer-delete')
                                        <a href="{{ route('account.transfer.destroy', $accountTransferData->id)}}" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#danger-header-modal{{ $accountTransferData->id }}">Delete</a>
                                    @endcan
                                </div>
                            </td>

                            <!--Edit Modal -->
                            <div class="modal fade" id="editNewModalId{{ $accountTransferData->id}}" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editNewModalLabel{{ $accountTransferData->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="addNewModalLabel{{ $accountTransferData->id }}">Edit</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('account.transfer.update',$accountTransferData->id) }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="from_account_id" class="form-label">Select Debit Account</label>
                                                            <select name="from_account_id" class="form-select" required>
                                                                @foreach($account as $acc)
                                                                    <option value="{{ $acc->id }}" {{ $accountTransferData->from_account_id == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="to_account_id" class="form-label">Select Credit Account</label>
                                                            <select name="to_account_id" class="form-select" required>
                                                                @foreach($account as $acc)
                                                                    <option value="{{ $acc->id }}" {{ $accountTransferData->to_account_id == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="amount" class="form-label">Amount</label>
                                                            <input type="number" name="amount" value="{{ $accountTransferData->amount }}" step="0.01" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="pending" {{ $accountTransferData->status=='pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="approved" {{ $accountTransferData->status=='approved' ? 'selected' : '' }}>Approved</option>
                                                                <option value="rejected" {{ $accountTransferData->status=='rejected' ? 'selected' : '' }}>Rejected</option>
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
                            <div id="danger-header-modal{{$accountTransferData->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel{{ $accountTransferData->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header modal-colored-header bg-danger">
                                            <h4 class="modal-title" id="danger-header-modalLabe{{ $accountTransferData->id }}l">Delete</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="mt-0">Do you want to Delete this ? </h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <a href="{{route('account.transfer.destroy', $accountTransferData->id) }}" class="btn btn-danger">Delete</a>
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

    <!--Add Modal -->
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Add</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{route('account.transfer.store')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="from_account_id" class="form-label">Account</label>
                                    <select name="from_account_id" class="form-select" required>
                                        <option value="">Select Debit Account</option>
                                        @foreach($account as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="to_account_id" class="form-label">Account</label>
                                    <select name="to_account_id" class="form-select" required>
                                        <option value="">Select Account</option>
                                        @foreach($account as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
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
@endsection
