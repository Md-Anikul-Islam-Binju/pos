@extends('admin.app')
@section('admin_content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                        <li class="breadcrumb-item active">Production!</li>
                    </ol>
                </div>
                <h4 class="page-title">Production!</h4>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('production-create')
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewModalId">Add New</button>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                    <tr>
                        <th>S/N</th>
                        <th>Production House</th>
                        <th>Showroom</th>
                        <th>Account</th>
                        <th>Production Date</th>
                        <th>Total Cost</th>
                        <th>Total Raw Material</th>
                        <th>Total Product Cost</th>
                        <th>Net Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($production as $key => $productionData)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $productionData->productionHouse?->name ?? '-' }}</td>
                            <td>{{ $productionData->showroom?->name ?? '-' }}</td>
                            <td>{{ $productionData->account?->name ?? '-' }}</td>
                            <td>{{ $productionData->production_date }}</td>
                            <td>{{ number_format($productionData->total_cost,2) }}</td>
                            <td>{{ number_format($productionData->total_raw_material_cost,2) }}</td>
                            <td>{{ number_format($productionData->total_product_cost,2) }}</td>
                            <td>{{ number_format($productionData->net_total,2) }}</td>
                            <td>
                                @can('production-edit')
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $productionData->id }}">Edit</button>
                                @endcan
                                @can('production-delete')
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $productionData->id }}">Delete</button>
                                @endcan
                            </td>
                        </tr>

                        <!-- Edit Modal Placeholder -->
                        <div class="modal fade" id="editModal{{ $productionData->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Production</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="post" action="{{ route('production.update', $productionData->id) }}">
                                            @csrf
                                            @method('PUT')

                                            <!-- Tabs Navigation -->
                                            <ul class="nav nav-tabs" id="editTabs{{ $productionData->id }}" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="editTab1-tab{{ $productionData->id }}" data-bs-toggle="tab" data-bs-target="#editTab1{{ $productionData->id }}" type="button" role="tab">
                                                        Production
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="editTab2-tab{{ $productionData->id }}" data-bs-toggle="tab" data-bs-target="#editTab2{{ $productionData->id }}" type="button" role="tab">
                                                        Raw Materials
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="editTab3-tab{{ $productionData->id }}" data-bs-toggle="tab" data-bs-target="#editTab3{{ $productionData->id }}" type="button" role="tab">
                                                        Products
                                                    </button>
                                                </li>
                                            </ul>

                                            <!-- Tabs Content -->
                                            <div class="tab-content mt-3">

                                                <!-- Tab 1: Production Info -->
                                                <div class="tab-pane fade show active" id="editTab1{{ $productionData->id }}" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Production House</label>
                                                            <select name="production_house_id" class="form-select" required>
                                                                <option value="">Select Production House</option>
                                                                @foreach($productionHouse as $house)
                                                                    <option value="{{ $house->id }}" {{ $productionData->production_house_id == $house->id ? 'selected' : '' }}>{{ $house->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Showroom</label>
                                                            <select name="showroom_id" class="form-select" required>
                                                                <option value="">Select Showroom</option>
                                                                @foreach($showroom as $show)
                                                                    <option value="{{ $show->id }}" {{ $productionData->showroom_id == $show->id ? 'selected' : '' }}>{{ $show->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Account</label>
                                                            <select name="account_id" class="form-select" required>
                                                                <option value="">Select Account</option>
                                                                @foreach($account as $acc)
                                                                    <option value="{{ $acc->id }}" {{ $productionData->account_id == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Production Date</label>
                                                            <input type="date" name="production_date" class="form-control" value="{{ $productionData->production_date }}" required>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tab 2: Raw Materials -->
                                                <div class="tab-pane fade" id="editTab2{{ $productionData->id }}" role="tabpanel">
                                                    <div id="editRawMaterialsWrapper{{ $productionData->id }}">
                                                        @foreach($productionData->rawMaterials as $rm)
                                                            <div class="row raw-material-row mb-2">
                                                                <div class="col-md-3">
                                                                    <label>Raw Material</label>
                                                                    <select name="raw_material_id[]" class="form-select">
                                                                        <option value="">Select Material</option>
                                                                        @foreach($rawMaterialStock as $stock)
                                                                            <option value="{{ $stock->id }}" {{ $rm->raw_material_id == $stock->id ? 'selected' : '' }}>
                                                                                {{ $stock->raw_material->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Brand</label>
                                                                    <select name="raw_material_brand_id[]" class="form-select">
                                                                        <option value="">Select Brand</option>
                                                                        @foreach($brand as $b)
                                                                            <option value="{{ $b->id }}" {{ $rm->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Size</label>
                                                                    <select name="raw_material_size_id[]" class="form-select">
                                                                        <option value="">Select Size</option>
                                                                        @foreach($size as $s)
                                                                            <option value="{{ $s->id }}" {{ $rm->size_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Color</label>
                                                                    <select name="raw_material_color_id[]" class="form-select">
                                                                        <option value="">Select Color</option>
                                                                        @foreach($color as $c)
                                                                            <option value="{{ $c->id }}" {{ $rm->color_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Quantity</label>
                                                                    <input type="number" name="raw_material_quantity[]" class="form-control" value="{{ $rm->quantity }}" min="0">
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Price</label>
                                                                    <input type="number" name="raw_material_price[]" class="form-control" value="{{ $rm->price }}" min="0">
                                                                </div>
                                                                <div class="col-md-1 d-flex align-items-end">
                                                                    <button type="button" class="btn btn-success addRawMaterial">+</button>
                                                                    <button type="button" class="btn btn-danger removeRawMaterial">-</button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Tab 3: Products -->
                                                <div class="tab-pane fade" id="editTab3{{ $productionData->id }}" role="tabpanel">
                                                    <div id="editProductsWrapper{{ $productionData->id }}">
                                                        @foreach($productionData->products as $p)
                                                            <div class="row product-row mb-2">
                                                                <div class="col-md-3">
                                                                    <label>Product</label>
                                                                    <select name="product_id[]" class="form-select">
                                                                        <option value="">Select Product</option>
                                                                        @foreach($product as $prod)
                                                                            <option value="{{ $prod->id }}" {{ $p->product_id == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Brand</label>
                                                                    <select name="brand_id[]" class="form-select">
                                                                        <option value="">Select Brand</option>
                                                                        @foreach($brand as $b)
                                                                            <option value="{{ $b->id }}" {{ $p->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Size</label>
                                                                    <select name="size_id[]" class="form-select">
                                                                        <option value="">Select Size</option>
                                                                        @foreach($size as $s)
                                                                            <option value="{{ $s->id }}" {{ $p->size_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label>Color</label>
                                                                    <select name="color_id[]" class="form-select">
                                                                        <option value="">Select Color</option>
                                                                        @foreach($color as $c)
                                                                            <option value="{{ $c->id }}" {{ $p->color_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Qty</label>
                                                                    <input type="number" name="quantity[]" class="form-control" value="{{ $p->quantity }}" min="0">
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <label>Cost</label>
                                                                    <input type="number" name="price[]" class="form-control" value="{{ $p->price }}" min="0">
                                                                </div>
                                                                <div class="col-md-1 d-flex align-items-end">
                                                                    <button type="button" class="btn btn-success addProduct">+</button>
                                                                    <button type="button" class="btn btn-danger removeProduct">-</button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="row mt-3">
                                                        <div class="col-md-4">
                                                            <label>Total Raw Material Cost</label>
                                                            <input type="text" class="form-control totalRawMaterialCost" readonly value="0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Total Product Cost</label>
                                                            <input type="text" class="form-control totalProductCost" readonly value="0">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label>Net Total</label>
                                                            <input type="text" class="form-control netTotal" readonly value="0">
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>



                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal Placeholder -->
                        <div class="modal fade" id="deleteModal{{ $productionData->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h4 class="modal-title">Delete Production</h4>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <h5>Are you sure you want to delete this production?</h5>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        <a href="{{ route('production.destroy',$productionData->id) }}" class="btn btn-danger">Delete</a>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addNewModalId" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addNewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="addNewModalLabel">Add Production</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="post" action="{{ route('production.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <!-- Tabs Navigation -->
                        <ul class="nav nav-tabs" id="addTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab">
                                    Production
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab">
                                    Raw Materials
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3" type="button" role="tab">
                                    Products
                                </button>
                            </li>
                        </ul>

                        <!-- Tabs Content -->
                        <div class="tab-content mt-3" id="addTabsContent">

                            <!-- Tab 1: Production Info -->
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Production House</label>
                                            <select name="production_house_id" class="form-select" required>
                                                <option value="">Select Production House</option>
                                                @foreach($productionHouse as $house)
                                                    <option value="{{ $house->id }}">{{ $house->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Showroom</label>
                                            <select name="showroom_id" class="form-select" required>
                                                <option value="">Select Showroom</option>
                                                @foreach($showroom as $show)
                                                    <option value="{{ $show->id }}">{{ $show->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
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

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Production Date</label>
                                            <input type="date" name="production_date" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Type</label>
                                            <select name="payment_type" class="form-select">
                                                <option value="full_paid">Full Paid</option>
                                                <option value="partial_paid">Partial Paid</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Paid Amount</label>
                                            <input type="number" name="paid_amount" class="form-control" placeholder="Enter Paid Amount">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Raw Materials -->
                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                <div id="rawMaterialsWrapper" class="raw-material-wrapper">
                                    <!-- Hidden template row -->
                                    <div class="raw-material-row template-row d-none">
                                        <input type="text" name="raw_material_name[]" class="form-control" />
                                        <input type="number" name="quantity[]" class="form-control quantity" />
                                        <input type="number" name="price[]" class="form-control price" />
                                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                                    </div>
                                    <div class="row raw-material-row mb-2">
                                        <div class="col-md-3">
                                            <label>Raw Material</label>
                                            <select name="raw_material_id[]" class="form-select">
                                                <option value="">Select Material</option>
                                                @foreach($rawMaterialStock as $rm)
                                                    <option value="{{ $rm->id }}">{{ $rm->raw_material->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Brand</label>
                                            <select name="raw_material_brand_id[]" class="form-select">
                                                <option value="">Select Brand</option>
                                                @foreach($brand as $b)
                                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Size</label>
                                            <select name="raw_material_size_id[]" class="form-select">
                                                <option value="">Select Size</option>
                                                @foreach($size as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Color</label>
                                            <select name="raw_material_color_id[]" class="form-select">
                                                <option value="">Select Color</option>
                                                @foreach($color as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label>Quantity</label>
                                            <input type="number" name="raw_material_quantity[]" class="form-control" placeholder="Qty" min="0">
                                        </div>
                                        <div class="col-md-1">
                                            <label>Price</label>
                                            <input type="number" name="raw_material_price[]" class="form-control" placeholder="Price" min="0">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-success addRawMaterial">+</button>
                                            <button type="button" class="btn btn-danger removeRawMaterial">-</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Products -->
                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                <div id="productsWrapper" class="product-wrapper">
                                    <div class="product-row template-row d-none">
                                        <input type="text" name="product_name[]" class="form-control" />
                                        <input type="number" name="quantity[]" class="form-control quantity" />
                                        <input type="number" name="price[]" class="form-control price" />
                                        <button type="button" class="btn btn-danger remove-row">Remove</button>
                                    </div>
                                    <div class="row product-row mb-2">
                                        <div class="col-md-3">
                                            <label>Product</label>
                                            <select name="product_id[]" class="form-select">
                                                <option value="">Select Product</option>
                                                @foreach($product as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Brand</label>
                                            <select name="brand_id[]" class="form-select">
                                                <option value="">Select Brand</option>
                                                @foreach($brand as $b)
                                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Size</label>
                                            <select name="size_id[]" class="form-select">
                                                <option value="">Select Size</option>
                                                @foreach($size as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Color</label>
                                            <select name="color_id[]" class="form-select">
                                                <option value="">Select Color</option>
                                                @foreach($color as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label>Qty</label>
                                            <input type="number" name="quantity[]" class="form-control" placeholder="Qty" min="0">
                                        </div>
                                        <div class="col-md-1">
                                            <label>Cost</label>
                                            <input type="number" name="price[]" class="form-control" placeholder="Cost" min="0">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-success addProduct">+</button>
                                            <button type="button" class="btn btn-danger removeProduct">-</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label>Total Raw Material Cost</label>
                                    <input type="text" class="form-control totalRawMaterialCost" readonly value="0">
                                </div>
                                <div class="col-md-4">
                                    <label>Total Product Cost</label>
                                    <input type="text" class="form-control totalProductCost" readonly value="0">
                                </div>
                                <div class="col-md-4">
                                    <label>Net Total</label>
                                    <input type="text" class="form-control netTotal" readonly value="0">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            // Function to add raw material row
            function addRawMaterialRow(modal) {
                let wrapper = modal.find('.raw-material-wrapper');
                let template = wrapper.find('.template-row').first().clone();
                template.removeClass('template-row d-none'); // show row
                wrapper.append(template);
                calculateTotals(modal);
            }

            // Function to add product row
            function addProductRow(modal) {
                let wrapper = modal.find('.product-wrapper');
                let template = wrapper.find('.template-row').first().clone();
                template.removeClass('template-row d-none'); // show row
                wrapper.append(template);
                calculateTotals(modal);
            }

            // Function to calculate totals inside a modal
            function calculateTotals(modal) {
                let totalRaw = 0;
                modal.find('.raw-material-row').each(function() {
                    let qty = parseFloat($(this).find('.quantity').val()) || 0;
                    let price = parseFloat($(this).find('.price').val()) || 0;
                    totalRaw += qty * price;
                });
                modal.find('.total-raw-materials').text(totalRaw.toFixed(2));

                let totalProd = 0;
                modal.find('.product-row').each(function() {
                    let qty = parseFloat($(this).find('.quantity').val()) || 0;
                    let price = parseFloat($(this).find('.price').val()) || 0;
                    totalProd += qty * price;
                });
                modal.find('.total-products').text(totalProd.toFixed(2));
            }

            // Add Raw Material button
            $(document).on('click', '.add-raw-material', function() {
                let modal = $(this).closest('.modal');
                addRawMaterialRow(modal);
            });

            // Add Product button
            $(document).on('click', '.add-product', function() {
                let modal = $(this).closest('.modal');
                addProductRow(modal);
            });

            // Remove row button
            $(document).on('click', '.remove-row', function() {
                let modal = $(this).closest('.modal');
                $(this).closest('.raw-material-row, .product-row').remove();
                calculateTotals(modal);
            });

            // Recalculate totals on input change
            $(document).on('input', '.quantity, .price', function() {
                let modal = $(this).closest('.modal');
                calculateTotals(modal);
            });

            // Initialize totals when modal is shown (Edit modal)
            $('.modal').on('shown.bs.modal', function() {
                let modal = $(this);
                calculateTotals(modal);
            });
        });

    </script>
@endpush

