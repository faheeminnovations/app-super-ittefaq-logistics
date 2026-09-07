@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Driver-wise Report</h4>
                    <a href="{{ route('transport.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('transport.reports.driver') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="driver" class="form-select">
                                    <option value="">All Drivers</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver }}" {{ $driverName == $driver ? 'selected' : '' }}>
                                            {{ $driver }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="month" class="form-control" placeholder="Month (e.g. January-2026)" 
                                       value="{{ $month }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-filter"></i> Apply Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Trips</h6>
                                    <h3 class="card-text">{{ $totalTrips }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Freight</h6>
                                    <h3 class="card-text">{{ number_format($totalFreight, 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Fuel Entries</h6>
                                    <h3 class="card-text">{{ $totalFuel }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Trip Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sr</th>
                                    <th>Date</th>
                                    <th>Driver Name</th>
                                    <th>Vehicle No</th>
                                    <th>Delivery Point</th>
                                    <th>KM</th>
                                    <th>Rate</th>
                                    <th>FRT</th>
                                    <th>Fuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tripLogs as $tripLog)
                                    <tr>
                                        <td>{{ $tripLog->sr }}</td>
                                        <td>{{ $tripLog->date->format('d/m/Y') }}</td>
                                        <td>{{ $tripLog->driver_name ?? '-' }}</td>
                                        <td>{{ $tripLog->vehicle_no }}</td>
                                        <td>{{ Str::limit($tripLog->delivery_point, 30) }}</td>
                                        <td>{{ number_format($tripLog->km, 2) }}</td>
                                        <td>{{ number_format($tripLog->rate, 2) }}</td>
                                        <td><strong>{{ number_format($tripLog->frt, 2) }}</strong></td>
                                        <td>{{ $tripLog->fuel ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <p class="text-muted">No trip logs found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection