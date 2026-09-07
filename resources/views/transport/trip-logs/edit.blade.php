@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">Edit Trip Log #{{ $tripLog->sr }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('trip-logs.update', $tripLog) }}" method="POST" id="tripLogForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="date" name="date" required 
                                       value="{{ old('date', $tripLog->date->format('Y-m-d')) }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_no" class="form-label">Vehicle No *</label>
                                <select class="form-select" id="vehicle_no" name="vehicle_no" required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle }}" {{ old('vehicle_no', $tripLog->vehicle_no) == $vehicle ? 'selected' : '' }}>
                                            {{ $vehicle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="gp_number" class="form-label">GP# (Gate Pass Number)</label>
                                <input type="text" class="form-control" id="gp_number" name="gp_number" 
                                       value="{{ old('gp_number', $tripLog->gp_number) }}">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="business_category" class="form-label">Customer *</label>
                                <select class="form-select" id="business_category" name="business_category" required>
                                    <option value="">Select Customer</option>
                                    @foreach($categories as $key => $category)
                                        <option value="{{ $key }}" {{ old('business_category', $tripLog->business_category) == $key ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="delivery_point" class="form-label">Drop/Delivery Point</label>
                                <textarea class="form-control" id="delivery_point" name="delivery_point" rows="2">{{ old('delivery_point', $tripLog->delivery_point) }}</textarea>
                                <small class="text-muted">Multiple locations can be separated by commas</small>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <!-- Vehicle & Rate Information -->
                            <div class="col-md-4 mb-3">
                                <label for="vehicle_category" class="form-label">Vhl</label>
                                <select class="form-select" id="vehicle_category" name="vehicle_category">
                                    <option value="">Vhl Select Karein</option>
                                    @foreach($vehicleCategories as $category)
                                        <option value="{{ $category }}" {{ old('vehicle_category', $tripLog->vehicle_category) == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="km" class="form-label">Km</label>
                                <input type="number" step="0.01" class="form-control" id="km" name="km"
                                       value="{{ old('km', $tripLog->km) }}" oninput="calculateFRT()">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="number" step="0.01" class="form-control" id="rate" name="rate"
                                       value="{{ old('rate', $tripLog->rate) }}" oninput="calculateFRT()">
                                <small class="text-muted">Monthly rates se auto fill hoga</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="frt" class="form-label">FRT (Freight Amount)</label>
                                <input type="number" step="0.01" class="form-control" id="frt" name="frt" readonly 
                                       value="{{ old('frt', $tripLog->frt) }}">
                                <small class="text-muted">Auto-calculated: KM × Rate</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="fuel" class="form-label">Fuel</label>
                                <input type="text" class="form-control" id="fuel" name="fuel" 
                                       value="{{ old('fuel', $tripLog->fuel) }}">
                                <small class="text-muted">Can be amount, CASH, NILL, etc.</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="driver_name" class="form-label">Driver Name</label>
                                <select class="form-select" id="driver_name" name="driver_name">
                                    <option value="">Select Driver</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver }}" {{ old('driver_name', $tripLog->driver_name) == $driver ? 'selected' : '' }}>
                                            {{ $driver }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Category-specific fields -->
                        <div id="category-specific-fields">
                            <!-- Buyer Supply Chain -->
                            <div class="category-fields d-none" data-category="Buyer Supply Chain">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="load_id" class="form-label">Load ID</label>
                                        <input type="text" class="form-control" id="load_id" name="load_id" 
                                               value="{{ old('load_id', $tripLog->load_id) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cluster" class="form-label">Cluster</label>
                                        <input type="text" class="form-control" id="cluster" name="cluster" 
                                               value="{{ old('cluster', $tripLog->cluster) }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Open Market Work -->
                            <div class="category-fields d-none" data-category="Open Market Work">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                               value="{{ old('customer_name', $tripLog->customer_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point" 
                                               value="{{ old('loading_point', $tripLog->loading_point) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point" 
                                               value="{{ old('unloading_point', $tripLog->unloading_point) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rent" class="form-label">Rent</label>
                                        <input type="number" step="0.01" class="form-control" id="rent" name="rent" 
                                               value="{{ old('rent', $tripLog->rent) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="expenses" class="form-label">Expenses</label>
                                        <input type="number" step="0.01" class="form-control" id="expenses" name="expenses" 
                                               value="{{ old('expenses', $tripLog->expenses) }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Buyer Seed Supply -->
                            <div class="category-fields d-none" data-category="Buyer Seed Supply">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="phone_no" class="form-label">Phone No</label>
                                        <input type="text" class="form-control" id="phone_no" name="phone_no" 
                                               value="{{ old('phone_no', $tripLog->phone_no) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="text" class="form-control" id="quantity" name="quantity" 
                                               value="{{ old('quantity', $tripLog->quantity) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="guarantor" class="form-label">Guarantor</label>
                                        <input type="text" class="form-control" id="guarantor" name="guarantor" 
                                               value="{{ old('guarantor', $tripLog->guarantor) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rent" class="form-label">Rent Paid</label>
                                        <input type="number" step="0.01" class="form-control" id="rent" name="rent" 
                                               value="{{ old('rent', $tripLog->rent) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="payment_detail" class="form-label">Payment Detail</label>
                                        <input type="text" class="form-control" id="payment_detail" name="payment_detail" 
                                               value="{{ old('payment_detail', $tripLog->payment_detail) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="receiving_detail" class="form-label">Receiving Detail</label>
                                        <input type="text" class="form-control" id="receiving_detail" name="receiving_detail" 
                                               value="{{ old('receiving_detail', $tripLog->receiving_detail) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <input type="text" class="form-control" id="status" name="status" 
                                               value="{{ old('status', $tripLog->status) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Trip Log
                            </button>
                            <a href="{{ route('trip-logs.show', $tripLog) }}" class="btn btn-secondary">
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
function calculateFRT() {
    const km = parseFloat(document.getElementById('km').value) || 0;
    const rate = parseFloat(document.getElementById('rate').value) || 0;
    const frt = km * rate;
    document.getElementById('frt').value = frt.toFixed(2);
}

// Auto-fetch rate when vehicle category and date change
function fetchRate() {
    const category = document.getElementById('vehicle_category').value;
    const date = document.getElementById('date').value;
    
    if (category && date) {
        fetch(`{{ route('trip-logs.get-rate') }}?category=${category}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.rate) {
                    document.getElementById('rate').value = data.rate;
                    calculateFRT();
                }
            });
    }
}

// Show/hide category-specific fields and initialize with current category
document.addEventListener('DOMContentLoaded', function() {
    const selectedCategory = document.getElementById('business_category').value;
    
    // Show fields for selected category
    const specificFields = document.querySelector(`.category-fields[data-category="${selectedCategory}"]`);
    if (specificFields) {
        specificFields.classList.remove('d-none');
    }
});

document.getElementById('business_category').addEventListener('change', function() {
    const selectedCategory = this.value;
    
    // Hide all category-specific fields
    document.querySelectorAll('.category-fields').forEach(field => {
        field.classList.add('d-none');
    });
    
    // Show fields for selected category
    const specificFields = document.querySelector(`.category-fields[data-category="${selectedCategory}"]`);
    if (specificFields) {
        specificFields.classList.remove('d-none');
    }
});

// Add event listeners for auto-rate fetching
document.getElementById('vehicle_category').addEventListener('change', fetchRate);
document.getElementById('date').addEventListener('change', fetchRate);
</script>
@endsection