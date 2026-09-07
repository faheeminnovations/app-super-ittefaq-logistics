@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Transport Management Dashboard</h2>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">This Month's Trips</h6>
                            <h2 class="card-text">{{ $totalTrips }}</h2>
                        </div>
                        <i class="bi bi-truck fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total KM</h6>
                            <h2 class="card-text">{{ number_format($totalKm, 0) }}</h2>
                        </div>
                        <i class="bi bi-speedometer2 fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Freight</h6>
                            <h2 class="card-text">{{ number_format($totalFreight, 0) }}</h2>
                        </div>
                        <i class="bi bi-currency-rupee fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Avg. Rate/KM</h6>
                            <h2 class="card-text">{{ $totalKm > 0 ? number_format($totalFreight / $totalKm, 2) : '0.00' }}</h2>
                        </div>
                        <i class="bi bi-calculator fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fleet Overview -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Vehicle Overview</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total Active Vehicles:</span>
                        <span class="badge bg-primary">{{ $totalVehicles }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Trips</th>
                                    <th>Total KM</th>
                                    <th>Freight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicleBreakdown as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->vehicle_no }}</td>
                                        <td>{{ $vehicle->trip_count }}</td>
                                        <td>{{ number_format($vehicle->total_km, 0) }}</td>
                                        <td>{{ number_format($vehicle->total_freight, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No vehicle data for this month</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Driver Overview</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total Active Drivers:</span>
                        <span class="badge bg-success">{{ $totalDrivers }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Driver</th>
                                    <th>Trips</th>
                                    <th>Total Freight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($driverBreakdown as $driver)
                                    <tr>
                                        <td>{{ $driver->driver_name }}</td>
                                        <td>{{ $driver->trip_count }}</td>
                                        <td>{{ number_format($driver->total_freight, 0) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No driver data for this month</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Category Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Business Category Breakdown - {{ $currentMonth }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($categoryBreakdown as $category)
                            <div class="col-md-4 mb-3">
                                <div class="card border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $category->business_category }}</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Trips: <strong>{{ $category->trip_count }}</strong></span>
                                            <span>Freight: <strong>{{ number_format($category->total_freight, 0) }}</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center">No category data for this month</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Monthly Performance - 2026</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('trip-logs.create') }}" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Add New Trip
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('transport.rate-management') }}" class="btn btn-warning w-100">
                                <i class="bi bi-gear"></i> Manage Rates
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('transport.reports.vehicle') }}" class="btn btn-info w-100">
                                <i class="bi bi-truck"></i> Vehicle Reports
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('transport.reports.driver') }}" class="btn btn-success w-100">
                                <i class="bi bi-person"></i> Driver Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
const monthlyChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($monthlyTotals->pluck('month')->toArray()),
        datasets: [
            {
                label: 'Total Freight',
                data: @json($monthlyTotals->pluck('total_freight')->toArray()),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            },
            {
                label: 'Total KM',
                data: @json($monthlyTotals->pluck('total_km')->toArray()),
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Freight Amount'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Total KM'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});
</script>
@endsection
