@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item active">Asset</li>
                    </ol>
                </div>
                <h4 class="page-title">Asset</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">

                    @can('asset-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addAssetModal">Add New Asset</button>
                    @endcan

                </div>
            </div>

            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Account</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($asset as $key => $assetData)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $assetData->name }}</td>
                            <td>{{ $assetData->category?->name ?? '-' }}</td>
                            <td>{{ $assetData->account?->name ?? '-' }}</td>
                            <td>{{ number_format($assetData->amount,2) }}</td>
                            <td>
                                <span class="px-2 py-1 badge {{ $assetData->status == 'approved' ? 'bg-success' : ($assetData->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ ucfirst($assetData->status) }}
                                </span>
                            </td>
                            <td style="width: 150px;">
                                <div class="d-flex justify-content-end gap-1">

                                    @can('asset-edit')
                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editAssetModal{{ $assetData->id }}">Edit</button>
                                    @endcan

                                    @can('asset-delete')
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAssetModal{{ $assetData->id }}">Delete</button>
                                    @endcan

                                </div>
                            </td>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editAssetModal{{ $assetData->id }}" tabindex="-1"
                                 aria-labelledby="editAssetModalLabel{{ $assetData->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h4 class="modal-title">Edit Asset Information</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form method="POST" action="{{ route('asset.update', $assetData->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Asset Name</label>
                                                            <input type="text" name="name" id="name" value="{{ $assetData->name ?? '' }}"
                                                                   class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="category_id" class="form-label">Select Category</label>
                                                            <select name="category_id" id="category_id" class="form-select" required>
                                                                <option value="">Select Category</option>
                                                                @if(!empty($categories))
                                                                    @foreach($categories as $category)
                                                                        <option value="{{ $category->id }}" {{ $assetData->asset_category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                @else
                                                                    <option>No category available</option>
                                                                @endif
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="account_id" class="form-label">Select Account</label>
                                                            <select name="account_id" id="account_id" class="form-select" required>
                                                                @if(!empty($accounts))
                                                                    @foreach($accounts as $account)
                                                                        <option value="{{ $account->id }}" {{ $assetData->account_id == $account->id ? 'selected' : '' }}
                                                                            {{ $account->status == 'deactivate' ? 'disabled' : '' }}>
                                                                            {{ $account->name }}
                                                                        </option>
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
                                                            <input type="number" step="0.01" name="amount" value="{{ $assetData->amount }}" id="amount" class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <label for="details" class="form-label">Details</label>
                                                            <textarea name="details" id="" class="form-control">{{ $assetData->details ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="mb-3">
                                                            <div class="form-group">
                                                                <label for="photo">Select photo</label>
                                                                <input name="old_photo" value="{{ asset($assetData->images) ?? '' }}" class="d-none">
                                                                <input name="photo" type="file" class="form-control" id="photo">
                                                            </div>
                                                            <div class="form-group mb-2">
                                                                <img src="{{ asset($assetData->images) }}" alt="Selected Image" id="selected-image">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                @can('asset-update-status')
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label for="status">Select Status</label>
                                                                <select id="edit_status" name="status" class="select2 form-control" required>
                                                                    <option value="pending" {{ $assetData->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                                    <option value="rejected" {{ $assetData->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                                    <option value="approved" {{ $assetData->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endcan

                                                @can('asset-edit')
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </div>
                                                @endcan
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteAssetModal{{ $assetData->id }}" tabindex="-1"
                                 aria-labelledby="deleteAssetModalLabel{{ $assetData->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header bg-danger text-white">
                                            <h4 class="modal-title">Delete Asset</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <h5>Are you sure you want to delete this asset?</h5>
                                        </div>

                                        @can('asset-delete')
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <a href="{{ route('asset.destroy', $assetData->id) }}" class="btn btn-danger">Delete</a>
                                            </div>
                                        @endcan

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
    <div class="modal fade" id="addAssetModal" tabindex="-1" aria-labelledby="addAssetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title">Add New Asset</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('asset.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Asset Name</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        @if(!empty($categories))
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @else
                                            <option>No category available</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="account_id" class="form-label">Account</label>
                                    <select name="account_id" id="account_id" class="form-select" required>
                                        <option value="">Select Account</option>
                                        @if(!empty($accounts))
                                            @foreach($accounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
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
                                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="details" class="form-label">Details</label>
                                    <textarea name="details" id="" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
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
                        </div>

                        @can('asset-create')
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        @endcan

                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection
