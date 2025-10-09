@extends('admin.app')

@section('admin_content')
<div class="row mb-3">
    <div class="col-md-6">
        <h1 class="h3">Transfer Details - {{ \Carbon\Carbon::parse($transfer->date)->format('F j, Y') }}</h1>
    </div>
    <div class="col-md-6 text-end">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-end mb-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('raw.material.stock.transfer.section') }}">Raw Material Stock Transfers</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Transfer</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                {{-- Transfer Info --}}
                <table class="table table-bordered table-striped mb-4">
                    <tbody>
                        <tr>
                            <th style="width:30%">From Warehouse</th>
                            <td>{{ $transfer->fromWarehouse->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>To Warehouse</th>
                            <td>{{ $transfer->toWarehouse->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ \Carbon\Carbon::parse($transfer->date)->format('l, d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Details</th>
                            <td>{{ $transfer->note ? strip_tags($transfer->note) : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Admin</th>
                            <td>{{ $transfer->admin->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge
                                    @if($transfer->status == 'pending') bg-warning
                                    @elseif($transfer->status == 'approved') bg-success
                                    @elseif($transfer->status == 'rejected') bg-danger
                                    @endif text-capitalize">
                                    {{ $transfer->status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                {{-- Raw Materials Table --}}
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Raw Material Name</th>
                                <th class="text-end">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfer->rawMaterialStocks as $materialStock)
                                <tr>
                                    <td>{{ $materialStock->raw_material->name }}</td>
                                    <td class="text-end">{{ $materialStock->pivot->quantity }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No raw materials found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('raw.material.stock.transfer.section') }}" class="btn btn-success">Go Back</a>

                    @can('raw-material-stock-transfer-edit')
                    <a href="{{ route('raw.material.stock.transfer.edit', $transfer->id) }}" class="btn btn-warning">
                        <i class="fa fa-pen"></i> Edit
                    </a>
                    @endcan

                    {{-- Status Change --}}
                    @if($transfer->status === 'pending')
                        <form class="d-inline" action="{{ route('raw.material.stock.transfer.update.status', $transfer) }}" method="GET">
                            <input type="hidden" name="status" value="approved">
                            <button class="btn btn-success"><i class="fa fa-check"></i> Approve</button>
                        </form>
                        <form class="d-inline" action="{{ route('raw.material.stock.transfer.update.status', $transfer) }}" method="GET">
                            <input type="hidden" name="status" value="rejected">
                            <button class="btn btn-danger"><i class="fa fa-times"></i> Reject</button>
                        </form>
                    @elseif($transfer->status === 'rejected' || $transfer->status === 'approved')
                        <form class="d-inline" action="{{ route('raw.material.stock.transfer.update.status', $transfer) }}" method="GET">
                            <input type="hidden" name="status" value="pending">
                            <button class="btn btn-info"><i class="fa fa-arrow-left"></i> Pending</button>
                        </form>
                    @endif

                    {{-- Delete Modal Trigger --}}
                    @can('raw-material-stock-transfer-delete')
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $transfer->id }}">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    @endcan
                </div>

                {{-- Delete Modal --}}
                <div id="deleteModal{{ $transfer->id }}" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel{{ $transfer->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Delete Transfer</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this transfer?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <form action="{{ route('raw.material.stock.transfer.destroy', $transfer->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- card-body --}}
        </div> {{-- card --}}
    </div> {{-- col-12 --}}
</div> {{-- row --}}
@endsection
