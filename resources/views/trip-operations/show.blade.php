@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Transport Management</div>
            <h1>Trip Operation Details</h1>
            <div class="sub">{{ $tripOperation->trip_number }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="window.location.href='{{ route('trip-operations.index') }}'"><i class="bi bi-arrow-left me-1"></i> Back to List</button>
            <button class="btn btn-primary" onclick="window.location.href='{{ route('trip-operations.edit', $tripOperation->id) }}'"><i class="bi bi-pencil me-1"></i> Edit</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Trip Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Trip Number</label>
                            <div class="fw-semibold">{{ $tripOperation->trip_number }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Trip Date</label>
                            <div class="fw-semibold">{{ $tripOperation->trip_date ? $tripOperation->trip_date->format('d M Y') : 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Vehicle Number</label>
                            <div class="fw-semibold">{{ strtoupper($tripOperation->vehicle_number) }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Driver Name</label>
                            <div class="fw-semibold">{{ $tripOperation->driver_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Gate Pass Number</label>
                            <div class="fw-semibold">{{ $tripOperation->gp_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Delivery Point</label>
                            <div class="fw-semibold">{{ $tripOperation->delivery_point }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Vehicle & Distance Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Vehicle Category</label>
                            <div class="fw-semibold">{{ $tripOperation->vehicle_category ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Vehicle Type</label>
                            <div class="fw-semibold">{{ $tripOperation->vehicle_type ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Kilometers</label>
                            <div class="fw-semibold">{{ number_format($tripOperation->kilometers, 2) }} km</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Rate per KM</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->rate_per_km) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Calculated Freight</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->freight) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Fuel & Expenses</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Fuel Type</label>
                            <div class="fw-semibold">{{ $tripOperation->fuel_type ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Fuel</label>
                            <div class="fw-semibold">{{ $tripOperation->fuel ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Fuel Payment Type</label>
                            <div class="fw-semibold">{{ ucfirst($tripOperation->fuel_payment_type ?? 'N/A') }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Fuel Payment Amount</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->fuel_payment_amount) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Expenses</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->expenses) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Business Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Business Category</label>
                            <div class="fw-semibold">{{ $tripOperation->business_category ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Customer Name</label>
                            <div class="fw-semibold">{{ $tripOperation->customer_name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Warehouse Location</label>
                            <div class="fw-semibold">{{ $tripOperation->warehouse_location ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">GL Number</label>
                            <div class="fw-semibold">{{ $tripOperation->gl_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Load ID</label>
                            <div class="fw-semibold">{{ $tripOperation->load_id ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Freight Bill Number</label>
                            <div class="fw-semibold">{{ $tripOperation->freight_bill_no ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Loading Point</label>
                            <div class="fw-semibold">{{ $tripOperation->loading_point ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Unloading Point</label>
                            <div class="fw-semibold">{{ $tripOperation->unloading_point ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Phone Number</label>
                            <div class="fw-semibold">{{ $tripOperation->phone_number ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Quantity</label>
                            <div class="fw-semibold">{{ $tripOperation->quantity }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Guarantor</label>
                            <div class="fw-semibold">{{ $tripOperation->guarantor ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Rent Paid</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->rent_paid) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Initial Amount</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->initial_amount) }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label text-muted">Amount Changed</label>
                            <div class="fw-semibold">{{ \App\Helpers\CurrencyHelper::formatCurrency($tripOperation->amount_changed) }}</div>
                        </div>
                    </div>
                    @if($tripOperation->payment_details)
                    <div class="mb-3">
                        <label class="form-label text-muted">Payment Details</label>
                        <div>{{ $tripOperation->payment_details }}</div>
                    </div>
                    @endif
                    @if($tripOperation->receiving_details)
                    <div class="mb-3">
                        <label class="form-label text-muted">Receiving Details</label>
                        <div>{{ $tripOperation->receiving_details }}</div>
                    </div>
                    @endif
                    @if($tripOperation->notes)
                    <div class="mb-3">
                        <label class="form-label text-muted">Notes</label>
                        <div>{{ $tripOperation->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Status & Progress</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <div>
                            @switch($tripOperation->status)
                                @case('pending')
                                    <span class="badge-status badge-pending">Pending</span>
                                    @break
                                @case('in_progress')
                                    <span class="badge-status badge-transit">In Progress</span>
                                    @break
                                @case('completed')
                                    <span class="badge-status badge-delivered">Completed</span>
                                    @break
                                @case('billed')
                                    <span class="badge-status badge-success">Billed</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge-status badge-cancelled">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $tripOperation->status)) }}</span>
                            @endswitch
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Wizard Progress</label>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $tripOperation->wizard_progress }}%;" aria-valuenow="{{ $tripOperation->wizard_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small>{{ number_format($tripOperation->wizard_progress) }}% Complete</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Billing Month</label>
                        <div class="fw-semibold">{{ $tripOperation->billing_month }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Serial Number</label>
                        <div class="fw-semibold">{{ $tripOperation->sr ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('trip-operations.edit', $tripOperation->id) }}'">
                            <i class="bi bi-pencil me-1"></i> Edit Trip
                        </button>
                        @if($tripOperation->current_wizard_step < 6)
                        <button class="btn btn-success" onclick="window.location.href='/trip-operations/{{ $tripOperation->id }}/wizard'">
                            <i class="bi bi-arrow-right-circle me-1"></i> Continue Wizard
                        </button>
                        @endif
                        <button class="btn btn-outline-danger" onclick="deleteTrip({{ $tripOperation->id }})">
                            <i class="bi bi-trash me-1"></i> Delete Trip
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deleteTrip(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this trip operation!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/trip-operations/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Trip operation has been deleted.',
                        'success'
                    ).then(() => {
                        window.location.href = '/trip-operations';
                    });
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error deleting trip operation';
                    Swal.fire(
                        'Error!',
                        message,
                        'error'
                    );
                }
            });
        }
    });
}
</script>
@endpush
