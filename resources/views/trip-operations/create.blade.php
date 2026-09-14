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
                        <div class="step-label">Fuel & Expenses</div>
                    </div>
                    <div class="step {{ isset($tripOperation) && $tripOperation->current_wizard_step > 4 ? 'completed' : '' }} {{ isset($tripOperation) && $tripOperation->current_wizard_step == 4 ? 'active' : '' }}" data-step="4">
                        <div class="step-number">4</div>
                        <div class="step-label">Business Details</div>
                    </div>
                    <div class="step {{ isset($tripOperation) && $tripOperation->current_wizard_step > 5 ? 'completed' : '' }} {{ isset($tripOperation) && $tripOperation->current_wizard_step == 5 ? 'active' : '' }}" data-step="5">
                        <div class="step-number">5</div>
                        <div class="step-label">Final Step</div>
                    </div>
                </div>
            </div>

            <!-- Wizard Form -->
            <form id="wizardForm">
                <input type="hidden" name="trip_id" id="trip_id" value="{{ isset($tripOperation) ? $tripOperation->id : '' }}">
                <input type="hidden" name="step" id="currentStep" value="{{ isset($tripOperation) ? $tripOperation->current_wizard_step : 1 }}">

                <!-- Step 1: Basic Information -->
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

                <!-- Step 3: Fuel and Expenses -->
                <div class="wizard-step {{ isset($tripOperation) && $tripOperation->current_wizard_step == 3 ? 'active' : '' }}" data-step="3">
                    <h4 class="mb-3">Step 3: Fuel and Expenses</h4>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <input type="text" class="form-control" id="fuel_type" name="fuel_type" placeholder="e.g., Diesel, Petrol" value="{{ isset($tripOperation) ? $tripOperation->fuel_type : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fuel" class="form-label">Fuel</label>
                            <input type="text" class="form-control" id="fuel" name="fuel" placeholder="Amount, cash, or null" value="{{ isset($tripOperation) ? $tripOperation->fuel : '' }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="fuel_payment_type" class="form-label">Fuel Payment Type</label>
                            <select class="form-select" id="fuel_payment_type" name="fuel_payment_type">
                                <option value="">Select Type</option>
                                <option value="credit" {{ isset($tripOperation) && $tripOperation->fuel_payment_type == 'credit' ? 'selected' : '' }}>Credit</option>
                                <option value="cash" {{ isset($tripOperation) && $tripOperation->fuel_payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fuel_payment_amount" class="form-label">Fuel Payment Amount</label>
                            <input type="number" step="0.01" class="form-control" id="fuel_payment_amount" name="fuel_payment_amount" value="{{ isset($tripOperation) ? $tripOperation->fuel_payment_amount : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expenses" class="form-label">Expenses</label>
                            <input type="number" step="0.01" class="form-control" id="expenses" name="expenses" value="{{ isset($tripOperation) ? $tripOperation->expenses : '' }}">
                        </div>
                    </div>
                </div>

                <!-- Step 4: Business Details -->
                <div class="wizard-step {{ isset($tripOperation) && $tripOperation->current_wizard_step == 4 ? 'active' : '' }}" data-step="4">
                    <h4 class="mb-3">Step 4: Business Details</h4>
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
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->location }}" {{ isset($tripOperation) && $tripOperation->warehouse_location == $warehouse->location ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gl_number" class="form-label">GL Number</label>
                            <input type="text" class="form-control" id="gl_number" name="gl_number" value="{{ isset($tripOperation) ? $tripOperation->gl_number : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="load_id" class="form-label">Load ID</label>
                            <input type="text" class="form-control" id="load_id" name="load_id" value="{{ isset($tripOperation) ? $tripOperation->load_id : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="freight_bill_no" class="form-label">Freight Bill Number</label>
                            <input type="text" class="form-control" id="freight_bill_no" name="freight_bill_no" value="{{ isset($tripOperation) ? $tripOperation->freight_bill_no : '' }}">
                        </div>
                    </div>
                </div>

                <!-- Step 5: Final Information -->
                <div class="wizard-step {{ isset($tripOperation) && $tripOperation->current_wizard_step == 5 ? 'active' : '' }}" data-step="5">
                    <h4 class="mb-3">Step 5: Final Information</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="loading_point" class="form-label">Loading Point</label>
                            <input type="text" class="form-control" id="loading_point" name="loading_point" value="{{ isset($tripOperation) ? $tripOperation->loading_point : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="unloading_point" class="form-label">Unloading Point</label>
                            <input type="text" class="form-control" id="unloading_point" name="unloading_point" value="{{ isset($tripOperation) ? $tripOperation->unloading_point : '' }}">
                        </div>
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
                            <label for="amount_changed" class="form-label">Amount Changed</label>
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
const totalSteps = 5;

document.addEventListener('DOMContentLoaded', function() {
    // Set initial step based on existing trip operation
    @if(isset($tripOperation))
        currentStep = {{ $tripOperation->current_wizard_step > 5 ? 5 : $tripOperation->current_wizard_step }};
    @endif

    updateWizardUI();
});

function calculateFreight() {
    const km = parseFloat(document.getElementById('kilometers').value) || 0;
    const rate = parseFloat(document.getElementById('rate_per_km').value) || 0;
    const freight = km * rate;
    document.getElementById('freight').value = freight.toFixed(2);
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
    const progress = ((currentStep - 1) / totalSteps) * 100;
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
        const response = await fetch('/trip-operations/wizard-step', {
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

            if (data.is_complete || currentStep > 5) {
                // Wizard complete - redirect
                window.location.href = '/trip-operations';
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
        const response = await fetch(`/trip-operations/${tripId}/complete-wizard`, {
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
                window.location.href = '/trip-operations';
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
