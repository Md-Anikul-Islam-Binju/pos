@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Inventory</a></li>
                        <li class="breadcrumb-item active">Product</li>
                    </ol>
                </div>
                <h4 class="page-title">Product</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card shadow-sm rounded-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product List</h5>
                @can('product-create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="mdi mdi-plus"></i> Add Product
                    </button>
                @endcan
            </div>
            <div class="card-body">
                <table id="product-datatable" class="table table-striped table-bordered nowrap w-100">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>SKU</th>
                        <th>Unit</th>
                        <th>Width</th>
                        <th>Length</th>
                        <th>Density</th>
                        <th>Slug</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($product as $key => $p)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->category?->name }}</td>
                            <td>{{ $p->sku }}</td>
                            <td>{{ $p->unit?->name }}</td>
                            <td>{{ $p->width }}</td>
                            <td>{{ $p->length }}</td>
                            <td>{{ $p->density }}</td>
                            <td>{{ $p->slug }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    @can('product-edit')
                                        <button class="btn btn-sm btn-info"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editProductModal{{ $p->id }}">
                                            Edit
                                        </button>
                                    @endcan
                                    @can('product-delete')
                                        <button class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteProductModal{{ $p->id }}">
                                            Delete
                                        </button>
                                    @endcan
                                    @can('product-view')
                                        <button class="btn btn-sm btn-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewProductModal{{ $p->id }}">
                                            View
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editProductModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('product.update', $p->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">

                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" value="{{ $p->name }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Select Category</label>
                                                <select name="category_id" class="form-select" required>
                                                    @foreach($productCategory as $cat)
                                                        <option value="{{ $cat->id }}" {{ $cat->id == $p->category_id ? 'selected' : '' }}>
                                                            {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Details</label>
                                                <textarea name="details" id="details" >{{$p->details}}</textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Short Description</label>
                                                <textarea name="short_description" id="details" >{{$p->short_details}}</textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">SKU</label>
                                                <input type="text" name="sku" value="{{ $p->sku }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Unit</label>
                                                <select name="unit_id" class="form-select" required>
                                                    @foreach($unit as $u)
                                                        <option value="{{ $u->id }}" {{ $u->id == $p->unit_id ? 'selected' : '' }}>
                                                            {{ $u->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Width</label>
                                                <input type="number" step="0.01" name="width" value="{{ $p->width }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Length</label>
                                                <input type="number" step="0.01" name="length" value="{{ $p->length }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Density</label>
                                                <input type="number" step="0.01" name="density" value="{{ $p->density }}" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="product_images">Product Images</label>
                                                    <input type="hidden" name="old_product_images" value="{{ $p->images }}" class="d-none">
                                                    <input type="file" name="product_images[]" value="{{ $p->images }}" class="form-control" id="product_images" multiple>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="product_thumbnail">Product Thumbnail</label>
                                                    <input type="hidden" name="old_thumbnail" value="{{ $p->thumbnail }}" class="d-none">
                                                    <input type="file" name="thumbnail" value="{{ $p->thumbnail }}" class="form-control" id="product_thumbnail">
                                                </div>
                                            </div>

                                            <div class="col-md-6 d-flex flex-wrap gap-2" id="image-preview-row">
                                                @foreach(json_decode($product->images) as $key => $image)
                                                    <div class="image-container" id="image-{{ $key }}">
                                                        <img src="{{ asset('uploads/' . $image) }}" alt="Product Image" />
                                                        <button class="btn-overlay delete-image" data-image="{{ $image }}"
                                                                data-key="{{ $key }}" data-product-id="{{ $product->id }}" type="button">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="col-md-6 d-flex flex-wrap gap-2" id="thumb-preview-row">
                                                <div class="image-container" id="product-thumbnail">
                                                    <img src="{{ asset('uploads/' . $product->thumbnail) }}" alt="Product Thumbnail" />
                                                    <button class="btn-overlay delete-thumbnail" data-product-id="{{ $product->id }}" type="button">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            @can('product-edit')
                                                <button type="submit" class="btn btn-success">Update</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            @endcan
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Modal --}}
                        <div class="modal fade" id="deleteProductModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Confirm Delete</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete <b>{{ $p->name }}</b>?
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('product.destroy', $p->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- View Modal --}}
                        <div class="modal fade" id="viewProductModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title">Product Details - {{ $p->name }}</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><b>Category:</b> {{ $p->category?->name }}</p>
                                        <p><b>SKU:</b> {{ $p->sku }}</p>
                                        <p><b>Unit:</b> {{ $p->unit?->name }}</p>
                                        <p><b>Width:</b> {{ $p->width }}</p>
                                        <p><b>Length:</b> {{ $p->length }}</p>
                                        <p><b>Density:</b> {{ $p->density }}</p>
                                        <p><b>Details:</b> {{ $p->details }}</p>
                                        <p><b>Short Description:</b> {{ $p->short_details }}</p>

                                        <hr>
                                        <h6>Thumbnail</h6>
                                        @if($p->thumbnail)
                                            <img src="{{ Storage::url($p->thumbnail) }}" width="150" class="img-thumbnail">
                                        @else
                                            <p>No thumbnail</p>
                                        @endif

                                        <hr>
                                        <h6>Images</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($p->images)
                                                @foreach(json_decode($p->images, true) as $img)
                                                    <img src="{{ Storage::url($img) }}" width="120" class="img-thumbnail">
                                                @endforeach
                                            @else
                                                <p>No images</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
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
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter product name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Select Category</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select Category</option>
                                @foreach($productCategory as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="details">Details</label>
                                <textarea name="details" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="details">Short Description</label>
                                <textarea name="short_description" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control" placeholder="Enter SKU" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <select name="unit_id" class="form-select" required>
                                <option value="">Select Unit</option>
                                @foreach($unit as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Width</label>
                            <input type="number" step="0.01" name="width" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Length</label>
                            <input type="number" step="0.01" name="length" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Density</label>
                            <input type="number" step="0.01" name="density" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_images">Product Images</label>
                                <input type="hidden" name="old_product_images" class="d-none">
                                <input type="file" name="product_images[]" id="product_images" class="form-control" multiple>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="product_thumbnail">Product Thumbnail</label>
                                <input type="hidden" name="old_thumbnail" class="d-none">
                                <input type="file" name="thumbnail" id="product_thumbnail" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex flex-wrap gap-2" id="image-preview-row">
                            <!-- Product Images will be added here -->
                        </div>

                        <div class="col-md-6 d-flex flex-wrap gap-2" id="thumb-preview-row">
                            <!-- Product Thumbnail will be added here -->
                        </div>

                    </div>

                    @can('product-create')
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    @endcan

                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#product-datatable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: true,
            autoWidth: false
        });

        // Auto-generate slug preview (optional)
        $('input[name="name"]').on('input', function () {
            let slug = $(this).val().toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            $(this).closest('form').find('input[name="slug"]').val(slug);
        });
    });
</script>
<script>
    // Preview for multiple product images
    $(document).on('change', '#product_images', function () {
        let previewRow = $('#image-preview-row');
        previewRow.empty(); // clear old previews

        if (this.files) {
            $.each(this.files, function (i, file) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    previewRow.append(`
                        <div class="image-container position-relative">
                            <img src="${e.target.result}" width="100" class="img-thumbnail"/>
                        </div>
                    `);
                }
                reader.readAsDataURL(file);
            });
        }
    });

    // Preview for single thumbnail
    $(document).on('change', '#product_thumbnail', function () {
        let previewRow = $('#thumb-preview-row');
        previewRow.empty(); // clear old thumbnail

        if (this.files && this.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                previewRow.append(`
                    <div class="image-container position-relative">
                        <img src="${e.target.result}" width="100" class="img-thumbnail"/>
                    </div>
                `);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endpush
