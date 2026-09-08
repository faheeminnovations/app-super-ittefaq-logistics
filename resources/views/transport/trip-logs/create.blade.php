@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">Add New Trip Log</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('trip-logs.store') }}" method="POST" id="tripLogForm">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="date" name="date" required
                                       value="{{ old('date', now()->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="vehicle_no" class="form-label">Vhl No *</label>
                                <select class="form-select" id="vehicle_no" name="vehicle_no" required>
                                    <option value="">Vhl No Select Karein</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle }}" {{ old('vehicle_no') == $vehicle ? 'selected' : '' }}>
                                            {{ $vehicle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="gp_number" class="form-label">GP#</label>
                                <input type="text" class="form-control" id="gp_number" name="gp_number"
                                       value="{{ old('gp_number') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="business_category" class="form-label">Customer *</label>
                                <select class="form-select" id="business_category" name="business_category" required>
                                    <option value="">Customer Select Karein</option>
                                    @foreach($categories as $key => $category)
                                        <option value="{{ $key }}" {{ old('business_category') == $key ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="delivery_point" class="form-label">Drop/Delivery Point</label>
                                <textarea class="form-control" id="delivery_point" name="delivery_point" rows="2">{{ old('delivery_point') }}</textarea>
                                <small class="text-muted">Multiple locations ko comma se alag kiya ja sakta hai</small>
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
                                        <option value="{{ $category }}" {{ old('vehicle_category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="km" class="form-label">Km</label>
                                <input type="number" step="0.01" class="form-control" id="km" name="km"
                                       value="{{ old('km') }}" oninput="calculateFRT()">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="rate" class="form-label">Rate</label>
                                <input type="number" step="0.01" class="form-control" id="rate" name="rate"
                                       value="{{ old('rate') }}" oninput="calculateFRT()">
                                <small class="text-muted">Monthly rates se auto fill hoga</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="frt" class="form-label">FRT</label>
                                <input type="number" step="0.01" class="form-control" id="frt" name="frt" readonly
                                       value="{{ old('frt') }}">
                                <small class="text-muted">Auto calculate: Km × Rate</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="fuel" class="form-label">Fuel</label>
                                <input type="text" class="form-control" id="fuel" name="fuel"
                                       value="{{ old('fuel') }}">
                                <small class="text-muted">Amount, cash, ya null ho sakta hai</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="driver_name" class="form-label">Driver Name</label>
                                <select class="form-select" id="driver_name" name="driver_name">
                                    <option value="">Driver Select Karein</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver }}" {{ old('driver_name') == $driver ? 'selected' : '' }}>
                                            {{ $driver }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Category-specific fields -->
                        <div id="category-specific-fields">
                            <!-- Buyer Supply Chain: Serial No. / Date / Vehicle No. / Load ID / Gate Pass No. / Delivery Point / Vehicle Category / Cluster / Rate / Amount -->
                            <div class="category-fields d-none" data-category="Buyer Supply Chain">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="load_id" class="form-label">Load ID</label>
                                        <input type="text" class="form-control" id="load_id" name="load_id"
                                               value="{{ old('load_id') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="freight_bill_no" class="form-label">Freight Bill NO</label>
                                        <input type="text" class="form-control" id="freight_bill_no" name="freight_bill_no"
                                               value="{{ old('freight_bill_no') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="cluster" class="form-label">Cluster</label>
                                        <input type="text" class="form-control" id="cluster" name="cluster"
                                               value="{{ old('cluster') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Buyer Breading: Serial No. / Date / Delivery Point / Kilometers / Rate / Amount -->
                            <div class="category-fields d-none" data-category="Buyer Breading">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point"
                                               value="{{ old('loading_point') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point"
                                               value="{{ old('unloading_point') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Buyer Marketing Development: Same as Buyer Breading -->
                            <div class="category-fields d-none" data-category="Buyer Marketing Development">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point"
                                               value="{{ old('loading_point') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point"
                                               value="{{ old('unloading_point') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Buyer S.P.R: Same as Buyer Breading -->
                            <div class="category-fields d-none" data-category="Buyer S.P.R">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point"
                                               value="{{ old('loading_point') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point"
                                               value="{{ old('unloading_point') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Syngenta: Same as Buyer Breading -->
                            <div class="category-fields d-none" data-category="Syngenta">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point"
                                               value="{{ old('loading_point') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point"
                                               value="{{ old('unloading_point') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Syngenta Breading: Same as Buyer Breading -->
                            <div class="category-fields d-none" data-category="Syngenta Breading">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point"
                                               value="{{ old('loading_point') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point"
                                               value="{{ old('unloading_point') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Open Market Operations: Date / Vehicle No. / Serial No. / Driver Name / Customer Name / Loading Point / Unloading Point / Rent / Expenses -->
                            <div class="category-fields d-none" data-category="Open Market Work">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="driver_name" class="form-label">Driver Name</label>
                                        <input type="text" class="form-control" id="driver_name" name="driver_name"
                                               value="{{ old('driver_name') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="customer_name" class="form-label">Customer Name</label>
                                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                                               value="{{ old('customer_name') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="loading_point" class="form-label">Loading Point</label>
                                        <input type="text" class="form-control" id="loading_point" name="loading_point"
                                               value="{{ old('loading_point') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="unloading_point" class="form-label">Unloading Point</label>
                                        <input type="text" class="form-control" id="unloading_point" name="unloading_point"
                                               value="{{ old('unloading_point') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="rent_paid" class="form-label">Rent</label>
                                        <input type="number" step="0.01" class="form-control" id="rent_paid" name="rent_paid"
                                               value="{{ old('rent_paid') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="expenses" class="form-label">Expenses</label>
                                        <input type="number" step="0.01" class="form-control" id="expenses" name="expenses"
                                               value="{{ old('expenses') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Buyer Seed Supply: Date / Vehicle No. / Driver Name / Phone No. / Quantity / Delivery Point / Guarantor / Rent Paid / Payment Details / Receipt Details / Status -->
                            <div class="category-fields d-none" data-category="Buyer Seed Supply">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="driver_name" class="form-label">Driver Name</label>
                                        <input type="text" class="form-control" id="driver_name" name="driver_name"
                                               value="{{ old('driver_name') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="phone_number" class="form-label">Phone No.</label>
                                        <input type="text" class="form-control" id="phone_number" name="phone_number"
                                               value="{{ old('phone_number') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="quantity" class="form-label">Quantity</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                               value="{{ old('quantity') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="guarantor" class="form-label">Guarantor</label>
                                        <input type="text" class="form-control" id="guarantor" name="guarantor"
                                               value="{{ old('guarantor') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="rent_paid" class="form-label">Rent Paid</label>
                                        <input type="number" step="0.01" class="form-control" id="rent_paid" name="rent_paid"
                                               value="{{ old('rent_paid') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="payment_details" class="form-label">Payment Details</label>
                                        <textarea class="form-control" id="payment_details" name="payment_details" rows="2">{{ old('payment_details') }}</textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="receiving_details" class="form-label">Receipt Details</label>
                                        <textarea class="form-control" id="receiving_details" name="receiving_details" rows="2">{{ old('receiving_details') }}</textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="trip_status" class="form-label">Status</label>
                                        <select class="form-select" id="trip_status" name="trip_status">
                                            <option value="">Status Select Karein</option>
                                            @foreach($tripStatuses as $key => $status)
                                                <option value="{{ $key }}" {{ old('trip_status') == $key ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Trip Log
                            </button>
                            <a href="{{ route('trip-logs.index') }}" class="btn btn-secondary">
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

// Show/hide category-specific fields
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