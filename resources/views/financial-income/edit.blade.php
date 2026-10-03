@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-head">
        <div>
            <div class="eyebrow">Operations Management</div>
            <h1>Edit Operations Income</h1>
            <div class="sub">Update income and expense record</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-navy" onclick="window.location.href='{{ route('financial-income.index') }}'"><i class="bi bi-arrow-left me-1"></i> Back to List</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('financial-income.update', $financialIncome->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date *</label>
                        <input type="date" class="form-control" id="date" name="date" required value="{{ $financialIncome->date ? $financialIncome->date->format('Y-m-d') : old('date') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="total_income" class="form-label">Total Income *</label>
                        <input type="number" step="0.01" class="form-control" id="total_income" name="total_income" required value="{{ old('total_income', $financialIncome->total_income) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vehicle" class="form-label">Vehicle</label>
                        <select class="form-select" id="vehicle" name="vehicle">
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle }}" {{ old('vehicle', $financialIncome->vehicle) == $vehicle ? 'selected' : '' }}>{{ $vehicle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="driver" class="form-label">Driver</label>
                        <select class="form-select" id="driver" name="driver">
                            <option value="">Select Driver</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver }}" {{ old('driver', $financialIncome->driver) == $driver ? 'selected' : '' }}>{{ $driver }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Expense Information -->
                    <div class="col-md-4 mb-3">
                        <label for="expense" class="form-label">Expense</label>
                        <input type="number" step="0.01" class="form-control" id="expense" name="expense" value="{{ old('expense', $financialIncome->expense) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="expense_category" class="form-label">Expense Category</label>
                        <select class="form-select" id="expense_category" name="expense_category">
                            <option value="">Select Category</option>
                            @foreach($expenseCategories as $key => $category)
                                <option value="{{ $key }}" {{ old('expense_category', $financialIncome->expense_category) == $key ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="loading_point" class="form-label">Loading Point</label>
                        <input type="text" class="form-control" id="loading_point" name="loading_point" value="{{ old('loading_point', $financialIncome->loading_point) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="uploading_point" class="form-label">Uploading Point</label>
                        <input type="text" class="form-control" id="uploading_point" name="uploading_point" value="{{ old('uploading_point', $financialIncome->uploading_point) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fuel" class="form-label">Fuel</label>
                        <input type="number" step="0.01" class="form-control" id="fuel" name="fuel" value="{{ old('fuel', $financialIncome->fuel) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="food" class="form-label">Food</label>
                        <input type="number" step="0.01" class="form-control" id="food" name="food" value="{{ old('food', $financialIncome->food) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="toll" class="form-label">Toll</label>
                        <input type="number" step="0.01" class="form-control" id="toll" name="toll" value="{{ old('toll', $financialIncome->toll) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="driver_payment" class="form-label">Driver Payment</label>
                        <input type="number" step="0.01" class="form-control" id="driver_payment" name="driver_payment" value="{{ old('driver_payment', $financialIncome->driver_payment) }}">
                    </div>
                </div>

                <hr>

                <div class="row">
                    <!-- Additional Information -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $financialIncome->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Select Status</option>
                            <option value="pending" {{ old('status', $financialIncome->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status', $financialIncome->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $financialIncome->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Operations Income
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