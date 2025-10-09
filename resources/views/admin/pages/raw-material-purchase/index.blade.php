@extends('admin.app')
@section('admin_content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Raw Materials</a></li>
                        <li class="breadcrumb-item active">Raw Material Purchase</li>
                    </ol>
                </div>
                <h4 class="page-title">Raw Material Purchase</h4>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('raw-material-purchase-create')
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addNewPurchaseModal">Add New</button>
                    @endcan
                </div>
            </div>

            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Supplier</th>
                            <th>Account</th>
                            <th>Warehouse</th>
                            <th>Purchase Date</th>
                            <th>Total Cost</th>
                            <th>Total Price</th>
                            <th>Net Total</th>
                            <th>Cost Details</th>
                            <th>Status</th>
                            <th style="min-width: 260px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $key => $purchase)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ optional($purchase->supplier)->name ?? 'N/A' }}</td>
                                <td>{{ optional($purchase->account)->name ?? 'N/A' }}</td>
                                <td>{{ optional($purchase->warehouse)->name ?? 'N/A' }}</td>
                                <td>{{ $purchase->purchase_date }}</td>
                                <td>{{ number_format($purchase->total_cost ?? 0, 2) }}</td>
                                <td>{{ number_format($purchase->total_price ?? 0, 2) }}</td>
                                <td>{{ number_format($purchase->net_total ?? $purchase->amount ?? 0, 2) }}</td>
                                <td>{{ $purchase->cost_details ?? '-' }}</td>
                                <td>
                                    @if($purchase->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($purchase->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($purchase->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-secondary">Unknown</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-end">
                                        @can('raw-material-purchase-view')
                                            <a href="{{ route('raw.material.purchase.show', $purchase->id) }}" class="btn btn-primary btn-sm">View</a>
                                        @endcan

                                        @can('raw-material-purchase-edit')
                                            {{-- Open Edit modal --}}
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#editPurchaseModal{{ $purchase->id }}">Edit</button>
                                        @endcan

                                        @can('raw-material-purchase-delete')
                                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePurchaseModal{{ $purchase->id }}">Delete</button>
                                        @endcan

                                        @can('raw-material-purchase-view')
                                            <a href="{{ route('raw.material.purchase.print', $purchase->id) }}" target="_blank" class="btn btn-success btn-sm">Print</a>
                                        @endcan

                                        {{-- Status action buttons (separate) --}}
                                        <div class="btn-group ms-2" role="group">
                                            @can('raw-material-purchase-edit')
                                                <a href="{{ route('raw.material.purchase.update.status', ['id' => $purchase->id, 'status' => 'approved']) }}"
                                                   class="btn btn-outline-success btn-sm"
                                                   onclick="return confirm('Approve this purchase?')">Approve</a>
                                                <a href="{{ route('raw.material.purchase.update.status', ['id' => $purchase->id, 'status' => 'rejected']) }}"
                                                   class="btn btn-outline-danger btn-sm"
                                                   onclick="return confirm('Reject this purchase?')">Reject</a>
                                                <a href="{{ route('raw.material.purchase.update.status', ['id' => $purchase->id, 'status' => 'pending']) }}"
                                                   class="btn btn-outline-secondary btn-sm"
                                                   onclick="return confirm('Set to pending?')">Pending</a>
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Delete Modal --}}
                            <div id="deletePurchaseModal{{ $purchase->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deletePurchaseModalLabel{{ $purchase->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header modal-colored-header bg-danger">
                                            <h4 class="modal-title">Delete Purchase</h4>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h5 class="mt-0">Are you sure you want to delete this purchase?</h5>
                                            <p><strong>Supplier:</strong> {{ optional($purchase->supplier)->name ?? 'N/A' }}</p>
                                            <p><strong>Date:</strong> {{ $purchase->purchase_date }}</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <a href="{{ route('raw.material.purchase.destroy', $purchase->id) }}" class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Modal (pre-filled) --}}
                            <div class="modal fade" id="editPurchaseModal{{ $purchase->id }}" tabindex="-1" aria-labelledby="editPurchaseModalLabel{{ $purchase->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Purchase #{{ $purchase->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="post" action="{{ route('raw.material.purchase.update', $purchase->id) }}" enctype="multipart/form-data" class="edit-purchase-form" data-purchase-id="{{ $purchase->id }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Supplier</label>
                                                        <select name="supplier_id" class="form-select">
                                                            <option value="">Select Supplier</option>
                                                            @foreach(\App\Models\Supplier::orderBy('id', 'desc')->get() as $s)
                                                                <option value="{{ $s->id }}" {{ $purchase->supplier_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Warehouse</label>
                                                        <select name="warehouse_id" class="form-select">
                                                            <option value="">Select Warehouse</option>
                                                            @foreach(\App\Models\Warehouse::orderBy('id', 'desc')->get() as $w)
                                                                <option value="{{ $w->id }}" {{ $purchase->warehouse_id == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Purchase Date</label>
                                                        <input type="date" name="purchase_date" value="{{ $purchase->purchase_date }}" class="form-control" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Account</label>
                                                        <select name="account_id" class="form-select">
                                                            <option value="">Select Account</option>
                                                            @foreach(\App\Models\Account::orderBy('id','desc')->get() as $a)
                                                                <option value="{{ $a->id }}" {{ $purchase->account_id == $a->id ? 'selected' : '' }}>{{ $a->name ?? $a->title ?? 'Account '.$a->id }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <hr/>

                                                {{-- Product table --}}
                                                <div class="table-responsive mb-3">
                                                    <table class="table table-bordered purchase-items-table" id="purchase-items-{{ $purchase->id }}">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:28%;">Raw Material</th>
                                                                <th style="width:12%;">Brand</th>
                                                                <th style="width:10%;">Size</th>
                                                                <th style="width:10%;">Color</th>
                                                                <th style="width:8%;">Qty</th>
                                                                <th style="width:12%;">Price</th>
                                                                <th style="width:12%;">Total</th>
                                                                <th style="width:6%;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {{-- load existing purchase items --}}
                                                            @php
                                                                $items = DB::table('purchase_raw_material')->where('raw_material_purchase_id', $purchase->id)->get();
                                                            @endphp
                                                            @if($items && $items->count())
                                                                @foreach($items as $idx => $item)
                                                                    <tr>
                                                                        <td>
                                                                            <select name="product_id[]" class="form-select product-select">
                                                                                <option value="">Select Raw Material</option>
                                                                                @foreach(\App\Models\RawMaterial::with('brands','sizes','colors')->get() as $rm)
                                                                                    <option value="{{ $rm->id }}" {{ $item->raw_material_id == $rm->id ? 'selected' : '' }}>{{ $rm->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="brand_id[]" class="form-select">
                                                                                <option value="">Brand</option>
                                                                                @foreach(\App\Models\Brand::orderBy('id','desc')->get() as $b)
                                                                                    <option value="{{ $b->id }}" {{ $item->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="size_id[]" class="form-select">
                                                                                <option value="">Size</option>
                                                                                @foreach(\App\Models\Size::orderBy('id','desc')->get() as $s)
                                                                                    <option value="{{ $s->id }}" {{ $item->size_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="color_id[]" class="form-select">
                                                                                <option value="">Color</option>
                                                                                @foreach(\App\Models\Color::orderBy('id','desc')->get() as $c)
                                                                                    <option value="{{ $c->id }}" {{ $item->color_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td><input type="number" name="quantity[]" class="form-control qty-input" value="{{ $item->quantity }}" step="1" min="0"></td>
                                                                        <td><input type="number" name="price[]" class="form-control price-input" value="{{ $item->price }}" step="0.01" min="0"></td>
                                                                        <td><input type="number" name="total_price[]" class="form-control total-input" value="{{ $item->total_price }}" step="0.01" readonly></td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                {{-- empty row placeholder --}}
                                                                <tr>
                                                                    <td>
                                                                        <select name="product_id[]" class="form-select product-select">
                                                                            <option value="">Select Raw Material</option>
                                                                            @foreach(\App\Models\RawMaterial::with('brands','sizes','colors')->get() as $rm)
                                                                                <option value="{{ $rm->id }}">{{ $rm->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="brand_id[]" class="form-select">
                                                                            <option value="">Brand</option>
                                                                            @foreach(\App\Models\Brand::orderBy('id','desc')->get() as $b)
                                                                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="size_id[]" class="form-select">
                                                                            <option value="">Size</option>
                                                                            @foreach(\App\Models\Size::orderBy('id','desc')->get() as $s)
                                                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="color_id[]" class="form-select">
                                                                            <option value="">Color</option>
                                                                            @foreach(\App\Models\Color::orderBy('id','desc')->get() as $c)
                                                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td><input type="number" name="quantity[]" class="form-control qty-input" value="0" step="1" min="0"></td>
                                                                    <td><input type="number" name="price[]" class="form-control price-input" value="0.00" step="0.01" min="0"></td>
                                                                    <td><input type="number" name="total_price[]" class="form-control total-input" value="0.00" step="0.01" readonly></td>
                                                                    <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="8">
                                                                    <button type="button" class="btn btn-sm btn-secondary add-item">+ Add Item</button>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>

                                                {{-- Cost details repeater --}}
                                                <div class="mb-3">
                                                    <label class="form-label">Cost Details</label>
                                                    <div class="cost-details-wrap">
                                                        @php
                                                            $costs = json_decode($purchase->cost_details ?? '[]', true);
                                                        @endphp
                                                        @if(!empty($costs))
                                                            @foreach($costs as $ci => $c)
                                                                <div class="row cost-row g-2 mb-2">
                                                                    <div class="col-md-8">
                                                                        <input type="text" name="cost_details[]" class="form-control" placeholder="Cost Detail" value="{{ $c['detail'] ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input type="number" name="cost_amount[]" class="form-control cost-amount" placeholder="Amount" step="0.01" value="{{ $c['amount'] ?? 0 }}">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button" class="btn btn-danger btn-sm remove-cost">X</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="row cost-row g-2 mb-2">
                                                                <div class="col-md-8">
                                                                    <input type="text" name="cost_details[]" class="form-control" placeholder="Cost Detail">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <input type="number" name="cost_amount[]" class="form-control cost-amount" placeholder="Amount" step="0.01" value="0">
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <button type="button" class="btn btn-danger btn-sm remove-cost">X</button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-secondary add-cost">+ Add Cost</button>
                                                    </div>
                                                </div>

                                                {{-- Payment & summary --}}
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Total Item Price</label>
                                                        <input type="number" class="form-control total-item-price" name="total_price" value="{{ $purchase->total_price ?? 0 }}" step="0.01" readonly>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Total Cost</label>
                                                        <input type="number" class="form-control total-cost" name="total_cost" value="{{ $purchase->total_cost ?? 0 }}" step="0.01" readonly>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Net Total (Cost + Items)</label>
                                                        <input type="number" class="form-control net-total" name="net_total" value="{{ $purchase->net_total ?? $purchase->amount ?? 0 }}" step="0.01" readonly>
                                                    </div>

                                                    <div class="col-md-4 mt-2">
                                                        <label class="form-label">Payment Type</label>
                                                        <select name="payment_type" class="form-select payment-type">
                                                            <option value="full_paid" {{ ($purchase->payment_type ?? '') == 'full_paid' ? 'selected' : '' }}>Full Paid</option>
                                                            <option value="partial" {{ ($purchase->payment_type ?? '') == 'partial' ? 'selected' : '' }}>Partial</option>
                                                            <option value="due" {{ ($purchase->payment_type ?? '') == 'due' ? 'selected' : '' }}>Due</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mt-2">
                                                        <label class="form-label">Paid Amount</label>
                                                        <input type="number" name="paid_amount" class="form-control paid-amount" value="{{ $purchase->amount ?? 0 }}" step="0.01">
                                                    </div>
                                                    <div class="col-md-4 mt-2">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" {{ ($purchase->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ ($purchase->status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="rejected" {{ ($purchase->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mt-3 d-flex justify-content-end">
                                                    <button class="btn btn-primary" type="submit">Update Purchase</button>
                                                </div>
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
    <div class="modal fade" id="addNewPurchaseModal" tabindex="-1" aria-labelledby="addNewPurchaseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Raw Material Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form method="post" action="{{ route('raw.material.purchase.store') }}" enctype="multipart/form-data" id="create-purchase-form">
                        @csrf

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select">
                                    <option value="">Select Supplier</option>
                                    @foreach(\App\Models\Supplier::orderBy('id','desc')->get() as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Warehouse</label>
                                <select name="warehouse_id" class="form-select">
                                    <option value="">Select Warehouse</option>
                                    @foreach(\App\Models\Warehouse::orderBy('id','desc')->get() as $w)
                                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Account</label>
                                <select name="account_id" class="form-select" required>
                                    <option value="">Select Account</option>
                                    @foreach(\App\Models\Account::orderBy('id','desc')->get() as $a)
                                        <option value="{{ $a->id }}">{{ $a->name ?? $a->title ?? 'Account '.$a->id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr/>

                        {{-- Product table (create) --}}
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="purchase-items-create">
                                <thead>
                                    <tr>
                                        <th style="width:28%;">Raw Material</th>
                                        <th style="width:12%;">Brand</th>
                                        <th style="width:10%;">Size</th>
                                        <th style="width:10%;">Color</th>
                                        <th style="width:8%;">Qty</th>
                                        <th style="width:12%;">Price</th>
                                        <th style="width:12%;">Total</th>
                                        <th style="width:6%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select name="product_id[]" class="form-select product-select">
                                                <option value="">Select Raw Material</option>
                                                @foreach(\App\Models\RawMaterial::with('brands','sizes','colors')->get() as $rm)
                                                    <option value="{{ $rm->id }}">{{ $rm->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="brand_id[]" class="form-select">
                                                <option value="">Brand</option>
                                                @foreach(\App\Models\Brand::orderBy('id','desc')->get() as $b)
                                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="size_id[]" class="form-select">
                                                <option value="">Size</option>
                                                @foreach(\App\Models\Size::orderBy('id','desc')->get() as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="color_id[]" class="form-select">
                                                <option value="">Color</option>
                                                @foreach(\App\Models\Color::orderBy('id','desc')->get() as $c)
                                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="quantity[]" class="form-control qty-input" value="0" step="1" min="0"></td>
                                        <td><input type="number" name="price[]" class="form-control price-input" value="0.00" step="0.01" min="0"></td>
                                        <td><input type="number" name="total_price[]" class="form-control total-input" value="0.00" step="0.01" readonly></td>
                                        <td><button type="button" class="btn btn-sm btn-danger remove-row">X</button></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8">
                                            <button type="button" class="btn btn-sm btn-secondary add-item">+ Add Item</button>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- Cost details --}}
                        <div class="mb-3">
                            <label class="form-label">Cost Details</label>
                            <div class="cost-details-wrap">
                                <div class="row cost-row g-2 mb-2">
                                    <div class="col-md-8">
                                        <input type="text" name="cost_details[]" class="form-control" placeholder="Cost Detail">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="cost_amount[]" class="form-control cost-amount" placeholder="Amount" step="0.01" value="0">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-cost">X</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-secondary add-cost">+ Add Cost</button>
                            </div>
                        </div>

                        {{-- Payment & summary --}}
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Total Item Price</label>
                                <input type="number" class="form-control total-item-price" name="total_price" value="0.00" step="0.01" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Total Cost</label>
                                <input type="number" class="form-control total-cost" name="total_cost" value="0.00" step="0.01" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Net Total (Cost + Items)</label>
                                <input type="number" class="form-control net-total" name="net_total" value="0.00" step="0.01" readonly>
                            </div>

                            <div class="col-md-4 mt-2">
                                <label class="form-label">Payment Type</label>
                                <select name="payment_type" class="form-select payment-type">
                                    <option value="full_paid">Full Paid</option>
                                    <option value="partial">Partial</option>
                                    <option value="due">Due</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Paid Amount</label>
                                <input type="number" name="paid_amount" class="form-control paid-amount" value="0.00" step="0.01">
                            </div>
                            <div class="col-md-4 mt-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="pending" selected>Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">Create Purchase</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Required scripts --}}
    @push('scripts')
        <!-- Assumes jQuery and Bootstrap JS are loaded by layout; datatables optional -->
        <script>
            (function($){
                // Utility to recalc totals on a given container (table or modal)
                function recalc(container) {
                    // items
                    let totalItems = 0;
                    $(container).find('.purchase-items-table, #purchase-items-create, table').find('tbody tr').each(function(){
                        const qty = parseFloat($(this).find('.qty-input').val() || 0);
                        const price = parseFloat($(this).find('.price-input').val() || 0);
                        const total = parseFloat((qty * price).toFixed(2)) || 0;
                        $(this).find('.total-input').val(total.toFixed(2));
                        totalItems += total;
                    });

                    // costs
                    let totalCosts = 0;
                    $(container).find('.cost-amount').each(function(){
                        totalCosts += parseFloat($(this).val() || 0);
                    });

                    // set fields (prefer fields in container first)
                    const totalItemField = $(container).find('.total-item-price').first();
                    const totalCostField = $(container).find('.total-cost').first();
                    const netTotalField = $(container).find('.net-total').first();
                    if(totalItemField.length) totalItemField.val(totalItems.toFixed(2));
                    if(totalCostField.length) totalCostField.val(totalCosts.toFixed(2));
                    if(netTotalField.length) netTotalField.val((totalItems + totalCosts).toFixed(2));

                    // if payment type is full_paid, set paid amount = net total
                    $(container).find('.payment-type').each(function(){
                        const type = $(this).val();
                        const paidField = $(container).find('.paid-amount').first();
                        if(type === 'full_paid') {
                            if(paidField.length) paidField.val(((totalItems + totalCosts)).toFixed(2));
                        }
                    });
                }

                // Document ready
                $(document).ready(function(){
                    // Recalc initially for all edit modals
                    $('form.edit-purchase-form').each(function(){
                        recalc($(this));
                    });
                    recalc($('#create-purchase-form'));

                    // Delegate add-item (works for both create table and edit tables)
                    $(document).on('click', '.add-item', function(){
                        // find nearest table body
                        const tbl = $(this).closest('table');
                        const tbody = tbl.find('tbody');
                        // clone first row as template (or create fresh)
                        const templateRow = tbody.find('tr').first().clone();
                        // clear values
                        templateRow.find('select').val('');
                        templateRow.find('input').val('0');
                        tbody.append(templateRow);
                    });

                    // Delegate remove-row
                    $(document).on('click', '.remove-row', function(){
                        const tbody = $(this).closest('tbody');
                        // if only 1 row, clear values instead of removing
                        if(tbody.find('tr').length <= 1){
                            const row = $(this).closest('tr');
                            row.find('select').val('');
                            row.find('input').val('0');
                        } else {
                            $(this).closest('tr').remove();
                        }
                        // recalc parent form
                        recalc($(this).closest('form'));
                    });

                    // Add cost
                    $(document).on('click', '.add-cost', function(){
                        const wrap = $(this).closest('.modal-body, .card-body, form').find('.cost-details-wrap').first();
                        const newRow = $('<div class="row cost-row g-2 mb-2">' +
                            '<div class="col-md-8"><input type="text" name="cost_details[]" class="form-control" placeholder="Cost Detail"></div>' +
                            '<div class="col-md-3"><input type="number" name="cost_amount[]" class="form-control cost-amount" placeholder="Amount" step="0.01" value="0"></div>' +
                            '<div class="col-md-1"><button type="button" class="btn btn-danger btn-sm remove-cost">X</button></div>' +
                            '</div>');
                        wrap.append(newRow);
                    });

                    // Remove cost
                    $(document).on('click', '.remove-cost', function(){
                        const wrap = $(this).closest('.cost-details-wrap');
                        if(wrap.find('.cost-row').length <= 1){
                            const row = $(this).closest('.cost-row');
                            row.find('input').val('');
                        } else {
                            $(this).closest('.cost-row').remove();
                        }
                        recalc($(this).closest('form'));
                    });

                    // recalc when qty/price/cost change
                    $(document).on('input change', '.qty-input, .price-input, .cost-amount, .payment-type, .paid-amount', function(){
                        const form = $(this).closest('form');
                        recalc(form);
                    });

                    // When payment-type changes: if full_paid set paid-amount automatically
                    $(document).on('change', '.payment-type', function(){
                        const form = $(this).closest('form');
                        const type = $(this).val();
                        const net = parseFloat(form.find('.net-total').val() || 0);
                        const paidField = form.find('.paid-amount').first();
                        if(type === 'full_paid'){
                            paidField.val(net.toFixed(2));
                        } else if(type === 'due'){
                            paidField.val('0.00');
                        }
                    });

                    // When edit modal opens, recalc its form
                    $(document).on('shown.bs.modal', '.modal', function(){
                        const form = $(this).find('form').first();
                        recalc(form);
                    });

                    // If datatable is used elsewhere, initialize (optional)
                    if($.fn.DataTable) {
                        try { $('#basic-datatable').DataTable(); } catch(e) { /* ignore */ }
                    }

                });
            })(jQuery);
        </script>
    @endpush
@endsection
