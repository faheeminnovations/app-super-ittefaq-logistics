@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Professional Billing</div>
        <h1>Branding Billing</h1>
        <div class="sub">بائر،بریڈنگ - Branding Management</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="window.location.href='/professional-billing'"><i class="bi bi-arrow-left me-1"></i> Back</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#billingModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add Record</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Billing Month</label>
            <input type="month" class="form-control" id="filter_month" value="{{ $currentMonth }}" onchange="filterBillings()">
          </div>
        </div>
      </div>
    </div>

    <!-- Billing Table -->
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Sr</th>
                <th>Date</th>
                <th>Vehicle No</th>
                <th>Delivery Point</th>
                <th>Kilometers</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="billingTableBody">
              @foreach($billings as $billing)
                <tr>
                  <td>{{ $billing->serial_number ?? '-' }}</td>
                  <td>{{ $billing->formatted_date }}</td>
                  <td>{{ strtoupper($billing->vehicle_number ?? '') }}</td>
                  <td>{{ $billing->delivery_point ?? '' }}</td>
                  <td>{{ number_format($billing->kilometers ?? 0, 2) }}</td>
                  <td>{{ number_format($billing->rate ?? 0, 2) }}</td>
                  <td>{{ $billing->formatted_amount }}</td>
                  <td>
                    <span class="badge bg-{{ $billing->status === 'Paid' ? 'success' : 'warning' }}">
                      {{ $billing->status }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBilling({{ $billing->id }})">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Billing Modal -->
  <div class="modal fade" id="billingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Branding Billing Record</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="billingForm">
            <div class="row g-3">
              <div class="col-md-2">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" id="serial_number" placeholder="1">
              </div>
              <div class="col-md-2">
                <label class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="date" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Vehicle No <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="vehicle_number" placeholder="ABC-123" required>
              </div>
              <div class="col-md-5">
                <label class="form-label">Delivery Point <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="delivery_point" placeholder="Delivery Location" required>
              </div>
              <div class="col-md-2">
                <label class="form-label">Kilometers</label>
                <input type="number" step="0.01" class="form-control" id="kilometers" value="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Rate</label>
                <input type="number" step="0.01" class="form-control" id="rate" value="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" class="form-control" id="amount" value="0">
              </div>
              <div class="col-md-2">
                <label class="form-label">Status</label>
                <select class="form-select" id="status">
                  <option value="Pending">Pending</option>
                  <option value="Paid">Paid</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Billing Month <span class="text-danger">*</span></label>
                <input type="month" class="form-control" id="billing_month" required>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveBilling()">Save Record</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let billingModal;

    document.addEventListener('DOMContentLoaded', function() {
      billingModal = new bootstrap.Modal(document.getElementById('billingModal'));
      document.getElementById('billing_month').value = '{{ $currentMonth }}';
      document.getElementById('date').valueAsDate = new Date();
      
      Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      });
    });

    function resetForm() {
      document.getElementById('billingForm').reset();
      document.getElementById('billing_month').value = document.getElementById('filter_month').value;
      document.getElementById('date').valueAsDate = new Date();
    }

    function saveBilling() {
      const date = document.getElementById('date').value;
      const vehicleNumber = document.getElementById('vehicle_number').value;
      const deliveryPoint = document.getElementById('delivery_point').value;
      const billingMonth = document.getElementById('billing_month').value;

      if (!date || !vehicleNumber || !deliveryPoint || !billingMonth) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Required Fields',
          text: 'Please fill in all required fields'
        });
        return;
      }

      const formData = {
        serial_number: document.getElementById('serial_number').value || null,
        date: date,
        vehicle_number: vehicleNumber.toUpperCase(),
        delivery_point: deliveryPoint,
        kilometers: parseFloat(document.getElementById('kilometers').value) || 0,
        rate: parseFloat(document.getElementById('rate').value) || 0,
        amount: parseFloat(document.getElementById('amount').value) || 0,
        status: document.getElementById('status').value,
        billing_month: billingMonth
      };

      Swal.fire({
        title: 'Saving...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      fetch('/professional-billing/branding/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
      })
      .then(response => response.json())
      .then(data => {
        Swal.close();
        if (data.success) {
          billingModal.hide();
          location.reload();
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: data.message
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'An error occurred'
          });
        }
      })
      .catch(error => {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error saving billing record: ' + error.message
        });
      });
    }

    function deleteBilling(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this billing record?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Deleting...',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          fetch(`/professional-billing/branding/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
          .then(response => response.json())
          .then(data => {
            Swal.close();
            if (data.success) {
              location.reload();
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error deleting record'
              });
            }
          })
          .catch(error => {
            Swal.close();
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error deleting record: ' + error.message
            });
          });
        }
      });
    }

    function filterBillings() {
      const month = document.getElementById('filter_month').value;
      window.location.href = `/professional-billing/branding?month=${month}`;
    }
  </script>
@endsection
