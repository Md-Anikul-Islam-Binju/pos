@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                        <li class="breadcrumb-item active">Payment Method</li>
                    </ol>
                </div>
                <h4 class="page-title">Payment Method</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('payment-method-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">Add New</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($paymentMethod as $key => $method)
                        <tr>
                            <td>{{$key + 1}}</td>
                            <td>{{$method->name}}</td>
                            <td>{{$method->status == 1 ? 'Active' : 'Inactive'}}</td>
                            <td style="width: 120px;">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('payment-method-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{$method->id}}">Edit</button>
                                    @endcan
                                    @can('payment-method-delete')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#danger-header-modal{{$method->id}}">Delete</button>
                                    @endcan
                                </div>
                            </td>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{$method->id}}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="editModalLabel{{$method->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="editModalLabel{{$method->id}}">Edit Payment Method</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('payment.method.update', $method->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Name</label>
                                                            <input type="text" name="name" value="{{ $method->name }}" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Status</label>
                                                            <select name="status" class="form-select">
                                                                <option value="1" {{ $method->status == 1 ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ $method->status == 0 ? 'selected' : '' }}>Inactive</option>
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
                            <div id="danger-header-modal{{ $method->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel{{ $method->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header modal-colored-header bg-danger">
                                            <h4 class="modal-title" id="danger-header-modalLabe{{ $method->id }}l">Delete</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="mt-0">Do you want to Delete this ? </h5>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                            <a href="{{ route('payment.method.destroy',$method->id) }}" class="btn btn-danger">Delete</a>
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
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Add Payment Method</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('payment.method.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Name" required>
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
