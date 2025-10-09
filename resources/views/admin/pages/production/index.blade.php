@extends('admin.app')
@section('admin_content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">CoderNetix POS</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Production</a></li>
                    <li class="breadcrumb-item active">Production List</li>
                </ol>
            </div>
            <h4 class="page-title">Production List</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @can('production-create')
        <div class="mb-3">
            <a href="{{ route('production.create') }}" class="btn btn-success">Create Production</a>
        </div>
        @endcan

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="production-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Production House</th>
                            <th>Showroom</th>
                            <th>Account</th>
                            <th>Production Date</th>
                            <th>Total Cost</th>
                            <th>Raw Material Cost</th>
                            <th>Product Cost</th>
                            <th>Net Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productions as $production)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $production->productionHouse->name ?? 'N/A' }}</td>
                            <td>{{ $production->showroom->name ?? 'N/A' }}</td>
                            <td>{{ $production->account->name ?? 'N/A' }}</td>
                            <td>{{ $production->production_date->format('d-m-Y') }}</td>
                            <td>{{ number_format($production->total_cost, 2) }}</td>
                            <td>{{ number_format($production->total_raw_material_cost, 2) }}</td>
                            <td>{{ number_format($production->total_product_cost, 2) }}</td>
                            <td>{{ number_format($production->net_total, 2) }}</td>
                            <td>
                                @can('production-update-status')
                                <div class="dropdown">
                                    <button class="btn btn-{{ $production->status == 'approved' ? 'success' : ($production->status == 'pending' ? 'warning' : 'danger') }} btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        {{ ucfirst($production->status) }}
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('production.update.status', [$production->id, 'pending']) }}">Pending</a></li>
                                        <li><a class="dropdown-item" href="{{ route('production.update.status', [$production->id, 'approved']) }}">Approved</a></li>
                                        <li><a class="dropdown-item" href="{{ route('production.update.status', [$production->id, 'rejected']) }}">Rejected</a></li>
                                    </ul>
                                </div>
                                @else
                                    <span class="badge bg-{{ $production->status == 'approved' ? 'success' : ($production->status == 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($production->status) }}</span>
                                @endcan
                            </td>
                            <td>
                                <div class="btn-group">
                                    @can('production-view')
                                    <a href="{{ route('production.show', $production->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('production.print', $production->id) }}" target="_blank" class="btn btn-success btn-sm">Print</a>
                                    @endcan
                                    @can('production-edit')
                                    <a href="{{ route('production.edit', $production->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    @endcan
                                    @can('production-delete')
                                    <a href="{{ route('production.destroy', $production->id) }}" onclick="return confirm('Are you sure to delete this production?')" class="btn btn-danger btn-sm">Delete</a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#production-table').DataTable({
            responsive: true,
            pageLength: 10,
        });
    });
</script>
@endsection
