@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Operations Income Management</div>
            <h1>Create Operations Income</h1>
            <div class="sub">Add new income and expense record</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="window.location.href='{{ route('financial-income.index') }}'"><i class="bi bi-arrow-left me-1"></i> Back to List</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('financial-income.store') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="date" name="date" required value="{{ old('date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="total_income" class="form-label">Total Income *</label>
                        <input type="number" step="0.01" class="form-control" id="total_income" name="total_income" required value="{{ old('total_income') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vehicle" class="form-label">Vehicle</label>
                        <select class="form-select" id="vehicle" name="vehicle">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle }}" {{ old('vehicle') == $vehicle ? 'selected' : '' }}>{{ $vehicle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="driver" class="form-label">Driver</label>
                        <select class="form-select" id="driver" name="driver">
                            <option value="">Select Driver</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver }}" {{ old('driver') == $driver ? 'selected' : '' }}>{{ $driver }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Expense Information -->
                    <div class="col-md-4 mb-3">
                        <label for="expense" class="form-label">Expense</label>
                        <input type="number" step="0.01" class="form-control" id="expense" name="expense" value="{{ old('expense') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="expense_category" class="form-label">Expense Category</label>
                        <select class="form-select" id="expense_category" name="expense_category">
                            <option value="">Select Category</option>
                            @foreach($expenseCategories as $key => $category)
                                <option value="{{ $key }}" {{ old('expense_category') == $key ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel" class="form-label">Fuel</label>
                        <input type="number" step="0.01" class="form-control" id="fuel" name="fuel" value="{{ old('fuel') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="food" class="form-label">Food</label>
                        <input type="number" step="0.01" class="form-control" id="food" name="food" value="{{ old('food') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="toll" class="form-label">Toll</label>
                        <input type="number" step="0.01" class="form-control" id="toll" name="toll" value="{{ old('toll') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="driver_payment" class="form-label">Driver Payment</label>
                        <input type="number" step="0.01" class="form-control" id="driver_payment" name="driver_payment" value="{{ old('driver_payment') }}">
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Additional Information -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Operations Income
                    </button>
                    <a href="{{ route('financial-income.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection