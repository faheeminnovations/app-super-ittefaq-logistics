@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Financial Management</div>
            <h1>Financial Income Details</h1>
            <div class="sub">View income and expense record</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="window.location.href='{{ route('financial-income.index') }}'"><i class="bi bi-arrow-left me-1"></i> Back to List</button>
            <button class="btn btn-navy" onclick="window.location.href='{{ route('financial-income.edit', $financialIncome->id) }}'"><i class="bi bi-pencil me-1"></i> Edit</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Date</label>
                    <div>{{ $financialIncome->date ? $financialIncome->date->format('d M Y') : 'N/A' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Total Income</label>
                    <div class="text-success fw-bold">{{ \App\Helpers\CurrencyHelper::formatCurrency($financialIncome->total_income) }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Vehicle</label>
                    <div>{{ $financialIncome->vehicle ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Driver</label>
                    <div>{{ $financialIncome->driver ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Expense</label>
                    <div class="text-danger fw-bold">{{ \App\Helpers\CurrencyHelper::formatCurrency($financialIncome->expense ?? 0) }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Expense Category</label>
                    <div>{{ $financialIncome->expense_category ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Loading Point</label>
                    <div>{{ $financialIncome->loading_point ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Uploading Point</label>
                    <div>{{ $financialIncome->uploading_point ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Fuel</label>
                    <div>{{ \App\Helpers\CurrencyHelper::formatCurrency($financialIncome->fuel ?? 0) }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Food</label>
                    <div>{{ \App\Helpers\CurrencyHelper::formatCurrency($financialIncome->food ?? 0) }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Toll</label>
                    <div>{{ \App\Helpers\CurrencyHelper::formatCurrency($financialIncome->toll ?? 0) }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Driver Payment</label>
                    <div>{{ \App\Helpers\CurrencyHelper::formatCurrency($financialIncome->driver_payment ?? 0) }}</div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label fw-bold">Description</label>
                    <div>{{ $financialIncome->description ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <div>
                        @switch($financialIncome->status)
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
                                <span class="badge-status badge-pending">{{ ucfirst($financialIncome->status) }}</span>
                        @endswitch
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Created At</label>
                    <div>{{ $financialIncome->created_at ? $financialIncome->created_at->format('d M Y H:i') : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection