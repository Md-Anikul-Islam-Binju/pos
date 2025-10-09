@extends('admin.app')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Raw Materials</a></li>
                    <li class="breadcrumb-item active">Manage</li>
                </ol>
            </div>
            <h4 class="page-title">Raw Materials</h4>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-end">
                @can('raw-material-create')
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">Add New</button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>SKU</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $key => $material)
                    <tr>
                        <td>
                            @if($material->image)
                            <img src="{{ getAssetUrl($material->image,$material->name) }}" class="rounded" width="100" alt="{{ $material->name }}">
                            @endif
                        </td>
                        <td>{{ $material->name }}</td>
                        <td>{{ $material->category->name ?? '' }}</td>
                        <td>{{ $material->unit->name ?? '' }}</td>
                        <td>{{ $material->sku }}</td>
                        <td style="width: 150px;">
                            <div class="d-flex justify-content-end gap-1">
                                @can('raw-material-view')
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#viewModalId{{ $material->id }}">View</button>
                                @endcan
                                @can('raw-material-edit')
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editModalId{{ $material->id }}">Edit</button>
                                @endcan
                                @can('raw-material-delete')
                                <form action="{{ route('raw.material.destroy', $material->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                                @endcan
                            </div>
                        </td>

                        <!-- View Modal -->
                        <div class="modal fade" id="viewModalId{{ $material->id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="viewModalLabel{{ $material->id }}">View Material - {{ $material->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-bordered w-100">
                                            <tr>
                                                <th>Name</th>
                                                <td>{{ $material->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>Category</th>
                                                <td>{{ $material->category->name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Unit</th>
                                                <td>{{ $material->unit->name ?? '' }}</td>
                                            </tr>
                                            <tr>
                                                <th>SKU</th>
                                                <td>{{ $material->sku }}</td>
                                            </tr>
                                            <tr>
                                                <th>Width</th>
                                                <td>{{ $material->width }}</td>
                                            </tr>
                                            <tr>
                                                <th>Length</th>
                                                <td>{{ $material->length }}</td>
                                            </tr>
                                            <tr>
                                                <th>Density</th>
                                                <td>{{ $material->density }}</td>
                                            </tr>
                                            <tr>
                                                <th>Brands</th>
                                                <td>{{ implode(', ', $material->brands->pluck('name')->toArray()) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sizes</th>
                                                <td>{{ implode(', ', $material->sizes->pluck('name')->toArray()) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Colors</th>
                                                <td>{{ implode(', ', $material->colors->pluck('color_name')->toArray()) }}</td>
                                            </tr>
                                            <tr>
                                                <th>Image</th>
                                                <td>@if($material->image)<img src="{{ getAssetUrl($material->image,$material->name) }}" width="150" class="rounded">@endif</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModalId{{ $material->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Material - {{ $material->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('raw.material.update',$material->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="name{{ $material->id }}" class="form-label">Name</label>
                                                        <input type="text" id="name{{ $material->id }}" name="name" value="{{ $material->name }}" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="sku{{ $material->id }}" class="form-label">SKU</label>
                                                        <input type="text" id="sku{{ $material->id }}" name="sku" value="{{ $material->sku }}" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="category{{ $material->id }}" class="form-label">Category</label>
                                                        <select id="category{{ $material->id }}" name="raw_material_category_id" class="form-select" required>
                                                            @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}" {{ $material->raw_material_category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="unit{{ $material->id }}" class="form-label">Unit</label>
                                                        <select id="unit{{ $material->id }}" name="unit_id" class="form-select">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($units as $unit)
                                                            <option value="{{ $unit->id }}" {{ $material->unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label for="photo{{ $material->id }}" class="form-label">Image</label>
                                                        <input type="file" name="photo" id="photo{{ $material->id }}" class="form-control">
                                                        @if($material->image)
                                                        <img src="{{ getAssetUrl($material->image,$material->name) }}" class="mt-2" width="150">
                                                        @endif
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
                        <div id="deleteModalId{{ $material->id }}" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel{{ $material->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Delete</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Are you sure you want to delete this material?</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('raw.material.destroy',$material->id) }}" class="btn btn-danger">Delete</a>
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
<div class="modal fade" id="addNewModalId" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Raw Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('raw.material.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" id="sku" name="sku" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select id="category" name="raw_material_category_id" class="form-select" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="unit" class="form-label">Unit</label>
                                <select id="unit" name="unit_id" class="form-select">
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="photo" class="form-label">Image</label>
                                <input type="file" name="photo" id="photo" class="form-control">
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
