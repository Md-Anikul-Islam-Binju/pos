@extends('admin.app')

@section('admin_content')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Raw Material Stock Transfers</h1>
        @can('raw-material-stock-transfer-create')
            <a href="{{ route('raw.material.stock.transfer.create') }}" class="btn btn-primary mt-2">Add New</a>
        @endcan
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
            <li class="breadcrumb-item active">Raw Material Stock Transfers</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @can('raw-material-stock-transfer-list')
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>From Warehouse</th>
                            <th>To Warehouse</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->fromWarehouse->name ?? '' }}</td>
                            <td>{{ $transfer->toWarehouse->name ?? '' }}</td>
                            <td class="text-capitalize">{{ $transfer->status ?? '' }}</td>
                            <td class="text-center">
                                <a href="{{ route('raw.material.stock.transfer.show', $transfer->id) }}" class="btn btn-info btn-sm px-1 py-0"><i class="fa fa-eye"></i></a>
                                @can('raw-material-stock-transfer-edit')
                                <a href="{{ route('raw.material.stock.transfer.edit', $transfer->id) }}" class="btn btn-warning btn-sm px-1 py-0"><i class="fa fa-pen"></i></a>
                                @endcan
                                @can('raw-material-stock-transfer-delete')
                                <button type="button" class="btn btn-danger btn-sm px-1 py-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transfer->id }}"><i class="fa fa-trash"></i></button>
                                @endcan

                                {{-- Status Change Buttons --}}
                                @if($transfer->status === 'pending')
                                    <form class="d-inline" action="{{ route('raw.material.stock.transfer.update.status', $transfer) }}" method="GET">
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-success btn-sm px-1 py-0"><i class="fa fa-check"></i></button>
                                    </form>
                                    <form class="d-inline" action="{{ route('raw.material.stock.transfer.update.status', $transfer) }}" method="GET">
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-danger btn-sm px-1 py-0"><i class="fa fa-times"></i></button>
                                    </form>
                                @elseif($transfer->status === 'rejected' || $transfer->status === 'approved')
                                    <form class="d-inline" action="{{ route('raw.material.stock.transfer.update.status', $transfer) }}" method="GET">
                                        <input type="hidden" name="status" value="pending">
                                        <button class="btn btn-info btn-sm px-1 py-0"><i class="fa fa-arrow-left"></i></button>
                                    </form>
                                @endif
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
                                        <form action="{{ route('raw.material.stock.transfer.destroy', $transfer->id) }}" method="POST">
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
        @endcan
    </div>
</div>
@endsection
