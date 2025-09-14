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
                        <li class="breadcrumb-item active">Showroom!</li>
                    </ol>
                </div>
                <h4 class="page-title">Showroom!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    <!-- Large modal -->
                    @can('showroom-create')
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
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($showroom as $key=>$showroomData)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$showroomData->name}}</td>
                        <td>{{$showroomData->address}}</td>
                        <td>{{$showroomData->phone}}</td>
                        <td>{{$showroomData->email}}</td>
                        <td>{{$showroomData->status==1? 'Active':'Inactive'}}</td>
                        <td style="width: 100px;">
                            <div class="d-flex justify-content-end gap-1">
                                @can('showroom-edit')
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editNewModalId{{$showroomData->id}}">Edit</button>
                                @endcan
                                @can('showroom-delete')
                                    <a href="{{route('showroom.destroy',$showroomData->id)}}" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#danger-header-modal{{$showroomData->id}}">Delete</a>
                                @endcan
                            </div>
                        </td>
                        <!--Edit Modal -->
                        <div class="modal fade" id="editNewModalId{{$showroomData->id}}" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="editNewModalLabel{{$showroomData->id}}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="addNewModalLabel{{$showroomData->id}}">Edit</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{route('showroom.update',$showroomData->id)}}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" id="name" name="name" value="{{$showroomData->name}}"
                                                               class="form-control" placeholder="Enter Name" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="address" class="form-label">Address</label>
                                                        <input type="text" id="address" name="address" value="{{$showroomData->address}}"
                                                               class="form-control" placeholder="Enter Address" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="tel" id="phone" name="phone" value="{{$showroomData->phone}}"
                                                               class="form-control" placeholder="Enter Phone" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="email" id="email" name="email" value="{{$showroomData->email}}"
                                                               class="form-control" placeholder="Enter Email" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="example-select" class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="1" {{ $showroomData->status === 1 ? 'selected' : '' }}>Active</option>
                                                            <option value="0" {{ $showroomData->status === 0 ? 'selected' : '' }}>Inactive</option>
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
                        <div id="danger-header-modal{{$showroomData->id}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="danger-header-modalLabel{{$showroomData->id}}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-colored-header bg-danger">
                                        <h4 class="modal-title" id="danger-header-modalLabe{{$showroomData->id}}l">Delete</h4>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5 class="mt-0">Do you want to Delete this ? </h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <a href="{{route('showroom.destroy',$showroomData->id)}}" class="btn btn-danger">Delete</a>
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
                    <form method="post" action="{{ route('showroom.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" id="name" name="name"
                                           class="form-control" placeholder="Enter Name" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" id="address" name="address"
                                           class="form-control" placeholder="Enter Address">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" id="phone" name="phone"
                                           class="form-control" placeholder="Enter Phone" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email"
                                           class="form-control" placeholder="Enter Email">
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
