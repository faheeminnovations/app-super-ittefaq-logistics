@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Transport Management</div>
            <h1>Edit Trip Operation</h1>
            <div class="sub">{{ $tripOperation->trip_number }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="window.location.href='{{ route('trip-operations.index') }}'"><i class="bi bi-arrow-left me-1"></i> Back to List</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('trip-operations.update', $tripOperation->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6 mb-3">
                        <label for="trip_date" class="form-label">Trip Date *</label>
                        <input type="date" class="form-control" id="trip_date" name="trip_date" required value="{{ $tripOperation->trip_date ? $tripOperation->trip_date->format('Y-m-d') : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vehicle_number" class="form-label">Vehicle Number *</label>
                        <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required value="{{ $tripOperation->vehicle_number }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="driver_name" class="form-label">Driver Name</label>
                        <input type="text" class="form-control" id="driver_name" name="driver_name" value="{{ $tripOperation->driver_name }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gp_number" class="form-label">Gate Pass Number</label>
                        <input type="text" class="form-control" id="gp_number" name="gp_number" value="{{ $tripOperation->gp_number }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="delivery_point" class="form-label">Delivery Point *</label>
                        <textarea class="form-control" id="delivery_point" name="delivery_point" rows="2" required>{{ $tripOperation->delivery_point }}</textarea>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Vehicle & Distance -->
                    <div class="col-md-4 mb-3">
                        <label for="vehicle_category" class="form-label">Vehicle Category</label>
                        <select class="form-select" id="vehicle_category" name="vehicle_category">
                            <option value="">Select Category</option>
                            @foreach($vehicleCategories as $category)
                                <option value="{{ $category }}" {{ $tripOperation->vehicle_category == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="vehicle_type" class="form-label">Vehicle Type</label>
                        <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" value="{{ $tripOperation->vehicle_type }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="kilometers" class="form-label">Kilometers *</label>
                        <input type="number" step="0.01" class="form-control" id="kilometers" name="kilometers" required value="{{ $tripOperation->kilometers }}" oninput="calculateFreight()">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="rate_per_km" class="form-label">Rate per KM *</label>
                        <input type="number" step="0.01" class="form-control" id="rate_per_km" name="rate_per_km" required value="{{ $tripOperation->rate_per_km }}" oninput="calculateFreight()">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="freight" class="form-label">Calculated Freight</label>
                        <input type="number" step="0.01" class="form-control" id="freight" name="freight" readonly value="{{ $tripOperation->freight }}">
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Fuel & Expenses -->
                    <div class="col-md-4 mb-3">
                        <label for="fuel_type" class="form-label">Fuel Type</label>
                        <input type="text" class="form-control" id="fuel_type" name="fuel_type" placeholder="e.g., Petrol" value="{{ $tripOperation->fuel_type }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel" class="form-label">Fuel</label>
                        <input type="text" class="form-control" id="fuel" name="fuel" value="{{ $tripOperation->fuel }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel_payment_type" class="form-label">Fuel Payment Type</label>
                        <select class="form-select" id="fuel_payment_type" name="fuel_payment_type">
                            <option value="">Select Type</option>
                            <option value="credit" {{ $tripOperation->fuel_payment_type == 'credit' ? 'selected' : '' }}>Credit</option>
                            <option value="cash" {{ $tripOperation->fuel_payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="fuel_payment_amount" class="form-label">Fuel Payment Amount</label>
                        <input type="number" step="0.01" class="form-control" id="fuel_payment_amount" name="fuel_payment_amount" value="{{ $tripOperation->fuel_payment_amount }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="expenses" class="form-label">Expenses</label>
                        <input type="number" step="0.01" class="form-control" id="expenses" name="expenses" value="{{ $tripOperation->expenses }}">
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Business Details -->
                    <div class="col-md-6 mb-3">
                        <label for="business_category" class="form-label">Business Category *</label>
                        <select class="form-select" id="business_category" name="business_category" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $key => $category)
                                <option value="{{ $key }}" {{ $tripOperation->business_category == $key ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $tripOperation->customer_name }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="warehouse_location" class="form-label">Warehouse Location</label>
                        <select class="form-select" id="warehouse_location" name="warehouse_location">
                            <option value="">Select Warehouse</option>
                            <option value="Depalpur" {{ $tripOperation->warehouse_location == 'Depalpur' ? 'selected' : '' }}>Depalpur</option>
                            <option value="Multan" {{ $tripOperation->warehouse_location == 'Multan' ? 'selected' : '' }}>Multan</option>
                            <option value="Sahiwal" {{ $tripOperation->warehouse_location == 'Sahiwal' ? 'selected' : '' }}>Sahiwal</option>
                            <option value="Manga Mandi" {{ $tripOperation->warehouse_location == 'Manga Mandi' ? 'selected' : '' }}>Manga Mandi</option>
                            <option value="Sundar" {{ $tripOperation->warehouse_location == 'Sundar' ? 'selected' : '' }}>Sundar</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gl_number" class="form-label">GL Number</label>
                        <input type="text" class="form-control" id="gl_number" name="gl_number" value="{{ $tripOperation->gl_number }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="load_id" class="form-label">Load ID</label>
                        <input type="text" class="form-control" id="load_id" name="load_id" value="{{ $tripOperation->load_id }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="freight_bill_no" class="form-label">Freight Bill Number</label>
                        <input type="text" class="form-control" id="freight_bill_no" name="freight_bill_no" value="{{ $tripOperation->freight_bill_no }}">
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Additional Information -->
                    <div class="col-md-6 mb-3">
                        <label for="loading_point" class="form-label">Loading Point</label>
                        <input type="text" class="form-control" id="loading_point" name="loading_point" value="{{ $tripOperation->loading_point }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unloading_point" class="form-label">Unloading Point</label>
                        <input type="text" class="form-control" id="unloading_point" name="unloading_point" value="{{ $tripOperation->unloading_point }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $tripOperation->phone_number }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $tripOperation->quantity }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="guarantor" class="form-label">Guarantor</label>
                        <input type="text" class="form-control" id="guarantor" name="guarantor" value="{{ $tripOperation->guarantor }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rent_paid" class="form-label">Rent Paid</label>
                        <input type="number" step="0.01" class="form-control" id="rent_paid" name="rent_paid" value="{{ $tripOperation->rent_paid }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="initial_amount" class="form-label">Initial Amount</label>
                        <input type="number" step="0.01" class="form-control" id="initial_amount" name="initial_amount" value="{{ $tripOperation->initial_amount }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="amount_changed" class="form-label">Amount Charged</label>
                        <input type="number" step="0.01" class="form-control" id="amount_changed" name="amount_changed" value="{{ $tripOperation->amount_changed }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label for="payment_details" class="form-label">Payment Details</label>
                        <textarea class="form-control" id="payment_details" name="payment_details" rows="2">{{ $tripOperation->payment_details }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="receiving_details" class="form-label">Receiving Details</label>
                        <textarea class="form-control" id="receiving_details" name="receiving_details" rows="2">{{ $tripOperation->receiving_details }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ $tripOperation->notes }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $tripOperation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $tripOperation->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $tripOperation->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="billed" {{ $tripOperation->status == 'billed' ? 'selected' : '' }}>Billed</option>
                            <option value="cancelled" {{ $tripOperation->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Trip Operation
                    </button>
                    <a href="{{ route('trip-operations.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function calculateFreight() {
    const km = parseFloat(document.getElementById('kilometers').value) || 0;
    const rate = parseFloat(document.getElementById('rate_per_km').value) || 0;
    const freight = km * rate;
    document.getElementById('freight').value = freight.toFixed(2);
}
</script>
@endpush
