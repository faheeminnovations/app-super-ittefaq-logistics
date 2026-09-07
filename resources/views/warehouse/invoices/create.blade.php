@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">Create Warehouse Invoice</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('warehouse.invoices.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="invoice_date" class="form-label">Invoice Date *</label>
                                <input type="date" class="form-control" id="invoice_date" name="invoice_date" required
                                       value="{{ old('invoice_date', now()->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_month" class="form-label">Billing Month *</label>
                                <input type="text" class="form-control" id="billing_month" name="billing_month" required
                                       value="{{ old('billing_month', now()->format('F-Y')) }}" placeholder="e.g., May-2026">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="billing_year" class="form-label">Billing Year *</label>
                                <input type="number" class="form-control" id="billing_year" name="billing_year" required
                                       value="{{ old('billing_year', now()->year) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="warehouse_location" class="form-label">Warehouse Location *</label>
                                <input type="text" class="form-control" id="warehouse_location" name="warehouse_location" required
                                       value="{{ old('warehouse_location', 'Depalpur') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gl_number" class="form-label">GL Number *</label>
                                <input type="text" class="form-control" id="gl_number" name="gl_number" required
                                       value="{{ old('gl_number', '4022265') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_area" class="form-label">Business Area *</label>
                                <input type="text" class="form-control" id="business_area" name="business_area" required
                                       value="{{ old('business_area', '0900, Without any Cost Center.') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tax_rate" class="form-label">Tax Rate (%) *</label>
                                <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate" required
                                       value="{{ old('tax_rate', 16) }}">
                            </div>
                        </div>

                        <hr>

                        <h5 class="mb-3">Select Warehouse Trips</h5>
                        
                        @if($unbilledTrips->count() > 0)
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;">
                                                <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                            </th>
                                            <th>Trip #</th>
                                            <th>Date</th>
                                            <th>Vehicle</th>
                                            <th>Delivery Point</th>
                                            <th>KM</th>
                                            <th>Rate</th>
                                            <th>Freight</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unbilledTrips as $trip)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_trips[]" value="{{ $trip->id }}" class="trip-checkbox">
                                                </td>
                                                <td>{{ $trip->trip_number }}</td>
                                                <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
                                                <td>{{ $trip->vehicle_number }}</td>
                                                <td>{{ Str::limit($trip->delivery_point, 30) }}</td>
                                                <td>{{ number_format($trip->kilometers, 2) }}</td>
                                                <td>{{ number_format($trip->rate_per_km, 2) }}</td>
                                                <td><strong>{{ number_format($trip->freight, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="alert alert-info">
                                <strong>Total Freight:</strong> <span id="totalFreight">0.00</span>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                No unbilled warehouse trips available. Please create warehouse trips first.
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" @if($unbilledTrips->count() == 0) disabled @endif>
                                <i class="bi bi-save"></i> Create Invoice
                            </button>
                            <a href="{{ route('warehouse.invoices.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.trip-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    calculateTotal();
}

// Calculate total freight when checkboxes change
document.querySelectorAll('.trip-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', calculateTotal);
});

function calculateTotal() {
    const checkboxes = document.querySelectorAll('.trip-checkbox:checked');
    let total = 0;
    
    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const freight = parseFloat(row.querySelector('td:nth-child(9)').textContent.replace(/,/g, '')) || 0;
        total += freight;
    });
    
    document.getElementById('totalFreight').textContent = total.toFixed(2);
}
</script>
@endsection