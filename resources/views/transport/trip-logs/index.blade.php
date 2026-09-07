@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Trip Logs</h4>
                    <div>
                        <a href="{{ route('trip-logs.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Add New Trip
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($incompleteTrips > 0)
                        <div class="alert alert-warning mb-4">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Warning:</strong> There are {{ $incompleteTrips }} trip logs with missing rates or freight amounts. Please update them before generating invoices.
                        </div>
                    @endif
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('trip-logs.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <select name="month" class="form-select">
                                    <option value="">All Months</option>
                                    @foreach($months as $number => $name)
                                        <option value="{{ $number }}" {{ request('month') == $number ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="year" class="form-control" placeholder="Year" 
                                       value="{{ request('year', date('Y')) }}">
                            </div>
                            <div class="col-md-2">
                                <select name="category" class="form-select">
                                    <option value="">All Customers</option>
                                    @foreach($categories as $key => $category)
                                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="vehicle" class="form-select">
                                    <option value="">All Vehicles</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle }}" {{ request('vehicle') == $vehicle ? 'selected' : '' }}>
                                            {{ $vehicle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="driver" class="form-select">
                                    <option value="">All Drivers</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver }}" {{ request('driver') == $driver ? 'selected' : '' }}>
                                            {{ $driver }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-filter"></i>
                                </button>
                            </div>
                            <div class="col-md-1">
                                <a href="{{ route('trip-logs.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Trip Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr</th>
                                    <th>Date</th>
                                    <th>Vhl No</th>
                                    <th>GP#</th>
                                    <th>Drop/Delivery Point</th>
                                    <th>Vhl</th>
                                    <th>Km</th>
                                    <th>Rate</th>
                                    <th>FRT</th>
                                    <th>Driver</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tripLogs as $tripLog)
                                    <tr>
                                        <td>{{ $tripLog->sr }}</td>
                                        <td>{{ $tripLog->date->format('d/m/Y') }}</td>
                                        <td>{{ $tripLog->vehicle_no }}</td>
                                        <td>{{ $tripLog->gp_number ?? '-' }}</td>
                                        <td>{{ Str::limit($tripLog->delivery_point, 30) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $tripLog->vehicle_category }}</span>
                                        </td>
                                        <td>{{ number_format($tripLog->km, 2) }}</td>
                                        <td>{{ number_format($tripLog->rate, 2) }}</td>
                                        <td><strong>{{ number_format($tripLog->frt, 2) }}</strong></td>
                                        <td>{{ $tripLog->driver_name ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('trip-logs.show', $tripLog) }}" class="btn btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('trip-logs.edit', $tripLog) }}" class="btn btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="{{ route('trip-logs.print', $tripLog) }}" class="btn btn-secondary" target="_blank">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <form action="{{ route('trip-logs.destroy', $tripLog) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No trip logs found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($tripLogs->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted">
                                Showing {{ $tripLogs->firstItem() }} to {{ $tripLogs->lastItem() }} of {{ $tripLogs->total() }} entries
                            </span>
                            {{ $tripLogs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection