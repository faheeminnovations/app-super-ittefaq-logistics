@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Transport Management</div>
            <h1>Trip Operations</h1>
            <div class="sub">Combined trip management system</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="exportTrips()"><i class="bi bi-download me-1"></i> Export</button>
            <button class="btn btn-navy" onclick="window.location.href='{{ route('trip-operations.create') }}'"><i class="bi bi-plus-lg me-1"></i> New Trip Operation</button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-truck"></i></div>
                <div class="label">Total Trips</div>
                <div class="value">{{ $tripOperations->total() }}</div>
                <div class="delta up"><i class="bi bi-arrow-up-short"></i> All time</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
                <div class="label">Completed</div>
                <div class="value">{{ $tripOperations->where('status', 'completed')->count() }}</div>
                <div class="delta up"><i class="bi bi-arrow-up-short"></i> Finished trips</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#E8F5E9;color:var(--success);"><i class="bi bi-currency-dollar"></i></div>
                <div class="label">Total Income</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalIncome ?? ($totalFreight ?? 0)) }}</div>
                <div class="delta up"><i class="bi bi-arrow-up-short"></i> Revenue</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#FFEBEE;color:var(--danger);"><i class="bi bi-currency-dollar"></i></div>
                <div class="label">Total Expense</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalExpense ?? 0) }}</div>
                <div class="delta down"><i class="bi bi-arrow-down-short"></i> Costs</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#E3F2FD;color:var(--primary);"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="label">Net Amount</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalNetAmount ?? 0) }}</div>
                <div class="delta up"><i class="bi bi-arrow-up-short"></i> Profit/Loss</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-hourglass-split"></i></div>
                <div class="label">In Progress</div>
                <div class="value">{{ $tripOperations->where('status', 'in_progress')->count() }}</div>
                <div class="delta down"><i class="bi bi-arrow-down-short"></i> Active trips</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select class="form-select" id="filter_month" onchange="filterTrips()">
                        <option value="">All Months</option>
                        @foreach($months as $key => $month)
                            <option value="{{ $key }}" {{ request()->query('month') == $key ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <input type="number" class="form-control" id="filter_year" value="{{ request()->query('year', date('Y')) }}" onchange="filterTrips()">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Warehouse Location</label>
                    <select class="form-select" id="filter_warehouse" onchange="filterTrips()">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->location }}" {{ request()->query('warehouse_location') == $warehouse->location ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Business Category</label>
                    <select class="form-select" id="filter_category" onchange="filterTrips()">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $category)
                            <option value="{{ $key }}" {{ request()->query('business_category') == $key ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filter_status" onchange="filterTrips()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request()->query('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request()->query('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request()->query('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="billed" {{ request()->query('status') == 'billed' ? 'selected' : '' }}>Billed</option>
                        <option value="cancelled" {{ request()->query('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Trip Operations Table -->
    <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="panel-title mb-0">Trip Operations</div>
                <div class="panel-sub mb-0">Combined warehouse and transport trip management</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped tbl" id="tripOperationsTable">
                <thead>
                    <tr>
                        <th>Trip #</th>
                        <th>Date</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Delivery Point</th>
                        <th>Warehouse</th>
                        <th>Category</th>
                        <th>KM</th>
                        <th>Freight</th>
                        <th>Total Income</th>
                        <th>Total Expense</th>
                        <th>Net Amount</th>
                        <th>Status</th>
                        <th>Wizard Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tripOperations as $trip)
                    <tr>
                        <td><span class='mono fw-semibold'>{{ $trip->trip_number }}</span></td>
                        <td>{{ $trip->trip_date ? $trip->trip_date->format('d M Y') : 'N/A' }}</td>
                        <td>{{ strtoupper($trip->vehicle_number) }}</td>
                        <td>{{ $trip->driver_name ?? '-' }}</td>
                        <td>{{ substr($trip->delivery_point, 0, 30) }}...</td>
                        <td>{{ $trip->warehouse_location ?? '-' }}</td>
                        <td>{{ $trip->business_category ?? '-' }}</td>
                        <td>{{ number_format($trip->kilometers, 2) }}</td>
                        <td><strong>{{ \App\Helpers\CurrencyHelper::formatCurrency($trip->freight) }}</strong></td>
                        <td><strong class="text-success">{{ \App\Helpers\CurrencyHelper::formatCurrency($trip->total_income ?? $trip->freight) }}</strong></td>
                        <td><strong class="text-danger">{{ \App\Helpers\CurrencyHelper::formatCurrency($trip->total_expense ?? 0) }}</strong></td>
                        <td><strong class="{{ $trip->net_amount >= 0 ? 'text-primary' : 'text-danger' }}">{{ \App\Helpers\CurrencyHelper::formatCurrency($trip->net_amount ?? ($trip->total_income - $trip->total_expense)) }}</strong></td>
                        <td>
                            @switch($trip->status)
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
                                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $trip->status)) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="progress" style="height: 6px; background-color: #EDEFF5;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $trip->wizard_progress ?? 0 }}%; background-color: var(--navy-800);"
                                     aria-valuenow="{{ $trip->wizard_progress ?? 0 }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                            <small>{{ number_format($trip->wizard_progress ?? 0) }}%</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" onclick="editTrip({{ $trip->id }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="viewTrip({{ $trip->id }})" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if($trip->current_wizard_step < 5)
                                <button type="button" class="btn btn-outline-success" onclick="continueWizard({{ $trip->id }})" title="Continue Wizard">
                                    <i class="bi bi-arrow-right-circle"></i>
                                </button>
                                @endif
                                <button type="button" class="btn btn-outline-danger" onclick="deleteTrip({{ $trip->id }})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="no-data-row" style="display:none;"><td colspan="15" class="text-center">No trip operations found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">Showing {{ $tripOperations->firstItem() }} to {{ $tripOperations->lastItem() }} of {{ $tripOperations->total() }} entries</div>
            {{ $tripOperations->links() }}
        </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
        &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // DataTables temporarily disabled due to column count issues
    // The table will work as a regular HTML table with server-side pagination
    console.log('DataTables disabled - using regular table');
});

function filterTrips() {
    const month = document.getElementById('filter_month').value;
    const year = document.getElementById('filter_year').value;
    const warehouse = document.getElementById('filter_warehouse').value;
    const category = document.getElementById('filter_category').value;
    const status = document.getElementById('filter_status').value;

    let params = [];
    if (month) params.push(`month=${month}`);
    if (year) params.push(`year=${year}`);
    if (warehouse) params.push(`warehouse_location=${warehouse}`);
    if (category) params.push(`business_category=${category}`);
    if (status) params.push(`status=${status}`);

    let url = '{{ route('trip-operations.index') }}';
    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    window.location.href = url;
}

function editTrip(id) {
    window.location.href = `/trip-operations/${id}/edit`;
}

function viewTrip(id) {
    window.location.href = `/trip-operations/${id}`;
}

function continueWizard(id) {
    window.location.href = `/trip-operations/${id}/wizard`;
}

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
                        location.reload();
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

function exportTrips() {
    // Implement export functionality
    alert('Export functionality will be implemented soon');
}
</script>
@endpush
