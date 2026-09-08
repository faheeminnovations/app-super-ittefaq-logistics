@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Edit Bilty</h1>
        <div class="sub">Update traditional goods transportation receipt (بِلٹی)</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('bilties.index') }}" class="btn btn-outline-navy"><i class="bi bi-arrow-left me-1"></i> Back to Bilties</a>
        <a href="{{ route('bilties.show', $bilty->id) }}" class="btn btn-outline-primary">
          <i class="bi bi-eye me-1"></i> View Bilty
        </a>
      </div>
    </div>

    <div class="panel">
      <form action="{{ route('bilties.update', $bilty->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <h6 class="mb-3">Basic Information</h6>
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="bilty_number" class="form-label">Bilty Number</label>
            <input type="text" class="form-control" name="bilty_number" id="bilty_number" value="{{ $bilty->bilty_number }}">
            <small class="text-muted">Leave empty to auto-generate</small>
          </div>
          <div class="col-md-4 mb-3">
            <label for="bilty_date" class="form-label">Bilty Date</label>
            <input type="date" class="form-control" name="bilty_date" id="bilty_date" value="{{ $bilty->bilty_date }}" required>
          </div>
          <div class="col-md-4 mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" name="status" id="status" required>
              <option value="pending" {{ $bilty->status == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="in_transit" {{ $bilty->status == 'in_transit' ? 'selected' : '' }}>In Transit</option>
              <option value="delivered" {{ $bilty->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
              <option value="cancelled" {{ $bilty->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
        </div>

        <!-- Location Information -->
        <h6 class="mb-3 mt-4">Location Information</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="from_location" class="form-label">From Location (از)</label>
            <input type="text" class="form-control" name="from_location" id="from_location" value="{{ $bilty->from_location }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="to_location" class="form-label">To Location (تا)</label>
            <input type="text" class="form-control" name="to_location" id="to_location" value="{{ $bilty->to_location }}" required>
          </div>
        </div>

        <!-- Sender and Receiver Information -->
        <h6 class="mb-3 mt-4">Sender & Receiver Information</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="sender_name" class="form-label">Sender Name (بھیجنے والے کا نام)</label>
            <input type="text" class="form-control" name="sender_name" id="sender_name" value="{{ $bilty->sender_name }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="sender_phone" class="form-label">Sender Phone</label>
            <input type="text" class="form-control" name="sender_phone" id="sender_phone" value="{{ $bilty->sender_phone }}">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="receiver_name" class="form-label">Receiver Name (لینے والے کا نام)</label>
            <input type="text" class="form-control" name="receiver_name" id="receiver_name" value="{{ $bilty->receiver_name }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="receiver_phone" class="form-label">Receiver Phone</label>
            <input type="text" class="form-control" name="receiver_phone" id="receiver_phone" value="{{ $bilty->receiver_phone }}">
          </div>
        </div>

        <!-- Vehicle and Driver Information -->
        <h6 class="mb-3 mt-4">Vehicle & Driver Information</h6>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="vehicle_number" class="form-label">Vehicle Number (گاڑی نمبر)</label>
            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" value="{{ $bilty->vehicle_number }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="driver_name" class="form-label">Driver Name (نام ڈرائیور)</label>
            <input type="text" class="form-control" name="driver_name" id="driver_name" value="{{ $bilty->driver_name }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="card_number" class="form-label">ID Card Number (کارڈ نمبر)</label>
            <input type="text" class="form-control" name="card_number" id="card_number" value="{{ $bilty->card_number }}">
          </div>
          <div class="col-md-3 mb-3">
            <label for="driver_phone" class="form-label">Driver Phone</label>
            <input type="text" class="form-control" name="driver_phone" id="driver_phone" value="{{ $bilty->driver_phone }}">
          </div>
        </div>

        <!-- System Links -->
        <h6 class="mb-3 mt-4">System Integration</h6>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select class="form-select" name="customer_id" id="customer_id">
              <option value="">Select Customer</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ $bilty->customer_id == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="vehicle_id" class="form-label">Vehicle (System)</label>
            <select class="form-select" name="vehicle_id" id="vehicle_id">
              <option value="">Select Vehicle</option>
              @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->id }}" {{ $bilty->vehicle_id == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->reg_no }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="driver_id" class="form-label">Driver (System)</label>
            <select class="form-select" name="driver_id" id="driver_id">
              <option value="">Select Driver</option>
              @foreach($drivers as $driver)
                <option value="{{ $driver->id }}" {{ $bilty->driver_id == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 mb-3">
            <label for="job_id" class="form-label">Linked Job</label>
            <select class="form-select" name="job_id" id="job_id">
              <option value="">Select Job</option>
              @if(isset($jobs))
                @foreach($jobs as $job)
                  <option value="{{ $job->id }}" {{ $bilty->job_id == $job->id ? 'selected' : '' }}>{{ $job->job_number }}</option>
                @endforeach
              @endif
            </select>
          </div>
        </div>

        <!-- Goods Information -->
        <h6 class="mb-3 mt-4">Goods Information</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="goods_description" class="form-label">Goods Description (تفصیل مال)</label>
            <textarea class="form-control" name="goods_description" id="goods_description" rows="2" required>{{ $bilty->goods_description }}</textarea>
          </div>
          <div class="col-md-3 mb-3">
            <label for="quantity" class="form-label">Quantity (تعداد)</label>
            <input type="number" class="form-control" name="quantity" id="quantity" value="{{ $bilty->quantity }}" placeholder="0">
          </div>
          <div class="col-md-3 mb-3">
            <label for="quantity_unit" class="form-label">Unit</label>
            <input type="text" class="form-control" name="quantity_unit" id="quantity_unit" value="{{ $bilty->quantity_unit }}" placeholder="bags, kg, etc.">
          </div>
        </div>

        <!-- Financial Information -->
        <h6 class="mb-3 mt-4">Financial Information</h6>
        <div class="row">
          <div class="col-md-3 mb-3">
            <label for="total_amount" class="form-label">Total Amount (کرایہ روپے)</label>
            <input type="number" step="0.01" class="form-control" name="total_amount" id="total_amount" value="{{ $bilty->total_amount }}" required>
          </div>
          <div class="col-md-3 mb-3">
            <label for="advance_amount" class="form-label">Advance (پیشگی)</label>
            <input type="number" step="0.01" class="form-control" name="advance_amount" id="advance_amount" value="{{ $bilty->advance_amount }}" placeholder="0.00">
          </div>
          <div class="col-md-3 mb-3">
            <label for="rent_amount" class="form-label">Rent Amount</label>
            <input type="number" step="0.01" class="form-control" name="rent_amount" id="rent_amount" value="{{ $bilty->rent_amount }}" placeholder="0.00">
          </div>
          <div class="col-md-3 mb-3">
            <label for="scale" class="form-label">Scale</label>
            <input type="number" step="0.01" class="form-control" name="scale" id="scale" value="{{ $bilty->scale }}" placeholder="0.00">
          </div>
        </div>

        <!-- Additional Information -->
        <h6 class="mb-3 mt-4">Additional Information</h6>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="registration_number" class="form-label">Registration Number</label>
            <input type="text" class="form-control" name="registration_number" id="registration_number" value="{{ $bilty->registration_number }}">
          </div>
          <div class="col-md-6 mb-3">
            <label for="contact_details" class="form-label">Contact Details</label>
            <input type="text" class="form-control" name="contact_details" id="contact_details" value="{{ $bilty->contact_details }}">
          </div>
        </div>
        <div class="mb-3">
          <label for="notes" class="form-label">Notes</label>
          <textarea class="form-control" name="notes" id="notes" rows="2">{{ $bilty->notes }}</textarea>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Bilty</button>
          <a href="{{ route('bilties.show', $bilty->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>

    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
@endsection