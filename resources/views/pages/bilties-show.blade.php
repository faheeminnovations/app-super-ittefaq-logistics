@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Bilty Details</h1>
        <div class="sub">View traditional goods transportation receipt (بِلٹی)</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('bilties.index') }}" class="btn btn-outline-navy"><i class="bi bi-arrow-left me-1"></i> Back to Bilties</a>
        <a href="{{ route('bilties.print', $bilty->id) }}" class="btn btn-primary" target="_blank">
          <i class="bi bi-printer me-1"></i> Print Receipt
        </a>
      </div>
    </div>

    <div class="panel">
      <div class="row mb-4">
        <div class="col-md-6">
          <h5>Bilty Information</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Bilty Number:</strong></td>
              <td>{{ $bilty->bilty_number }}</td>
            </tr>
            <tr>
              <td><strong>Date:</strong></td>
              <td>{{ $bilty->bilty_date->format('d M Y') }}</td>
            </tr>
            <tr>
              <td><strong>Status:</strong></td>
              <td>
                <span class="badge bg-{{ $bilty->status == 'delivered' ? 'success' : ($bilty->status == 'cancelled' ? 'danger' : ($bilty->status == 'in_transit' ? 'info' : 'warning')) }}">
                  {{ ucfirst(str_replace('_', ' ', $bilty->status)) }}
                </span>
              </td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <h5>Financial Summary</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Total Amount:</strong></td>
              <td>{{ $bilty->formatted_total_amount }}</td>
            </tr>
            <tr>
              <td><strong>Advance:</strong></td>
              <td>{{ $bilty->formatted_advance_amount }}</td>
            </tr>
            <tr>
              <td><strong>Remaining Balance:</strong></td>
              <td>{{ $bilty->formatted_remaining_balance }}</td>
            </tr>
            <tr>
              <td><strong>Rent Amount:</strong></td>
              <td>{{ $bilty->formatted_rent_amount ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Scale:</strong></td>
              <td>{{ $bilty->formatted_scale ?? 'N/A' }}</td>
            </tr>
          </table>
        </div>
      </div>

      <hr>

      <div class="row mb-4">
        <div class="col-md-6">
          <h5>Location Information</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>From (از):</strong></td>
              <td>{{ $bilty->from_location }}</td>
            </tr>
            <tr>
              <td><strong>To (تا):</strong></td>
              <td>{{ $bilty->to_location }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <h5>Goods Information</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Description (تفصیل مال):</strong></td>
              <td>{{ $bilty->goods_description }}</td>
            </tr>
            <tr>
              <td><strong>Quantity (تعداد):</strong></td>
              <td>{{ $bilty->quantity }} {{ $bilty->quantity_unit }}</td>
            </tr>
          </table>
        </div>
      </div>

      <hr>

      <div class="row mb-4">
        <div class="col-md-6">
          <h5>Sender Information (بھیجنے والے)</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Name:</strong></td>
              <td>{{ $bilty->sender_name }}</td>
            </tr>
            <tr>
              <td><strong>Phone:</strong></td>
              <td>{{ $bilty->sender_phone ?? 'N/A' }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <h5>Receiver Information (لینے والے)</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Name:</strong></td>
              <td>{{ $bilty->receiver_name }}</td>
            </tr>
            <tr>
              <td><strong>Phone:</strong></td>
              <td>{{ $bilty->receiver_phone ?? 'N/A' }}</td>
            </tr>
          </table>
        </div>
      </div>

      <hr>

      <div class="row mb-4">
        <div class="col-md-6">
          <h5>Vehicle & Driver Information</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Vehicle Number (گاڑی نمبر):</strong></td>
              <td>{{ $bilty->vehicle_number ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Driver Name (نام ڈرائیور):</strong></td>
              <td>{{ $bilty->driver_name ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>ID Card Number (کارڈ نمبر):</strong></td>
              <td>{{ $bilty->card_number ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Driver Phone:</strong></td>
              <td>{{ $bilty->driver_phone ?? 'N/A' }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <h5>System Integration</h5>
          <table class="table table-borderless">
            <tr>
              <td><strong>Customer:</strong></td>
              <td>{{ $bilty->customer ? $bilty->customer->name : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Vehicle (System):</strong></td>
              <td>{{ $bilty->vehicle ? $bilty->vehicle->reg_no : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Driver (System):</strong></td>
              <td>{{ $bilty->driver ? $bilty->driver->name : 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Linked Job:</strong></td>
              <td>{{ $bilty->job ? $bilty->job->job_number : 'N/A' }}</td>
            </tr>
          </table>
        </div>
      </div>

      @if($bilty->notes || $bilty->registration_number || $bilty->contact_details)
        <hr>
        <div class="row mb-4">
          <div class="col-12">
            <h5>Additional Information</h5>
            <table class="table table-borderless">
              @if($bilty->registration_number)
                <tr>
                  <td><strong>Registration Number:</strong></td>
                  <td>{{ $bilty->registration_number }}</td>
                </tr>
              @endif
              @if($bilty->contact_details)
                <tr>
                  <td><strong>Contact Details:</strong></td>
                  <td>{{ $bilty->contact_details }}</td>
                </tr>
              @endif
              @if($bilty->notes)
                <tr>
                  <td><strong>Notes:</strong></td>
                  <td>{{ $bilty->notes }}</td>
                </tr>
              @endif
            </table>
          </div>
        </div>
      @endif

      <div class="d-flex gap-2 mt-4">
        <a href="{{ route('bilties.edit', $bilty->id) }}" class="btn btn-primary">
          <i class="bi bi-pencil me-1"></i> Edit Bilty
        </a>
        <a href="{{ route('bilties.print', $bilty->id) }}" class="btn btn-success" target="_blank">
          <i class="bi bi-printer me-1"></i> Print Receipt
        </a>
        <form action="{{ route('bilties.destroy', $bilty->id) }}" method="POST" style="display: inline;">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this bilty?')">
            <i class="bi bi-trash me-1"></i> Delete Bilty
          </button>
        </form>
      </div>
    </div>

    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>
@endsection