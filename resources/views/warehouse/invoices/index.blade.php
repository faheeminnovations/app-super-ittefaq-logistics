@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Warehouse Invoices</h4>
                    <div>
                        <a href="{{ route('warehouse.invoices.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create Invoice
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('warehouse.invoices.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="month" class="form-select">
                                    <option value="">All Months</option>
                                    @foreach($months as $number => $name)
                                        <option value="{{ $number }}" {{ request('month') == $number ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="year" class="form-control" placeholder="Year" 
                                       value="{{ request('year', date('Y')) }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('warehouse.invoices.index') }}" class="btn btn-secondary w-100">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Invoices Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Date</th>
                                    <th>Billing Month</th>
                                    <th>Warehouse</th>
                                    <th>Client</th>
                                    <th>Subtotal</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                        <td>{{ $invoice->billing_month }}</td>
                                        <td>{{ $invoice->warehouse_location }}</td>
                                        <td>{{ $invoice->client_name }}</td>
                                        <td>{{ number_format($invoice->subtotal, 2) }}</td>
                                        <td>{{ number_format($invoice->tax_amount, 2) }}</td>
                                        <td><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'sent' ? 'info' : ($invoice->status == 'draft' ? 'secondary' : 'warning')) }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('warehouse.invoices.show', $invoice) }}" class="btn btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($invoice->status == 'draft')
                                                    <a href="{{ route('warehouse.invoices.edit', $invoice) }}" class="btn btn-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('warehouse.invoices.destroy', $invoice) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($invoice->status == 'draft')
                                                    <a href="{{ route('warehouse.invoices.mark-sent', $invoice) }}" class="btn btn-success">
                                                        <i class="bi bi-send"></i>
                                                    </a>
                                                @endif
                                                @if($invoice->status == 'sent')
                                                    <a href="{{ route('warehouse.invoices.mark-paid', $invoice) }}" class="btn btn-success">
                                                        <i class="bi bi-cash"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No warehouse invoices found</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($invoices->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted">
                                Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} entries
                            </span>
                            {{ $invoices->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection