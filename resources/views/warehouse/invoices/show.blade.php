@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Warehouse Invoice #{{ $invoice->invoice_number }}</h4>
                    <div>
                        <a href="{{ route('warehouse.invoices.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        @if($invoice->status == 'draft')
                            <a href="{{ route('warehouse.invoices.edit', $invoice) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('warehouse.invoices.mark-sent', $invoice) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-send"></i> Mark as Sent
                            </a>
                        @endif
                        @if($invoice->status == 'sent')
                            <a href="{{ route('warehouse.invoices.mark-paid', $invoice) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-cash"></i> Mark as Paid
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Invoice Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Service Provider</h5>
                            <p><strong>Name:</strong> {{ $invoice->service_provider_name }}</p>
                            <p><strong>Address:</strong> {{ $invoice->service_provider_address }}</p>
                            <p><strong>NTN:</strong> {{ $invoice->service_provider_ntn }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Client Information</h5>
                            <p><strong>Name:</strong> {{ $invoice->client_name }}</p>
                            <p><strong>Address:</strong> {{ $invoice->client_address }}</p>
                            @if($invoice->client_ntn)
                                <p><strong>NTN:</strong> {{ $invoice->client_ntn }}</p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                            <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Billing Month:</strong> {{ $invoice->billing_month }}</p>
                            <p><strong>Warehouse:</strong> {{ $invoice->warehouse_location }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>GL Number:</strong> {{ $invoice->gl_number }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'sent' ? 'info' : ($invoice->status == 'draft' ? 'secondary' : 'warning')) }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <!-- Warehouse Trips -->
                    <h5 class="mb-3">Warehouse Trips</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Trip #</th>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th>GP #</th>
                                    <th>Delivery Point</th>
                                    <th>KM</th>
                                    <th>Rate</th>
                                    <th>Freight</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->warehouseTrips as $trip)
                                    <tr>
                                        <td>{{ $trip->trip_number }}</td>
                                        <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
                                        <td>{{ $trip->vehicle_number }}</td>
                                        <td>{{ $trip->gp_number ?? '-' }}</td>
                                        <td>{{ Str::limit($trip->delivery_point, 30) }}</td>
                                        <td>{{ number_format($trip->kilometers, 2) }}</td>
                                        <td>{{ number_format($trip->rate_per_km, 2) }}</td>
                                        <td><strong>{{ number_format($trip->freight, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            No trips found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="7" class="text-end">Subtotal:</th>
                                    <th>{{ number_format($invoice->subtotal, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="7" class="text-end">Tax ({{ $invoice->tax_rate }}%):</th>
                                    <th>{{ number_format($invoice->tax_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="7" class="text-end">Total:</th>
                                    <th><strong>{{ number_format($invoice->total_amount, 2) }}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($invoice->notes)
                        <div class="mb-4">
                            <h5>Notes</h5>
                            <p>{{ $invoice->notes }}</p>
                        </div>
                    @endif

                    @if($invoice->verified_by)
                        <div class="alert alert-info">
                            <strong>Verified by:</strong> {{ $invoice->verified_by }} on {{ $invoice->verified_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection