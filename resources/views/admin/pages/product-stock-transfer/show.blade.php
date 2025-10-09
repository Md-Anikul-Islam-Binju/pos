@extends('admin.app')

@section('admin_content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                    <li class="breadcrumb-item active">View Transfer</li>
                </ol>
            </div>
            <h4 class="page-title">View Transfer</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <table class="table table-bordered">
                    <tr>
                        <th style="width: 30%;">From Showroom</th>
                        <td>{{ $transfer->fromShowroom->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th style="width: 30%;">To Showroom</th>
                        <td>{{ $transfer->toShowroom->name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Date</th>
                        <td>{{ \Carbon\Carbon::parse($transfer->date)->diffForHumans() ?? '' }}</td>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Details</th>
                        <td>{{ strip_tags($transfer->note) ?? '' }}</td>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Admin</th>
                        <td>{{ strip_tags($transfer->admin->name ?? '') }}</td>
                    </tr>
                    <tr>
                        <th style="width: 30%;">Status</th>
                        <td>{{ ucfirst($transfer->status) }}</td>
                    </tr>
                </table>

                <div class="table-responsive mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transfer->productStocks as $productStock)
                            <tr>
                                <td>{{ $productStock->product->name ?? '' }}</td>
                                <td>{{ $productStock->pivot->quantity ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex flex-wrap gap-1">
                    <a href="{{ route('product.stock.transfer.section') }}" class="btn btn-success">Go Back</a>

                    @can('product-stock-transfer-edit')
                    <a href="{{ route('product.stock.transfer.edit', $transfer->id) }}" class="btn btn-warning">
                        <i class="fa fa-pen"></i> Edit
                    </a>
                    @endcan

                    @if($transfer->status == 'pending')
                    <form class="d-inline" action="{{ route('product.stock.transfer.update.status', $transfer) }}" method="GET">
                        <input type="hidden" name="status" value="approved">
                        <button class="btn btn-success"><i class="fa fa-check"></i> Approve</button>
                    </form>
                    <form class="d-inline" action="{{ route('product.stock.transfer.update.status', $transfer) }}" method="GET">
                        <input type="hidden" name="status" value="rejected">
                        <button class="btn btn-danger"><i class="fa fa-times"></i> Reject</button>
                    </form>
                    @endif

                    @if($transfer->status == 'rejected' || $transfer->status == 'approved')
                    <form class="d-inline" action="{{ route('product.stock.transfer.update.status', $transfer) }}" method="GET">
                        <input type="hidden" name="status" value="pending">
                        <button class="btn btn-info"><i class="fa fa-arrow-left"></i> Pending</button>
                    </form>
                    @endif

                    @can('product-stock-transfer-delete')
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transfer->id }}">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                    @endcan
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal{{ $transfer->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $transfer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-colored-header bg-danger">
                <h4 class="modal-title">Delete Transfer</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5 class="mt-0">Are you sure you want to delete this transfer?</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <form action="{{ route('product.stock.transfer.destroy', $transfer->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection
