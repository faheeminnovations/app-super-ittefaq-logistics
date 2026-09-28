@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Financial Management</div>
            <h1>Financial Income</h1>
            <div class="sub">Income and expense tracking</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="printTable()"><i class="bi bi-printer me-1"></i> Print</button>
            <button class="btn btn-outline-navy" onclick="exportData()"><i class="bi bi-download me-1"></i> Export</button>
            <button class="btn btn-navy" onclick="window.location.href='{{ route('financial-income.create') }}'"><i class="bi bi-plus-lg me-1"></i> New Financial Income</button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#E8F5E9;color:var(--success);"><i class="bi bi-currency-dollar"></i></div>
                <div class="label">Total Income</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalIncome ?? 0) }}</div>
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
                <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-fuel-pump"></i></div>
                <div class="label">Total Fuel</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalFuel ?? 0) }}</div>
                <div class="delta down"><i class="bi bi-arrow-down-short"></i> Fuel</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#E3F2FD;color:var(--primary);"><i class="bi bi-utensils"></i></div>
                <div class="label">Total Food</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalFood ?? 0) }}</div>
                <div class="delta down"><i class="bi bi-arrow-down-short"></i> Food</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#F3E5F5;color:#9C27B0;"><i class="bi bi-toll"></i></div>
                <div class="label">Total Toll</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalToll ?? 0) }}</div>
                <div class="delta down"><i class="bi bi-arrow-down-short"></i> Toll</div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="stat-card">
                <div class="icon-badge" style="background:#E0F7FA;color:#00BCD4;"><i class="bi bi-person-badge"></i></div>
                <div class="label">Driver Payment</div>
                <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalDriverPayment ?? 0) }}</div>
                <div class="delta down"><i class="bi bi-arrow-down-short"></i> Payment</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="icon-badge" style="background:rgba(255,255,255,0.2);color:white;"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="label" style="color: rgba(255,255,255,0.8);">Net Profit</div>
                <div class="value" style="color: white;">{{ \App\Helpers\CurrencyHelper::formatCurrency($netProfit ?? 0) }}</div>
                <div class="delta" style="color: rgba(255,255,255,0.8);"><i class="bi bi-arrow-up-short"></i> Total Profit/Loss</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" class="form-control" id="filter_date_from" value="{{ request()->query('date_from') }}" onchange="filterData()">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" class="form-control" id="filter_date_to" value="{{ request()->query('date_to') }}" onchange="filterData()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Vehicle</label>
                    <select class="form-select" id="filter_vehicle" onchange="filterData()">
                        <option value="">All Vehicles</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle }}" {{ request()->query('vehicle') == $vehicle ? 'selected' : '' }}>{{ $vehicle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Driver</label>
                    <select class="form-select" id="filter_driver" onchange="filterData()">
                        <option value="">All Drivers</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver }}" {{ request()->query('driver') == $driver ? 'selected' : '' }}>{{ $driver }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filter_status" onchange="filterData()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request()->query('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request()->query('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request()->query('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Income Table -->
    <div class="panel">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="panel-title mb-0">Financial Income Records</div>
                <div class="panel-sub mb-0">Income and expense tracking with detailed breakdown</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped tbl" id="financialIncomeTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Income</th>
                        <th>Vehicle</th>
                        <th>Driver</th>
                        <th>Expense</th>
                        <th>Expense Category</th>
                        <th>Loading Point</th>
                        <th>Uploading Point</th>
                        <th>Fuel</th>
                        <th>Food</th>
                        <th>Toll</th>
                        <th>Driver Payment</th>
                        <th>Status</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($financialIncomes as $income)
                    <tr>
                        <td>{{ $income->date ? $income->date->format('d M Y') : 'N/A' }}</td>
                        <td><strong class="text-success">{{ \App\Helpers\CurrencyHelper::formatCurrency($income->total_income) }}</strong></td>
                        <td>{{ $income->vehicle ?? '-' }}</td>
                        <td>{{ $income->driver ?? '-' }}</td>
                        <td><strong class="text-danger">{{ \App\Helpers\CurrencyHelper::formatCurrency($income->expense ?? 0) }}</strong></td>
                        <td>{{ $income->expense_category ?? '-' }}</td>
                        <td>{{ $income->loading_point ?? '-' }}</td>
                        <td>{{ $income->uploading_point ?? '-' }}</td>
                        <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($income->fuel ?? 0) }}</td>
                        <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($income->food ?? 0) }}</td>
                        <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($income->toll ?? 0) }}</td>
                        <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($income->driver_payment ?? 0) }}</td>
                        <td>
                            @switch($income->status)
                                @case('pending')
                                    <span class="badge-status badge-pending">Pending</span>
                                    @break
                                @case('completed')
                                    <span class="badge-status badge-success">Completed</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge-status badge-cancelled">Cancelled</span>
                                    @break
                                @default
                                    <span class="badge-status badge-pending">{{ ucfirst($income->status) }}</span>
                            @endswitch
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" onclick="editRecord({{ $income->id }})" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="viewRecord({{ $income->id }})" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteRecord({{ $income->id }})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="no-data-row" style="display:none;"><td colspan="14" class="text-center">No financial income records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">Showing {{ $financialIncomes->firstItem() }} to {{ $financialIncomes->lastItem() }} of {{ $financialIncomes->total() }} entries</div>
            {{ $financialIncomes->links() }}
        </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
        &copy; 2026 Super Ittefaq Logistics &middot; Financial Management System
    </div>
</div>

@endsection

@push('scripts')
<style>
.no-print {
    display: table-cell !important;
}

@media print {
    .no-print {
        display: none !important;
    }
}
</style>
<script>
function filterData() {
    const dateFrom = document.getElementById('filter_date_from').value;
    const dateTo = document.getElementById('filter_date_to').value;
    const vehicle = document.getElementById('filter_vehicle').value;
    const driver = document.getElementById('filter_driver').value;
    const status = document.getElementById('filter_status').value;

    let params = [];
    if (dateFrom) params.push(`date_from=${dateFrom}`);
    if (dateTo) params.push(`date_to=${dateTo}`);
    if (vehicle) params.push(`vehicle=${vehicle}`);
    if (driver) params.push(`driver=${driver}`);
    if (status) params.push(`status=${status}`);

    let url = '{{ route('financial-income.index') }}';
    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    window.location.href = url;
}

function editRecord(id) {
    window.location.href = `/financial-income/${id}/edit`;
}

function viewRecord(id) {
    window.location.href = `/financial-income/${id}`;
}

function deleteRecord(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this financial income record!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/financial-income/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Financial income record has been deleted.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error deleting financial income record';
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

function printTable() {
    const table = document.getElementById('financialIncomeTable');
    const dateFrom = document.getElementById('filter_date_from').value;
    const dateTo = document.getElementById('filter_date_to').value;

    // Clone the table and remove the Actions column
    const tableClone = table.cloneNode(true);
    const headerRow = tableClone.querySelector('thead tr');
    if (headerRow) {
        headerRow.removeChild(headerRow.lastElementChild);
    }

    const bodyRows = tableClone.querySelectorAll('tbody tr');
    bodyRows.forEach(row => {
        if (row.lastElementChild) {
            row.removeChild(row.lastElementChild);
        }
    });

    const printContent = `
        <html>
        <head>
            <title>Financial Income Report</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { text-align: center; margin-bottom: 10px; }
                h2 { text-align: center; margin-bottom: 20px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f4f4f4; font-weight: bold; }
                .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #666; }
                @media print { body { -webkit-print-color-adjust: exact; } }
            </style>
        </head>
        <body>
            <h1>SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY</h1>
            <h2>Financial Income Report - ${dateFrom && dateTo ? formatDate(dateFrom) + ' to ' + formatDate(dateTo) : 'All Time'}</h2>
            ${tableClone.outerHTML}
            <div class="footer">
                Generated on ${new Date().toLocaleString()}<br>
                Super Ittefaq Logistics & Financial Management System
            </div>
        </body>
        </html>
    `;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

function exportData() {
    const dateFrom = document.getElementById('filter_date_from').value;
    const dateTo = document.getElementById('filter_date_to').value;
    const vehicle = document.getElementById('filter_vehicle').value;
    const driver = document.getElementById('filter_driver').value;
    const status = document.getElementById('filter_status').value;

    let params = [];
    if (dateFrom) params.push(`date_from=${dateFrom}`);
    if (dateTo) params.push(`date_to=${dateTo}`);
    if (vehicle) params.push(`vehicle=${vehicle}`);
    if (driver) params.push(`driver=${driver}`);
    if (status) params.push(`status=${status}`);

    let url = '/financial-income/export';
    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    window.location.href = url;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>
@endpush