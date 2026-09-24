@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Transport Management</div>
            <h1>{{ isset($tripOperation) ? 'Continue Trip Operation' : 'New Trip Operation' }}</h1>
            <div class="sub">{{ isset($tripOperation) ? $tripOperation->trip_number : 'Create a new trip using wizard-based flow' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="window.location.href='{{ route('trip-operations.index') }}'"><i class="bi bi-arrow-left me-1"></i> Back to List</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Wizard Progress -->
            <div class="wizard-progress mb-4">
                <div class="progress" style="height: 8px; background-color: #EDEFF5;">
                    <div class="progress-bar" id="overallProgress" role="progressbar"
                         style="width: {{ isset($tripOperation) ? $tripOperation->wizard_progress : 0 }}%; background-color: var(--navy-800);"
                         aria-valuenow="{{ isset($tripOperation) ? $tripOperation->wizard_progress : 0 }}"
                         aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
                <div class="wizard-steps mt-3">
                    <div class="step {{ isset($tripOperation) && $tripOperation->current_wizard_step > 1 ? 'completed' : '' }} {{ !isset($tripOperation) || $tripOperation->current_wizard_step == 1 ? 'active' : '' }}" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Basic Info</div>
                    </div>
                    <div class="step {{ isset($tripOperation) && $tripOperation->current_wizard_step > 2 ? 'completed' : '' }} {{ isset($tripOperation) && $tripOperation->current_wizard_step == 2 ? 'active' : '' }}" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Distance & Rate</div>
                    </div>
                    <div class="step {{ isset($tripOperation) && $tripOperation->current_wizard_step > 3 ? 'completed' : '' }} {{ isset($tripOperation) && $tripOperation->current_wizard_step == 3 ? 'active' : '' }}" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Income & Expense</div>
                    </div>
                    <div class="step {{ isset($tripOperation) && $tripOperation->current_wizard_step > 4 ? 'completed' : '' }} {{ isset($tripOperation) && $tripOperation->current_wizard_step == 4 ? 'active' : '' }}" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-label">Final Step</div>
                    </div>
                </div>
            </div>

            <!-- Wizard Form -->
            <form id="wizardForm">
                <input type="hidden" name="trip_id" id="trip_id" value="{{ isset($tripOperation) ? $tripOperation->id : '' }}">
                <input type="hidden" name="step" id="currentStep" value="{{ isset($tripOperation) ? $tripOperation->current_wizard_step : 1 }}">

                <!-- Step 1: Basic Information + Business Details -->
                <div class="wizard-step {{ !isset($tripOperation) || $tripOperation->current_wizard_step == 1 ? 'active' : '' }}" data-step="1">
                    <h4 class="mb-3">Step 1: Basic Information</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="trip_date" class="form-label">Trip Date *</label>
                            <input type="date" class="form-control" id="trip_date" name="trip_date" required value="{{ isset($tripOperation) ? ($tripOperation->trip_date ? $tripOperation->trip_date->format('Y-m-d') : '') : old('trip_date', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_number" class="form-label">Vehicle Number</label>
                            @if($vehicles->isEmpty())
                                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" placeholder="Enter vehicle number manually" value="{{ isset($tripOperation) ? $tripOperation->vehicle_number : '' }}">
                                <small class="text-danger">No vehicles in database - enter manually</small>
                            @else
                                <select class="form-select" id="vehicle_number" name="vehicle_number">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $regNo => $vehicle)
                                        <option value="{{ $regNo }}" {{ isset($tripOperation) && $tripOperation->vehicle_number == $regNo ? 'selected' : '' }}>{{ strtoupper($regNo) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="driver_name" class="form-label">Driver Name</label>
                            <select class="form-select" id="driver_name" name="driver_name">
                                <option value="">Select Driver</option>
                                @foreach($drivers as $name => $driver)
                                    <option value="{{ $name }}" {{ isset($tripOperation) && $tripOperation->driver_name == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gp_number" class="form-label">Gate Pass Number</label>
                            <input type="text" class="form-control" id="gp_number" name="gp_number" placeholder="GP-001" value="{{ isset($tripOperation) ? $tripOperation->gp_number : '' }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="delivery_point" class="form-label">Delivery Point *</label>
                            <textarea class="form-control" id="delivery_point" name="delivery_point" rows="2" required placeholder="Enter delivery location(s)">{{ isset($tripOperation) ? $tripOperation->delivery_point : '' }}</textarea>
                            <small class="text-muted">Multiple locations can be separated by commas</small>
                        </div>
                    </div>

                    <h4 class="mb-3 mt-4">Business Details</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="business_category" class="form-label">Business Category *</label>
                            <select class="form-select" id="business_category" name="business_category" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $category)
                                    <option value="{{ $key }}" {{ isset($tripOperation) && $tripOperation->business_category == $key ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ isset($tripOperation) ? $tripOperation->customer_name : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="warehouse_location" class="form-label">Warehouse Location</label>
                            <select class="form-select" id="warehouse_location" name="warehouse_location">
                                <option value="">Select Warehouse</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gl_number" class="form-label">GL Number</label>
                            <input type="text" class="form-control" id="gl_number" name="gl_number" value="{{ isset($tripOperation) ? $tripOperation->gl_number : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gl_number" class="form-label">GL Number</label>
                            <input type="text" class="form-control" id="gl_number" name="gl_number" value="{{ isset($tripOperation) ? $tripOperation->gl_number : '' }}">
                        </div>
                        <div class="col-md-6 mb-3 d-none" id="load_id_container">
                            <label for="load_id" class="form-label">Load ID</label>
                            <input type="text" class="form-control" id="load_id" name="load_id" value="{{ isset($tripOperation) ? $tripOperation->load_id : '' }}">
                        </div>
                        <div class="col-md-6 mb-3 d-none" id="freight_bill_no_container">
                            <label for="freight_bill_no" class="form-label">Freight Bill Number</label>
                            <input type="text" class="form-control" id="freight_bill_no" name="freight_bill_no" value="{{ isset($tripOperation) ? $tripOperation->freight_bill_no : '' }}">
                        </div>
                        <div class="col-md-6 mb-3 d-none" id="loading_point_container">
                            <label for="loading_point" class="form-label">Loading Point</label>
                            <input type="text" class="form-control" id="loading_point" name="loading_point" value="{{ isset($tripOperation) ? $tripOperation->loading_point : '' }}">
                        </div>
                        <div class="col-md-6 mb-3 d-none" id="unloading_point_container">
                            <label for="unloading_point" class="form-label">Unloading Point</label>
                            <input type="text" class="form-control" id="unloading_point" name="unloading_point" value="{{ isset($tripOperation) ? $tripOperation->unloading_point : '' }}">
                        </div>
                    </div>
                </div>

                <!-- Step 2: Distance and Rate -->
                <div class="wizard-step {{ isset($tripOperation) && $tripOperation->current_wizard_step == 2 ? 'active' : '' }}" data-step="2">
                    <h4 class="mb-3">Step 2: Distance and Rate</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="vehicle_category" class="form-label">Vehicle Category</label>
                            <select class="form-select" id="vehicle_category" name="vehicle_category">
                                <option value="">Select Category</option>
                                @foreach($vehicleCategories as $category)
                                    <option value="{{ $category }}" {{ isset($tripOperation) && $tripOperation->vehicle_category == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <input type="text" class="form-control" id="vehicle_type" name="vehicle_type" placeholder="Additional vehicle type info" value="{{ isset($tripOperation) ? $tripOperation->vehicle_type : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kilometers" class="form-label">Kilometers *</label>
                            <input type="number" step="0.01" class="form-control" id="kilometers" name="kilometers" required oninput="calculateFreight()" value="{{ isset($tripOperation) ? $tripOperation->kilometers : '' }}" max="999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="rate_per_km" class="form-label">Rate per KM *</label>
                            <input type="number" step="0.01" class="form-control" id="rate_per_km" name="rate_per_km" required oninput="calculateFreight()" value="{{ isset($tripOperation) ? $tripOperation->rate_per_km : '' }}" max="999999999">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="freight" class="form-label">Calculated Freight</label>
                            <input type="number" step="0.01" class="form-control" id="freight" name="freight" readonly value="{{ isset($tripOperation) ? $tripOperation->freight : '' }}">
                            <small class="text-muted">Auto-calculated: KM × Rate</small>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Income and Expense -->
                <div class="wizard-step {{ isset($tripOperation) && $tripOperation->current_wizard_step == 3 ? 'active' : '' }}" data-step="3">
                    <h4 class="mb-3">Step 3: Income and Expense</h4>

                    <!-- Income Section -->
                    <div class="card mb-4" style="background-color: #f8f9fa; border-left: 4px solid #198754;">
                        <div class="card-body">
                            <h5 class="card-title text-success mb-3">Income Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="total_income" class="form-label fw-bold">Total Income (Freight)</label>
                                    <input type="number" step="0.01" class="form-control bg-light" id="total_income" name="total_income" readonly value="0.00" style="font-weight: bold; color: #198754;">
                                    <small class="text-muted">Auto-calculated from Step 2 (KM × Rate)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expense Section -->
                    <div class="card mb-4" style="background-color: #f8f9fa; border-left: 4px solid #dc3545;">
                        <div class="card-body">
                            <h5 class="card-title text-danger mb-3">Expense Details</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="total_expense" class="form-label fw-bold">Total Expense</label>
                                    <input type="number" step="0.01" class="form-control bg-light" id="total_expense" name="total_expense" readonly value="0.00" style="font-weight: bold; color: #dc3545;">
                                    <small class="text-muted">Auto-calculated from expense entries below</small>
                                </div>
                            </div>

                            <!-- Expense Entries Container -->
                            <div id="expenseEntriesContainer">
                                <div class="expense-entry row mb-3 p-3 border rounded" style="background-color: white;">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Expense Category</label>
                                        <select class="form-select expense-category" name="expense_entries[0][expense_category]" required>
                                            <option value="">Select Category</option>
                                            <option value="fuel">Fuel</option>
                                            <option value="toll">Toll</option>
                                            <option value="parking">Parking</option>
                                            <option value="driver_payment">Driver Payment</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="loading_charges">Loading Charges</option>
                                            <option value="unloading_charges">Unloading Charges</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Payment Type</label>
                                        <select class="form-select expense-payment-type" name="expense_entries[0][payment_type]">
                                            <option value="">Select Type</option>
                                            <option value="credit">Credit</option>
                                            <option value="cash">Cash</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Amount</label>
                                        <input type="number" step="0.01" class="form-control expense-amount" name="expense_entries[0][amount]" placeholder="0.00" oninput="calculateTotalExpense()">
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Description</label>
                                        <input type="text" class="form-control expense-description" name="expense_entries[0][description]" placeholder="Description">
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-danger d-none remove-expense-entry" onclick="removeExpenseEntry(this)">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="addExpenseEntry()">
                                <i class="bi bi-plus-circle"></i> Add Expense Entry
                            </button>
                        </div>
                    </div>

                    <!-- Net Amount Section -->
                    <div class="card" style="background-color: #f8f9fa; border-left: 4px solid #0d6efd;">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Net Amount / Profit</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="net_amount" class="form-label fw-bold">Net Amount (Profit/Loss)</label>
                                    <input type="number" step="0.01" class="form-control bg-light" id="net_amount" name="net_amount" readonly value="0.00" style="font-weight: bold; color: #0d6efd;">
                                    <small class="text-muted">Auto-calculated: Total Income − Total Expense</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Final Information -->
                <div class="wizard-step {{ isset($tripOperation) && $tripOperation->current_wizard_step == 4 ? 'active' : '' }}" data-step="4">
                    <h4 class="mb-3">Step 4: Final Information</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ isset($tripOperation) ? $tripOperation->phone_number : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ isset($tripOperation) ? $tripOperation->quantity : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="guarantor" class="form-label">Guarantor</label>
                            <input type="text" class="form-control" id="guarantor" name="guarantor" value="{{ isset($tripOperation) ? $tripOperation->guarantor : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="rent_paid" class="form-label">Rent Paid</label>
                            <input type="number" step="0.01" class="form-control" id="rent_paid" name="rent_paid" value="{{ isset($tripOperation) ? $tripOperation->rent_paid : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="initial_amount" class="form-label">Initial Amount</label>
                            <input type="number" step="0.01" class="form-control" id="initial_amount" name="initial_amount" value="{{ isset($tripOperation) ? $tripOperation->initial_amount : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="amount_changed" class="form-label">Amount Charged</label>
                            <input type="number" step="0.01" class="form-control" id="amount_changed" name="amount_changed" value="{{ isset($tripOperation) ? $tripOperation->amount_changed : '' }}">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="payment_details" class="form-label">Payment Details</label>
                            <textarea class="form-control" id="payment_details" name="payment_details" rows="2">{{ isset($tripOperation) ? $tripOperation->payment_details : '' }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="receiving_details" class="form-label">Receiving Details</label>
                            <textarea class="form-control" id="receiving_details" name="receiving_details" rows="2">{{ isset($tripOperation) ? $tripOperation->receiving_details : '' }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ isset($tripOperation) ? $tripOperation->notes : '' }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ isset($tripOperation) && $tripOperation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ isset($tripOperation) && $tripOperation->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ isset($tripOperation) && $tripOperation->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="billed" {{ isset($tripOperation) && $tripOperation->status == 'billed' ? 'selected' : '' }}>Billed</option>
                                <option value="cancelled" {{ isset($tripOperation) && $tripOperation->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Wizard Navigation -->
                <div class="wizard-navigation mt-4">
                    <button type="button" class="btn btn-secondary" id="prevBtn" onclick="previousStep()" disabled>Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">Next</button>
                    <button type="button" class="btn btn-success d-none" id="completeBtn" onclick="completeWizard()">Complete Trip Operation</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.wizard-progress {
    margin-bottom: 2rem;
}

.wizard-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
}

.wizard-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-bottom: 8px;
    transition: all 0.3s;
}

.step.active .step-number {
    background: #0d6efd;
    color: white;
}

.step.completed .step-number {
    background: #198754;
    color: white;
}

.step-label {
    font-size: 12px;
    color: #666;
    text-align: center;
}

.step.active .step-label {
    color: #0d6efd;
    font-weight: bold;
}

.wizard-step {
    display: none;
}

.wizard-step.active {
    display: block;
}

.wizard-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 4;

document.addEventListener('DOMContentLoaded', function() {
    // Set initial step based on existing trip operation
    @if(isset($tripOperation))
        currentStep = {{ $tripOperation->current_wizard_step > 4 ? 4 : $tripOperation->current_wizard_step }};
    @endif

    updateWizardUI();

    // Initialize total income and expense fields
    @if(isset($tripOperation))
        // Set total income from freight
        const freight = parseFloat(document.getElementById('freight').value) || 0;
        document.getElementById('total_income').value = freight.toFixed(2);

        // Load existing expense entries if available
        @if(isset($tripOperation->expenseEntries) && $tripOperation->expenseEntries->count() > 0)
            // Clear existing entries
            const container = document.getElementById('expenseEntriesContainer');
            container.innerHTML = '';

            // Add existing expense entries
            @foreach($tripOperation->expenseEntries as $index => $entry)
                const entry{{ $index }} = document.createElement('div');
                entry{{ $index }}.className = 'expense-entry row mb-3 p-3 border rounded';
                entry{{ $index }}.style.backgroundColor = 'white';
                entry{{ $index }}.innerHTML = `
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Expense Category</label>
                        <select class="form-select expense-category" name="expense_entries[{{ $index }}][expense_category]" required>
                            <option value="">Select Category</option>
                            <option value="fuel" {{ $entry->expense_category == 'fuel' ? 'selected' : '' }}>Fuel</option>
                            <option value="toll" {{ $entry->expense_category == 'toll' ? 'selected' : '' }}>Toll</option>
                            <option value="parking" {{ $entry->expense_category == 'parking' ? 'selected' : '' }}>Parking</option>
                            <option value="driver_payment" {{ $entry->expense_category == 'driver_payment' ? 'selected' : '' }}>Driver Payment</option>
                            <option value="maintenance" {{ $entry->expense_category == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="loading_charges" {{ $entry->expense_category == 'loading_charges' ? 'selected' : '' }}>Loading Charges</option>
                            <option value="unloading_charges" {{ $entry->expense_category == 'unloading_charges' ? 'selected' : '' }}>Unloading Charges</option>
                            <option value="other" {{ $entry->expense_category == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Payment Type</label>
                        <select class="form-select expense-payment-type" name="expense_entries[{{ $index }}][payment_type]">
                            <option value="">Select Type</option>
                            <option value="credit" {{ $entry->payment_type == 'credit' ? 'selected' : '' }}>Credit</option>
                            <option value="cash" {{ $entry->payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control expense-amount" name="expense_entries[{{ $index }}][amount]" placeholder="0.00" value="{{ $entry->amount }}" oninput="calculateTotalExpense()">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control expense-description" name="expense_entries[{{ $index }}][description]" placeholder="Description" value="{{ $entry->description }}">
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-sm btn-danger remove-expense-entry" onclick="removeExpenseEntry(this)">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                `;
                container.appendChild(entry{{ $index }});
            @endforeach

            // Update remove buttons
            updateRemoveButtons();
        @endif

        // Calculate total expense from loaded entries
        calculateTotalExpense();
    @else
        // For new trips, initialize from current values
        const initialFreight = parseFloat(document.getElementById('freight').value) || 0;
        document.getElementById('total_income').value = initialFreight.toFixed(2);
    @endif

    // Call the financial fields update function
    updateFinancialFields();

    // Initialize category-specific fields based on selected business category
    const businessCategory = document.getElementById('business_category');
    if (businessCategory && businessCategory.value) {
        const selectedCategory = businessCategory.value;

        // Hide all category-specific field containers
        document.getElementById('load_id_container').classList.add('d-none');
        document.getElementById('freight_bill_no_container').classList.add('d-none');
        document.getElementById('loading_point_container').classList.add('d-none');
        document.getElementById('unloading_point_container').classList.add('d-none');

        // Show fields based on selected category
        if (selectedCategory === 'Buyer Supply Chain') {
            document.getElementById('load_id_container').classList.remove('d-none');
            document.getElementById('freight_bill_no_container').classList.remove('d-none');
        } else if (['Buyer Breading', 'Buyer Marketing Development', 'Buyer S.P.R', 'Syngenta', 'Buyer Seed Supply'].includes(selectedCategory)) {
            document.getElementById('loading_point_container').classList.remove('d-none');
            document.getElementById('unloading_point_container').classList.remove('d-none');
        }

        // Initialize warehouse locations
        updateWarehouseLocations(selectedCategory);
    } else {
        // Initialize warehouse locations with default (empty or first category)
        updateWarehouseLocations('');
    }
});

function calculateFreight() {
    const km = parseFloat(document.getElementById('kilometers').value) || 0;
    const rate = parseFloat(document.getElementById('rate_per_km').value) || 0;
    const freight = km * rate;
    document.getElementById('freight').value = freight.toFixed(2);

    // Update total income field
    const totalIncomeField = document.getElementById('total_income');
    if (totalIncomeField) {
        totalIncomeField.value = freight.toFixed(2);
    }

    calculateNetAmount();
}

function calculateTotalExpense() {
    let totalExpense = 0;
    const expenseAmounts = document.querySelectorAll('.expense-amount');
    expenseAmounts.forEach(input => {
        totalExpense += parseFloat(input.value) || 0;
    });

    const totalExpenseField = document.getElementById('total_expense');
    if (totalExpenseField) {
        totalExpenseField.value = totalExpense.toFixed(2);
    }

    calculateNetAmount();
}

function calculateNetAmount() {
    const totalIncome = parseFloat(document.getElementById('total_income').value) || 0;
    const totalExpense = parseFloat(document.getElementById('total_expense').value) || 0;
    const netAmount = totalIncome - totalExpense;

    const netAmountField = document.getElementById('net_amount');
    if (netAmountField) {
        netAmountField.value = netAmount.toFixed(2);
        // Update color based on profit/loss
        if (netAmount >= 0) {
            netAmountField.style.color = '#198754'; // Green for profit
        } else {
            netAmountField.style.color = '#dc3545'; // Red for loss
        }
    }
}

function addExpenseEntry() {
    const container = document.getElementById('expenseEntriesContainer');
    const entries = container.querySelectorAll('.expense-entry');
    let maxIndex = 0;

    // Find the highest existing index
    entries.forEach(entry => {
        const select = entry.querySelector('.expense-category');
        if (select && select.name) {
            const match = select.name.match(/expense_entries\[(\d+)\]/);
            if (match) {
                const index = parseInt(match[1]);
                if (index > maxIndex) {
                    maxIndex = index;
                }
            }
        }
    });

    const newIndex = maxIndex + 1;
    const newEntry = document.createElement('div');
    newEntry.className = 'expense-entry row mb-3 p-3 border rounded';
    newEntry.style.backgroundColor = 'white';
    newEntry.innerHTML = `
        <div class="col-md-3 mb-2">
            <label class="form-label">Expense Category</label>
            <select class="form-select expense-category" name="expense_entries[${newIndex}][expense_category]" required>
                <option value="">Select Category</option>
                <option value="fuel">Fuel</option>
                <option value="toll">Toll</option>
                <option value="parking">Parking</option>
                <option value="driver_payment">Driver Payment</option>
                <option value="maintenance">Maintenance</option>
                <option value="loading_charges">Loading Charges</option>
                <option value="unloading_charges">Unloading Charges</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Payment Type</label>
            <select class="form-select expense-payment-type" name="expense_entries[${newIndex}][payment_type]">
                <option value="">Select Type</option>
                <option value="credit">Credit</option>
                <option value="cash">Cash</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" class="form-control expense-amount" name="expense_entries[${newIndex}][amount]" placeholder="0.00" oninput="calculateTotalExpense()">
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Description</label>
            <input type="text" class="form-control expense-description" name="expense_entries[${newIndex}][description]" placeholder="Description">
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-sm btn-danger remove-expense-entry" onclick="removeExpenseEntry(this)">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
    `;
    container.appendChild(newEntry);

    // Recalculate total expense
    calculateTotalExpense();

    // Show remove buttons for all entries if there are multiple
    updateRemoveButtons();
}

function removeExpenseEntry(button) {
    const entry = button.closest('.expense-entry');
    entry.remove();
    calculateTotalExpense();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const entries = document.querySelectorAll('.expense-entry');
    const removeButtons = document.querySelectorAll('.remove-expense-entry');

    // Show remove buttons only if there are multiple entries
    removeButtons.forEach(btn => {
        if (entries.length > 1) {
            btn.classList.remove('d-none');
        } else {
            btn.classList.add('d-none');
        }
    });
}

function updateTotalIncome() {
    const freight = parseFloat(document.getElementById('freight').value) || 0;
    document.getElementById('total_income').value = freight.toFixed(2);
}

// Ensure total_income and total_expense are always in sync
if (document.getElementById('freight')) {
    document.getElementById('freight').addEventListener('input', function() {
        const freight = parseFloat(this.value) || 0;
        const totalIncomeField = document.getElementById('total_income');
        if (totalIncomeField) {
            totalIncomeField.value = freight.toFixed(2);
        }
    });
}

if (document.getElementById('expenses')) {
    document.getElementById('expenses').addEventListener('input', function() {
        const expenses = parseFloat(this.value) || 0;
        const totalExpenseField = document.getElementById('total_expense');
        if (totalExpenseField) {
            totalExpenseField.value = expenses.toFixed(2);
        }
    });
}

// Function to update all financial fields when loading wizard data
function updateFinancialFields() {
    const freight = parseFloat(document.getElementById('freight').value) || 0;
    const expenses = parseFloat(document.getElementById('expenses').value) || 0;

    const totalIncomeField = document.getElementById('total_income');
    const totalExpenseField = document.getElementById('total_expense');

    if (totalIncomeField) {
        totalIncomeField.value = freight.toFixed(2);
    }
    if (totalExpenseField) {
        totalExpenseField.value = expenses.toFixed(2);
    }
}

// Show/hide category-specific fields
document.getElementById('business_category').addEventListener('change', function() {
    const selectedCategory = this.value;

    // Hide all category-specific field containers
    document.getElementById('load_id_container').classList.add('d-none');
    document.getElementById('freight_bill_no_container').classList.add('d-none');
    document.getElementById('loading_point_container').classList.add('d-none');
    document.getElementById('unloading_point_container').classList.add('d-none');

    // Show fields based on selected category
    if (selectedCategory === 'Buyer Supply Chain') {
        document.getElementById('load_id_container').classList.remove('d-none');
        document.getElementById('freight_bill_no_container').classList.remove('d-none');
    } else if (['Buyer Breading', 'Buyer Marketing Development', 'Buyer S.P.R', 'Syngenta', 'Buyer Seed Supply'].includes(selectedCategory)) {
        document.getElementById('loading_point_container').classList.remove('d-none');
        document.getElementById('unloading_point_container').classList.remove('d-none');
    }
    // Open Market Work shows no additional fields

    // Update warehouse locations based on business category
    updateWarehouseLocations(selectedCategory);
});

// Warehouse locations by business category
const warehouseLocationsByCategory = {
    'Buyer Supply Chain': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar'],
    'Buyer Breading': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar'],
    'Buyer Marketing Development': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar'],
    'Buyer S.P.R': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar'],
    'Syngenta': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar'],
    'Buyer Seed Supply': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar'],
    'Open Market Work': ['Depalpur', 'Multan', 'Sahiwal', 'Manga Mandi', 'Sundar']
};

function updateWarehouseLocations(category) {
    const warehouseSelect = document.getElementById('warehouse_location');
    const currentValue = warehouseSelect.value;

    // Clear existing options
    warehouseSelect.innerHTML = '<option value="">Select Warehouse</option>';

    // Get warehouse locations for the selected category
    const locations = warehouseLocationsByCategory[category] || [];

    // Add options
    locations.forEach(location => {
        const option = document.createElement('option');
        option.value = location;
        option.textContent = location;
        if (location === currentValue) {
            option.selected = true;
        }
        warehouseSelect.appendChild(option);
    });
}

function updateWizardUI() {
    // Update step indicators
    document.querySelectorAll('.step').forEach(step => {
        const stepNum = parseInt(step.dataset.step);
        step.classList.remove('active', 'completed');
        if (stepNum < currentStep) {
            step.classList.add('completed');
        } else if (stepNum === currentStep) {
            step.classList.add('active');
        }
    });

    // Update step visibility
    document.querySelectorAll('.wizard-step').forEach(step => {
        step.classList.remove('active');
        if (parseInt(step.dataset.step) === currentStep) {
            step.classList.add('active');
        }
    });

    // Update progress bar
    const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('overallProgress').style.width = progress + '%';
    document.getElementById('overallProgress').setAttribute('aria-valuenow', progress);

    // Update navigation buttons
    document.getElementById('prevBtn').disabled = currentStep === 1;
    document.getElementById('currentStep').value = currentStep;

    if (currentStep === totalSteps) {
        document.getElementById('nextBtn').classList.add('d-none');
        document.getElementById('completeBtn').classList.remove('d-none');
    } else {
        document.getElementById('nextBtn').classList.remove('d-none');
        document.getElementById('completeBtn').classList.add('d-none');
    }

    // Update financial fields when switching to step 3
    if (currentStep === 3) {
        updateFinancialFields();
    }
}

async function nextStep() {
    // Simple validation - just check required fields
    const currentStepElement = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
    const requiredFields = currentStepElement.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill in all required fields',
            confirmButtonColor: '#0d6efd'
        });
        return;
    }

    // Save current step data
    const formData = new FormData(document.getElementById('wizardForm'));
    formData.append('step', currentStep);

    try {
        const response = await fetch(window.location.origin + '/trip-operations/wizard-step', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById('trip_id').value = data.trip_id;
            currentStep = data.current_step;

            if (data.is_complete || currentStep > 4) {
                // Wizard complete - redirect
                window.location.href = window.location.origin + '/trip-operations';
            } else {
                updateWizardUI();
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#0d6efd'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error saving step data',
            confirmButtonColor: '#0d6efd'
        });
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateWizardUI();
    }
}

async function completeWizard() {
    const tripId = document.getElementById('trip_id').value;

    try {
        const response = await fetch(window.location.origin + `/trip-operations/${tripId}/complete-wizard`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Trip operation completed successfully!',
                confirmButtonColor: '#198754'
            }).then(() => {
                window.location.href = window.location.origin + '/trip-operations';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#0d6efd'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error completing wizard',
            confirmButtonColor: '#0d6efd'
        });
    }
}
</script>
@endpush
