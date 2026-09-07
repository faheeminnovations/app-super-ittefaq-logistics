@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Company Header (Excel Format) -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-1">{{ $companySettings->company_name }}</h3>
                        <p class="mb-1">{{ $companySettings->address }}</p>
                        <p class="mb-1">Contact Detail: {{ $companySettings->contact_phone }}  {{ $companySettings->contact_email }}</p>
                        <p class="mb-1">NTN : {{ $companySettings->ntn }}              Vendor Code : {{ $companySettings->vendor_code }}</p>
                        <p class="mb-1">Invoice No : 0000                              Date : {{ $month }}</p>
                        <p class="fw-bold">Billing Month : {{ $month }}</p>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Trips</h5>
                            <h2 class="card-text">{{ $totalTrips }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total KM</h5>
                            <h2 class="card-text">{{ number_format($totalKm, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Freight</h5>
                            <h2 class="card-text">{{ number_format($totalFreight, 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Average Rate</h5>
                            <h2 class="card-text">{{ $totalTrips > 0 ? number_format($totalFreight / $totalKm, 2) : '0.00' }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trip Logs Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Monthly Trip Details - {{ $month }}</h4>
                    <div>
                        <a href="{{ route('transport.invoice', [$month, $year]) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel"></i> Export Invoice
                        </a>
                        <a href="{{ route('trip-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
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
                                    <th>FUEL</th>
                                    <th>DRIVER</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tripLogs as $tripLog)
                                    <tr>
                                        <td>{{ $tripLog->sr }}</td>
                                        <td>{{ $tripLog->date->format('d/m/Y') }}</td>
                                        <td>{{ $tripLog->vehicle_no }}</td>
                                        <td>{{ $tripLog->gp_number ?? '-' }}</td>
                                        <td>{{ $tripLog->delivery_point }}</td>
                                        <td>{{ $tripLog->vehicle_category }}</td>
                                        <td>{{ number_format($tripLog->km, 2) }}</td>
                                        <td>{{ number_format($tripLog->rate, 2) }}</td>
                                        <td><strong>{{ number_format($tripLog->frt, 2) }}</strong></td>
                                        <td>{{ $tripLog->fuel ?? '-' }}</td>
                                        <td>{{ $tripLog->driver_name ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <p class="text-muted">No trip logs found for this month</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="8" class="text-end fw-bold">TOTAL AMOUNT</td>
                                    <td class="fw-bold">{{ number_format($totalFreight, 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection