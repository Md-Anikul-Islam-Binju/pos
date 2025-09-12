@extends('admin.app')
@section('admin_content')
    {{-- Account Transfer index (single-file CRUD) --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Accounts</a></li>
                        <li class="breadcrumb-item active">Account Transfer!</li>
                    </ol>
                </div>
                <h4 class="page-title">Account Transfer!</h4>
            </div>
        </div>
    </div>

    {{-- Flash / validation --}}
    <div class="row mb-2">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
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
                        <th>From Account</th>
                        <th>To Account</th>
                        <th>Amount</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th style="width:220px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accountTransfer as $key => $transfer)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $transfer->fromAccount->name ?? 'N/A' }}</td>
                            <td>{{ $transfer->toAccount->name ?? 'N/A' }}</td>
                            <td>{{ number_format($transfer->amount, 2) }}</td>
                            <td>{{ $transfer->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td>
                                @if($transfer->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($transfer->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1 align-items-center">
                                    {{-- Quick status controls --}}
                                    @can('account-transfer-edit')
                                        <form method="POST" action="{{ route('account.transfer.update.status', [$transfer->id, 'approved']) }}" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve" @if($transfer->status === 'approved') disabled @endif>Approve</button>
                                        </form>

                                        <form method="POST" action="{{ route('account.transfer.update.status', [$transfer->id, 'rejected']) }}" class="d-inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-warning" title="Reject" @if($transfer->status === 'rejected') disabled @endif>Reject</button>
                                        </form>
                                    @endcan

                                    {{-- Edit --}}
                                    @can('account-transfer-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editNewModalId{{ $transfer->id }}">Edit</button>
                                    @endcan

                                    {{-- Delete --}}
                                    @can('account-transfer-delete')
                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#danger-header-modal{{ $transfer->id }}">Delete</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal (per transfer) --}}
                        <div class="modal fade" id="editNewModalId{{ $transfer->id }}" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editNewModalLabel{{ $transfer->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Transfer #{{ $transfer->id }}</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <form method="post" action="{{ route('account.transfer.update', $transfer->id) }}" class="edit-transfer-form">
                                            @csrf
                                            @method('PUT')

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">From Account</label>
                                                        <select name="from_account_id" class="form-select" required>
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ $account->id == $transfer->from_account_id ? 'selected' : '' }}>
                                                                    {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('from_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">To Account</label>
                                                        <select name="to_account_id" class="form-select" required>
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ $account->id == $transfer->to_account_id ? 'selected' : '' }}>
                                                                    {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('to_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" step="0.01" name="amount" value="{{ old('amount', $transfer->amount) }}" class="form-control" required>
                                                        @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ $transfer->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $transfer->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="rejected" {{ $transfer->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
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

                        {{-- Delete Modal --}}
                        <div id="danger-header-modal{{ $transfer->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel{{ $transfer->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-colored-header bg-danger">
                                        <h4 class="modal-title">Delete Transfer #{{ $transfer->id }}</h4>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="mt-0">Do you want to delete this transfer?</h5>
                                        <p class="mb-0"><strong>From:</strong> {{ $transfer->fromAccount->name ?? 'N/A' }} &nbsp; <strong>To:</strong> {{ $transfer->toAccount->name ?? 'N/A' }}</p>
                                        <p class="mb-0"><strong>Amount:</strong> {{ number_format($transfer->amount,2) }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>

                                        <form method="POST" action="{{ route('account.transfer.destroy', $transfer->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>

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

    {{-- Add Modal --}}
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Transfer</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="post" action="{{ route('account.transfer.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">From Account</label>
                                    <select id="addFromAccount" name="from_account_id" class="form-select" required>
                                        <option value="">-- Select account --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('from_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">To Account</label>
                                    <select id="addToAccount" name="to_account_id" class="form-select" required>
                                        <option value="">-- Select account --</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('to_account_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="form-control" placeholder="Enter Amount" required>
                                    @error('amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button id="addSubmitBtn" class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- Small JS: prevent selecting same account for from/to in add & edit forms --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function preventSame(selectFrom, selectTo, submitBtn) {
                function check() {
                    // remove existing helper if present
                    const existing = selectTo.parentNode.querySelector('.same-account-error');
                    if (existing) existing.remove();

                    if (selectFrom.value && selectTo.value && selectFrom.value === selectTo.value) {
                        submitBtn.disabled = true;
                        const div = document.createElement('div');
                        div.className = 'same-account-error text-danger small mt-1';
                        div.innerText = 'From and To account cannot be the same.';
                        selectTo.parentNode.appendChild(div);
                    } else {
                        submitBtn.disabled = false;
                    }
                }

                selectFrom.addEventListener('change', check);
                selectTo.addEventListener('change', check);
                check();
            }

            // Add form
            const addFrom = document.querySelector('#addFromAccount');
            const addTo = document.querySelector('#addToAccount');
            const addSubmit = document.querySelector('#addSubmitBtn');
            if (addFrom && addTo && addSubmit) preventSame(addFrom, addTo, addSubmit);

            // Edit forms
            document.querySelectorAll('.edit-transfer-form').forEach(function (form) {
                const from = form.querySelector('[name="from_account_id"]');
                const to = form.querySelector('[name="to_account_id"]');
                const submit = form.querySelector('button[type="submit"]');
                if (from && to && submit) preventSame(from, to, submit);
            });
        });
    </script>
@endsection
