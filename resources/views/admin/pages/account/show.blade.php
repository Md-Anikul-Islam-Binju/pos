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
                        <li class="breadcrumb-item active">Account</li>
                    </ol>
                </div>
                <h4 class="page-title">View Account</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="d-flex justify-content-end gap-2">
                    <!-- Large modal -->
                    @can('brand-create')
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#depositModal">Deposit</button>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#withdrawModal">Withdraw</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer</button>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Account Name</label>
                                        <input type="text" name="name" value="{{ $account->name }}" class="form-control" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="admin_id" id="admin_id" class="form-label">Select Account Admin</label>
                                        <select name="admin_id" id="admin_id" class="form-control" disabled>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}" {{ $account->admin_id == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Select Account Type</label>
                                        <select name="type" id="type" class="form-control" disabled>
                                            <option value="cash" {{ $account->type == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="bank" {{ $account->type == 'bank' ? 'selected' : '' }}>Bank</option>
                                            <option value="mobile" {{ $account->type == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                            <option value="other" {{ $account->type == 'other' ? 'selected' : '' }}>Others</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="edit_status" id="edit_status" class="form-label">Select Account Status</label>
                                        <select name="status" id="edit_status" class="form-control" disabled>
                                            <option value="active" {{ $account->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="deactivate" {{ $account->status == 'deactivate' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table id="basic-datatable" class="table table-bordered table-sm text-center">
                            <thead>
                            <tr>
                                <th>Trx. ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Reference</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactionInfo as $info)
                                <tr>
                                    <td class="text-left">{{ $info->transaction_id }}</td>
                                    <td>
                                        @if($info->transaction_type == 'in')
                                            <span class="badge badge-success px-3">{{ $info->transaction_type }}</span>
                                        @else
                                            <span class="badge badge-danger px-3">{{ $info->transaction_type }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ number_format($info->amount )}}</td>
                                    <td class="text-left"><small>{{ $info->reference }}</small></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Deposit Modal -->
    <div class="modal fade" id="depositModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Deposit</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form method="post" action="{{ route('deposit.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="account_id" value="{{ $account->id }}">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" id="amount" name="amount" class="form-control" placeholder="Enter Amount" min="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="details" class="form-label">Notes</label>
                                    <textarea name="notes" id="" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="photo">Select photo</label>
                                    <input name="old_photo" value="" class="d-none">
                                    <input name="photo" type="file" class="form-control" id="photo">
                                </div>
                                <div class="form-group mb-2">
                                    <img src="" alt="Selected Image" id="selected-image">
                                </div>
                            </div>
                        </div>

                        @can('deposit.create')
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        @endcan
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Withdraw</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form method="post" action="{{ route('withdraw.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="account_id" value="{{ $account->id }}">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" id="amount" name="amount" class="form-control"
                                           placeholder="Enter Amount" max="{{ $account->balance }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="details" class="form-label">Notes</label>
                                    <textarea name="notes" id="" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="photo">Select photo</label>
                                    <input name="old_photo" value="" class="d-none">
                                    <input name="photo" type="file" class="form-control" id="photo">
                                </div>
                                <div class="form-group mb-2">
                                    <img src="" alt="Selected Image" id="selected-image">
                                </div>
                            </div>
                        </div>

                        @can('withdraw.create')
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        @endcan
                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Transfer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <small class="ml-3">Account Balance: <span class="text-danger">{{$account->balance}}</span> /-</small>

                <div class="modal-body">

                    <form method="post" action="{{ route('account-transfer.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="from_account_id" value="{{ $account->id }}">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="to_account_id" class="form-label">Select Account</label>
                                    <select name="to_account_id" id="to_account_id" class="form-control">
                                        @if(!empty($accounts))
                                            @foreach($accounts as $select_account)
                                                @if($select_account->id != $account->id)
                                                    <option value="{{ $select_account->id }}">{{ $select_account->name }}</option>
                                                @endif
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
                                    <input id="amount" name="amount" type="number" class="form-control" placeholder="Enter amount" max="{{ $account->balance }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="photo">Select photo</label>
                                    <input name="old_photo" value="" class="d-none">
                                    <input name="photo" type="file" class="form-control" id="photo">
                                </div>
                                <div class="form-group mb-2">
                                    <img src="" alt="Selected Image" id="selected-image">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="details" class="form-label">Notes</label>
                                    <textarea name="notes" id="" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>

                        @can('account_transfer.create')
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
