@extends('admin.app')
@section('admin_content')

<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4>Sell List</h4>
        @can('sell-create')
        <a href="{{ route('sells.create') }}" class="btn btn-primary">Create Sell</a>
        @endcan
    </div>
</div>

<table class="table table-bordered table-striped" id="sellTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Unique ID</th>
            <th>Customer</th>
            <th>Salesman</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sells as $sell)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $sell->unique_sale_id }}</td>
            <td>{{ $sell->customer->name ?? '-' }}</td>
            <td>{{ $sell->salesman->name ?? '-' }}</td>
            <td>{{ $sell->net_total }}</td>
            <td>{{ $sell->amount }}</td>
            <td>
                @if($sell->status == 'pending')
                    <span class="badge bg-warning">Pending</span>
                @elseif($sell->status == 'completed')
                    <span class="badge bg-success">Completed</span>
                @else
                    <span class="badge bg-danger">Cancelled</span>
                @endif
            </td>
            <td>{{ $sell->created_at->format('d M, Y') }}</td>
            <td>
                @can('sell-view')
                <a href="{{ route('sells.show', $sell->id) }}" class="btn btn-info btn-sm">View</a>
                @endcan

                @can('sell-edit')
                <a href="{{ route('sells.edit', $sell->id) }}" class="btn btn-primary btn-sm">Edit</a>
                <a href="{{ route('sells.update.status', [$sell->id, $sell->status == 'pending' ? 'completed' : 'pending']) }}" class="btn btn-warning btn-sm">Toggle Status</a>
                @endcan

                @can('sell-delete')
                <form action="{{ route('sells.destroy', $sell->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
                @endcan

                @can('sell-view')
                <a href="{{ route('sells.invoice', $sell->id) }}" target="_blank" class="btn btn-success btn-sm">Invoice</a>
                @endcan
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection

@section('scripts')
<script>
$(document).ready(function(){
    $('#sellTable').DataTable();
});
</script>
@endsection
