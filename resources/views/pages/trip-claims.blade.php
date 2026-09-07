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
        <div class="eyebrow">Trip Management</div>
        <h1>Trip Claims System</h1>
        <div class="sub">Manage trip claims, agreed amounts, and returns</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#claimModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add Claim</button>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Billing Month</label>
            <input type="month" class="form-control" id="filter_month" value="{{ $currentMonth }}" onchange="filterClaims()">
          </div>
          <div class="col-md-3">
            <label class="form-label">Vehicle Number</label>
            <input type="text" class="form-control" id="filter_vehicle" placeholder="ABC-123" onchange="filterClaims()">
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="filter_status" onchange="filterClaims()">
              <option value="all">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="paid">Paid</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Claims Table -->
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>Trip Date</th>
                <th>Vehicle No</th>
                <th>Route From</th>
                <th>Route To</th>
                <th>Agreed Amount</th>
                <th>Return Date</th>
                <th>Claimed Amount</th>
                <th>Claim Details</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="claimsTableBody">
              @foreach($claims as $claim)
                <tr>
                  <td>{{ $claim->formatted_trip_date }}</td>
                  <td>{{ strtoupper($claim->vehicle_number ?? '') }}</td>
                  <td>{{ $claim->route_from ?? '' }}</td>
                  <td>{{ $claim->route_to ?? '' }}</td>
                  <td>{{ $claim->formatted_agreed_amount }}</td>
                  <td>{{ $claim->formatted_return_date }}</td>
                  <td>{{ $claim->formatted_claimed_amount }}</td>
                  <td>{{ $claim->claim_details ?? '-' }}</td>
                  <td>
                    <span class="badge bg-{{ $claim->status === 'paid' ? 'success' : ($claim->status === 'approved' ? 'info' : ($claim->status === 'rejected' ? 'danger' : 'warning')) }}">
                      {{ ucfirst($claim->status) }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editClaim({{ $claim->id }})">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteClaim({{ $claim->id }})">
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

  <!-- Claim Modal -->
  <div class="modal fade" id="claimModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="claimModalTitle">Add Trip Claim</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="claimForm">
            <input type="hidden" id="claim_id">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Trip Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="trip_date" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Vehicle No <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="vehicle_number" placeholder="ABC-123" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Route From <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="route_from" placeholder="Depalpur" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Route To <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="route_to" placeholder="Lahore" required>
              </div>
              <div class="col-md-3">
                <label class="form-label">Agreed Amount (PKR) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control" id="agreed_amount" placeholder="14500" required min="0">
              </div>
              <div class="col-md-3">
                <label class="form-label">Return Date</label>
                <input type="date" class="form-control" id="return_date">
              </div>
              <div class="col-md-3">
                <label class="form-label">Claimed Amount (PKR)</label>
                <input type="number" step="0.01" class="form-control" id="claimed_amount" placeholder="12000" min="0">
              </div>
              <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="status">
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                  <option value="paid">Paid</option>
                </select>
              </div>
              <div class="col-md-12">
                <label class="form-label">Claim Details</label>
                <textarea class="form-control" id="claim_details" rows="3" placeholder="5 aug wapisi 12000 claim kiya"></textarea>
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
          <button type="button" class="btn btn-primary" onclick="saveClaim()">Save Claim</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let claimModal;

    document.addEventListener('DOMContentLoaded', function() {
      claimModal = new bootstrap.Modal(document.getElementById('claimModal'));
      document.getElementById('billing_month').value = '{{ $currentMonth }}';
      document.getElementById('trip_date').valueAsDate = new Date();
      
      Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      });
    });

    function resetForm() {
      document.getElementById('claimForm').reset();
      document.getElementById('claim_id').value = '';
      document.getElementById('claimModalTitle').textContent = 'Add Trip Claim';
      document.getElementById('billing_month').value = document.getElementById('filter_month').value;
      document.getElementById('trip_date').valueAsDate = new Date();
    }

    function editClaim(id) {
      fetch(`/trip-claims/${id}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('claim_id').value = data.id;
          document.getElementById('trip_date').value = data.trip_date || '';
          document.getElementById('vehicle_number').value = data.vehicle_number || '';
          document.getElementById('route_from').value = data.route_from || '';
          document.getElementById('route_to').value = data.route_to || '';
          document.getElementById('agreed_amount').value = data.agreed_amount || 0;
          document.getElementById('return_date').value = data.return_date || '';
          document.getElementById('claimed_amount').value = data.claimed_amount || 0;
          document.getElementById('claim_details').value = data.claim_details || '';
          document.getElementById('status').value = data.status || 'pending';
          document.getElementById('billing_month').value = data.billing_month || '';
          
          document.getElementById('claimModalTitle').textContent = 'Edit Trip Claim';
          claimModal.show();
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error loading claim: ' + error.message
          });
        });
    }

    function saveClaim() {
      const id = document.getElementById('claim_id').value;
      const tripDate = document.getElementById('trip_date').value;
      const vehicleNumber = document.getElementById('vehicle_number').value;
      const routeFrom = document.getElementById('route_from').value;
      const routeTo = document.getElementById('route_to').value;
      const agreedAmount = document.getElementById('agreed_amount').value;
      const billingMonth = document.getElementById('billing_month').value;

      if (!tripDate || !vehicleNumber || !routeFrom || !routeTo || !agreedAmount || !billingMonth) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Required Fields',
          text: 'Please fill in all required fields'
        });
        return;
      }

      const formData = {
        trip_date: tripDate,
        vehicle_number: vehicleNumber.toUpperCase(),
        route_from: routeFrom,
        route_to: routeTo,
        agreed_amount: parseFloat(agreedAmount),
        return_date: document.getElementById('return_date').value || null,
        claimed_amount: parseFloat(document.getElementById('claimed_amount').value) || 0,
        claim_details: document.getElementById('claim_details').value || null,
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

      const url = id ? `/trip-claims/${id}` : '/trip-claims';
      const method = id ? 'PUT' : 'POST';

      fetch(url, {
        method: method,
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
          claimModal.hide();
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
          text: 'Error saving claim: ' + error.message
        });
      });
    }

    function deleteClaim(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this trip claim?',
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

          fetch(`/trip-claims/${id}`, {
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
                text: 'Error deleting claim'
              });
            }
          })
          .catch(error => {
            Swal.close();
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error deleting claim: ' + error.message
            });
          });
        }
      });
    }

    function filterClaims() {
      const month = document.getElementById('filter_month').value;
      const vehicle = document.getElementById('filter_vehicle').value;
      const status = document.getElementById('filter_status').value;

      fetch('/trip-claims/filter', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          billing_month: month,
          vehicle_number: vehicle,
          status: status
        })
      })
      .then(response => response.json())
      .then(data => {
        updateClaimsTable(data.claims);
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error filtering claims: ' + error.message
        });
      });
    }

    function updateClaimsTable(claims) {
      const tbody = document.getElementById('claimsTableBody');
      tbody.innerHTML = '';
      
      claims.forEach(claim => {
        const statusClass = claim.status === 'paid' ? 'success' : (claim.status === 'approved' ? 'info' : (claim.status === 'rejected' ? 'danger' : 'warning'));
        const row = `
          <tr>
            <td>${claim.trip_date ? new Date(claim.trip_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '-'}</td>
            <td>${claim.vehicle_number ? claim.vehicle_number.toUpperCase() : '-'}</td>
            <td>${claim.route_from || '-'}</td>
            <td>${claim.route_to || '-'}</td>
            <td>${Number(claim.agreed_amount).toFixed(2)}</td>
            <td>${claim.return_date ? new Date(claim.return_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '-'}</td>
            <td>${Number(claim.claimed_amount).toFixed(2)}</td>
            <td>${claim.claim_details || '-'}</td>
            <td><span class="badge bg-${statusClass}">${claim.status ? claim.status.charAt(0).toUpperCase() + claim.status.slice(1) : 'Pending'}</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editClaim(${claim.id})">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteClaim(${claim.id})">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        `;
        tbody.innerHTML += row;
      });
    }
  </script>
@endsection
