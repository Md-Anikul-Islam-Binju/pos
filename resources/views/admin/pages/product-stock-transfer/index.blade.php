@extends('admin.app')

@section('admin_content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Resource</a></li>
                    <li class="breadcrumb-item active">Product Stock Transfer</li>
                </ol>
            </div>
            <h4 class="page-title">Product Stock Transfer</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-end">
                    @can('product-stock-transfer-create')
                    <a href="{{ route('product.stock.transfer.create') }}" class="btn btn-info">Add New</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <table id="basic-datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>From Showroom</th>
                            <th>To Showroom</th>
                            <th>Status</th>
                            <th style="width:180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $key => $transfer)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $transfer->fromShowroom->name ?? '' }}</td>
                            <td>{{ $transfer->toShowroom->name ?? '' }}</td>
                            <td class="text-capitalize">{{ $transfer->status ?? '' }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    @can('product-stock-transfer-view')
                                    <a href="{{ route('product.stock.transfer.show', $transfer->id) }}" class="btn btn-info btn-sm">View</a>
                                    @endcan
                                    @can('product-stock-transfer-edit')
                                    <a href="{{ route('product.stock.transfer.edit', $transfer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    @endcan
                                    @can('product-stock-transfer-delete')
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transfer->id }}">Delete</button>
                                    @endcan
                                </div>

                                {{-- Status Change Buttons --}}
                                <div class="mt-1 d-flex gap-1">
                                    @if($transfer->status == 'pending')
                                    <form action="{{ route('product.stock.transfer.update.status', $transfer) }}" method="GET">
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('product.stock.transfer.update.status', $transfer) }}" method="GET">
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-danger btn-sm" type="submit"><i class="fa fa-times"></i></button>
                                    </form>
                                    @elseif($transfer->status == 'approved' || $transfer->status == 'rejected')
                                    <form action="{{ route('product.stock.transfer.update.status', $transfer) }}" method="GET">
                                        <input type="hidden" name="status" value="pending">
                                        <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-arrow-left"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

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

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection