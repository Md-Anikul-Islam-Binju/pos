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
                        <li class="breadcrumb-item active">Production Payment</li>
                    </ol>
                </div>
                <h4 class="page-title">Production Payment</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">

                    @can('production-payment-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">Add Production Payment</button>
                    @endcan

                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Production House</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Received By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($payment as $key=>$paymentData)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $paymentData->productionHouse->name }}</td>
                            <td>{{ $paymentData->account->name }}</td>
                            <td>{{ $paymentData->amount }}</td>
                            <td>{{ $paymentData->date }}</td>
                            <td>{{ $paymentData->received_by }}</td>
                            <td>
                                {{ ucfirst($paymentData->status) }}
                                <form method="POST" action="{{ route('production.payment.update.status', $paymentData->id) }}" id="statusForm{{ $paymentData->id }}">
                                    @csrf
                                    <input type="hidden" name="status" id="statusInput{{ $paymentData->id }}">
                                    <select class="form-select form-select-sm"
                                            onchange="document.getElementById('statusInput{{ $paymentData->id }}').value=this.value; document.getElementById('statusForm{{ $paymentData->id }}').submit();">
                                        <option value="pending" {{ $paymentData->status=='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $paymentData->status=='approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $paymentData->status=='rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td style="width: 100px;">
                                <div class="d-flex justify-content-end gap-1">

                                    @can('production-payment-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editNewModalId{{ $paymentData->id }}">Edit</button>
                                    @endcan

                                    @can('production-payment-delete')
                                        <a href="{{ route('production.payment.destroy', $paymentData->id) }}" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#danger-header-modal{{ $paymentData->id }}">Delete</a>
                                    @endcan

                                </div>
                            </td>

                            <!--Edit Modal -->
                            <div class="modal fade" id="editNewModalId{{ $paymentData->id }}" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editNewModalLabel{{ $paymentData->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="addNewModalLabel{{ $paymentData->id }}">Edit</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('production.payment.update', $paymentData->id) }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Production House</label>
                                                        <select name="house_id" class="form-select" required>
                                                            @foreach($house as $h)
                                                                <option value="{{ $h->id }}" {{ $paymentData->house_id == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Account</label>
                                                        <select name="account_id" class="form-select" required>
                                                            @foreach($account as $a)
                                                                <option value="{{ $a->id }}" {{ $paymentData->account_id == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Amount</label>
                                                        <input type="number" name="amount" class="form-control" value="{{ $paymentData->amount }}" required>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Date</label>
                                                        <input type="date" name="date" class="form-control" value="{{ $paymentData->date }}" required>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Received By</label>
                                                        <input type="text" name="received_by" class="form-control" value="{{ $paymentData->received_by }}">
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <label class="form-label">Photo</label>
                                                        <input type="file" name="photo" class="form-control">
                                                        @if($paymentData->image)
                                                            <img src="{{ asset('storage/'.$paymentData->image) }}" alt="Photo" class="mt-2" style="max-height: 80px;">
                                                        @endif
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Details</label>
                                                        <textarea name="details" class="form-control">{{ $paymentData->details }}</textarea>
                                                    </div>
                                                    <div class="col-12 mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ $paymentData->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $paymentData->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="rejected" {{ $paymentData->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    @can('production-payment-edit')
                                                        <button class="btn btn-primary" type="submit">Update</button>
                                                    @endcan
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div id="danger-header-modal{{ $paymentData->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel{{ $paymentData->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header modal-colored-header bg-danger">
                                            <h4 class="modal-title" id="danger-header-modalLabe{{ $paymentData->id }}l">Delete</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="mt-0">Do you want to Delete this ? </h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            @can('production-payment-delete')
                                                <a href="{{ route('production.payment.destroy', $paymentData->id) }}" class="btn btn-danger">Delete</a>
                                            @endcan
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
                    <form method="post" action="{{ route('production.payment.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Production House</label>
                                <select name="house_id" class="form-select" required>
                                    @foreach($house as $h)
                                        <option value="{{ $h->id }}">{{ $h->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Account</label>
                                <select name="account_id" class="form-select" required>
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
                                <input type="text" name="received_by" class="form-control">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Details</label>
                                <textarea name="details" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            @can('production-payment-create')
                                <button class="btn btn-primary" type="submit">Submit</button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
