@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Operations</div>
        <h1>Bilties Management</h1>
        <div class="sub">Traditional goods transportation receipts (بِلٹی)</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#biltyModal">
          <i class="bi bi-plus-lg me-1"></i> Add Bilty
        </button>
        <button class="btn btn-outline-navy" onclick="exportBilties()">
          <i class="bi bi-download me-1"></i> Export
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Total Bilties</div>
            <div class="stat-value">{{ $totalBilties }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Pending</div>
            <div class="stat-value text-warning">{{ $pendingBilties }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">In Transit</div>
            <div class="stat-value text-info">{{ $inTransitBilties }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Delivered</div>
            <div class="stat-value text-success">{{ $deliveredBilties }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="panel mb-3">
      <div class="row">
        <div class="col-md-4">
          <input type="text" class="form-control" id="searchBilties" placeholder="Search by bilty number, sender, receiver...">
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterStatus">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="in_transit">In Transit</option>
            <option value="delivered">Delivered</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-select" id="filterCustomer">
            <option value="">All Customers</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-secondary w-100" onclick="clearFilters()">Clear</button>
        </div>
      </div>
    </div>

    <!-- Bilties Table -->
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-hover" id="biltiesTable">
          <thead>
            <tr>
              <th>Bilty Number</th>
              <th>Date</th>
              <th>From</th>
              <th>To</th>
              <th>Sender</th>
              <th>Receiver</th>
              <th>Vehicle</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bilties as $bilty)
              <tr>
                <td><strong>{{ $bilty->bilty_number }}</strong></td>
                <td>{{ $bilty->bilty_date->format('d M Y') }}</td>
                <td>{{ $bilty->from_location }}</td>
                <td>{{ $bilty->to_location }}</td>
                <td>{{ $bilty->sender_name }}</td>
                <td>{{ $bilty->receiver_name }}</td>
                <td>{{ $bilty->vehicle_number ?? 'N/A' }}</td>
                <td>{{ $bilty->formatted_total_amount }}</td>
                <td>
                  <span class="badge bg-{{ $bilty->status == 'delivered' ? 'success' : ($bilty->status == 'cancelled' ? 'danger' : ($bilty->status == 'in_transit' ? 'info' : 'warning')) }}">
                    {{ ucfirst(str_replace('_', ' ', $bilty->status)) }}
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="viewBilty({{ $bilty->id }})" title="View">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-outline-secondary" onclick="editBilty({{ $bilty->id }})" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-success" onclick="printBilty({{ $bilty->id }})" title="Print Receipt">
                      <i class="bi bi-printer"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteBilty({{ $bilty->id }})" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
          Showing {{ $bilties->firstItem() }} to {{ $bilties->lastItem() }} of {{ $bilties->total() }} entries
        </div>
        {{ $bilties->links() }}
      </div>
    </div>

    <!-- Create/Edit Bilty Modal -->
    <div class="modal fade" id="biltyModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="biltyModalLabel">Add New Bilty</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="biltyForm" action="{{ route('bilties.store') }}" method="POST">
            @csrf
            <div class="modal-body">
              <input type="hidden" name="bilty_id" id="bilty_id">
              <input type="hidden" name="_method" id="_method" value="POST">
              
              <!-- Basic Information -->
              <h6 class="mb-3">Basic Information</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="bilty_number" class="form-label">Bilty Number</label>
                  <input type="text" class="form-control" name="bilty_number" id="bilty_number">
                  <small class="text-muted">Leave empty to auto-generate</small>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="bilty_date" class="form-label">Bilty Date</label>
                  <input type="date" class="form-control" name="bilty_date" id="bilty_date" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" name="status" id="status" required>
                    <option value="pending">Pending</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
              </div>

              <!-- Location Information -->
              <h6 class="mb-3 mt-4">Location Information</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="from_location" class="form-label">From Location (از)</label>
                  <input type="text" class="form-control" name="from_location" id="from_location" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="to_location" class="form-label">To Location (تا)</label>
                  <input type="text" class="form-control" name="to_location" id="to_location" required>
                </div>
              </div>

              <!-- Sender and Receiver Information -->
              <h6 class="mb-3 mt-4">Sender & Receiver Information</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="sender_name" class="form-label">Sender Name (بھیجنے والے کا نام)</label>
                  <input type="text" class="form-control" name="sender_name" id="sender_name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="sender_phone" class="form-label">Sender Phone</label>
                  <input type="text" class="form-control" name="sender_phone" id="sender_phone">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="receiver_name" class="form-label">Receiver Name (لینے والے کا نام)</label>
                  <input type="text" class="form-control" name="receiver_name" id="receiver_name" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="receiver_phone" class="form-label">Receiver Phone</label>
                  <input type="text" class="form-control" name="receiver_phone" id="receiver_phone">
                </div>
              </div>

              <!-- Vehicle and Driver Information -->
              <h6 class="mb-3 mt-4">Vehicle & Driver Information</h6>
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="vehicle_number" class="form-label">Vehicle Number (گاڑی نمبر)</label>
                  <input type="text" class="form-control" name="vehicle_number" id="vehicle_number">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="driver_name" class="form-label">Driver Name (نام ڈرائیور)</label>
                  <input type="text" class="form-control" name="driver_name" id="driver_name">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="card_number" class="form-label">ID Card Number (کارڈ نمبر)</label>
                  <input type="text" class="form-control" name="card_number" id="card_number">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="driver_phone" class="form-label">Driver Phone</label>
                  <input type="text" class="form-control" name="driver_phone" id="driver_phone">
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
                      <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="vehicle_id" class="form-label">Vehicle (System)</label>
                  <select class="form-select" name="vehicle_id" id="vehicle_id">
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                      <option value="{{ $vehicle->id }}">{{ $vehicle->reg_no }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="driver_id" class="form-label">Driver (System)</label>
                  <select class="form-select" name="driver_id" id="driver_id">
                    <option value="">Select Driver</option>
                    @foreach($drivers as $driver)
                      <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="job_id" class="form-label">Linked Job</label>
                  <select class="form-select" name="job_id" id="job_id">
                    <option value="">Select Job</option>
                    @if(isset($jobs))
                      @foreach($jobs as $job)
                        <option value="{{ $job->id }}">{{ $job->job_number }}</option>
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
                  <textarea class="form-control" name="goods_description" id="goods_description" rows="2" required></textarea>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="quantity" class="form-label">Quantity (تعداد)</label>
                  <input type="number" class="form-control" name="quantity" id="quantity" placeholder="0">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="quantity_unit" class="form-label">Unit</label>
                  <input type="text" class="form-control" name="quantity_unit" id="quantity_unit" placeholder="bags, kg, etc.">
                </div>
              </div>

              <!-- Financial Information -->
              <h6 class="mb-3 mt-4">Financial Information</h6>
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="total_amount" class="form-label">Total Amount (کرایہ روپے)</label>
                  <input type="number" step="0.01" class="form-control" name="total_amount" id="total_amount" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="advance_amount" class="form-label">Advance (پیشگی)</label>
                  <input type="number" step="0.01" class="form-control" name="advance_amount" id="advance_amount" placeholder="0.00">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="rent_amount" class="form-label">Rent Amount</label>
                  <input type="number" step="0.01" class="form-control" name="rent_amount" id="rent_amount" placeholder="0.00">
                </div>
                <div class="col-md-3 mb-3">
                  <label for="scale" class="form-label">Scale</label>
                  <input type="number" step="0.01" class="form-control" name="scale" id="scale" placeholder="0.00">
                </div>
              </div>

              <!-- Additional Information -->
              <h6 class="mb-3 mt-4">Additional Information</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="registration_number" class="form-label">Registration Number</label>
                  <input type="text" class="form-control" name="registration_number" id="registration_number">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="contact_details" class="form-label">Contact Details</label>
                  <input type="text" class="form-control" name="contact_details" id="contact_details">
                </div>
              </div>
              <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> <span id="submitButtonText">Save Bilty</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View Bilty Modal -->
    <div class="modal fade" id="viewBiltyModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Bilty Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="viewBiltyContent">
            <!-- Content will be loaded via AJAX -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="printCurrentBilty()">
              <i class="bi bi-printer me-1"></i> Print Receipt
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <script>
    let currentBiltyId = null;
    let biltyModal, viewBiltyModal;

    // Clear any previous error messages from localStorage
    localStorage.removeItem('lastError');
    sessionStorage.removeItem('lastError');

    // Debug: Log that script is loaded
    console.log('Bilties script loaded');

    // Global functions for onclick handlers
    function filterBilties() {
        const search = document.getElementById('searchBilties').value.toLowerCase();
        const status = document.getElementById('filterStatus').value;
        const customer = document.getElementById('filterCustomer').value;

        const rows = document.querySelectorAll('#biltiesTable tbody tr');
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const rowStatus = row.querySelector('.badge').textContent.toLowerCase().replace(' ', '_');
          const rowCustomer = row.cells[4].textContent; // Using sender name as proxy

          const matchesSearch = text.includes(search);
          const matchesStatus = !status || rowStatus === status;
          const matchesCustomer = !customer || true; // Simplified logic

          row.style.display = matchesSearch && matchesStatus && matchesCustomer ? '' : 'none';
        });
      }

    function clearFilters() {
      document.getElementById('searchBilties').value = '';
      document.getElementById('filterStatus').value = '';
      document.getElementById('filterCustomer').value = '';
      filterBilties();
    }

    function openCreateModal() {
      document.getElementById('biltyForm').reset();
      document.getElementById('bilty_id').value = '';
      document.getElementById('_method').value = 'POST';
      document.getElementById('biltyForm').action = '{{ route('bilties.store') }}';
      document.getElementById('biltyModalLabel').textContent = 'Add New Bilty';
      document.getElementById('submitButtonText').textContent = 'Save Bilty';
      biltyModal.show();
    }

    function editBilty(id) {
      console.log('editBilty called with id:', id);
      console.log('Loading bilty for edit:', id);
        fetch(`/api/bilties/${id}`, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        })
          .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Bilty data received:', data);
            const bilty = data.bilty;
            document.getElementById('bilty_id').value = bilty.id;
            document.getElementById('_method').value = 'PUT';
            document.getElementById('biltyForm').action = `/bilties/${id}`;
            document.getElementById('biltyModalLabel').textContent = 'Edit Bilty';
            document.getElementById('submitButtonText').textContent = 'Update Bilty';

            // Populate form fields
            document.getElementById('bilty_number').value = bilty.bilty_number || '';
            // Handle date formatting - if it's a Carbon object date string, extract just the date part
            const biltyDate = bilty.bilty_date ? (bilty.bilty_date.includes('T') ? bilty.bilty_date.split('T')[0] : bilty.bilty_date) : '';
            document.getElementById('bilty_date').value = biltyDate;
            document.getElementById('status').value = bilty.status || 'pending';
            document.getElementById('from_location').value = bilty.from_location || '';
            document.getElementById('to_location').value = bilty.to_location || '';
            document.getElementById('sender_name').value = bilty.sender_name || '';
            document.getElementById('sender_phone').value = bilty.sender_phone || '';
            document.getElementById('receiver_name').value = bilty.receiver_name || '';
            document.getElementById('receiver_phone').value = bilty.receiver_phone || '';
            document.getElementById('vehicle_number').value = bilty.vehicle_number || '';
            document.getElementById('driver_name').value = bilty.driver_name || '';
            document.getElementById('card_number').value = bilty.card_number || '';
            document.getElementById('driver_phone').value = bilty.driver_phone || '';
            document.getElementById('customer_id').value = bilty.customer_id || '';
            document.getElementById('vehicle_id').value = bilty.vehicle_id || '';
            document.getElementById('driver_id').value = bilty.driver_id || '';
            document.getElementById('job_id').value = bilty.job_id || '';
            document.getElementById('goods_description').value = bilty.goods_description || '';
            document.getElementById('quantity').value = bilty.quantity || '';
            document.getElementById('quantity_unit').value = bilty.quantity_unit || '';
            document.getElementById('total_amount').value = bilty.total_amount || '';
            document.getElementById('advance_amount').value = bilty.advance_amount || '';
            document.getElementById('rent_amount').value = bilty.rent_amount || '';
            document.getElementById('scale').value = bilty.scale || '';
            document.getElementById('registration_number').value = bilty.registration_number || '';
            document.getElementById('contact_details').value = bilty.contact_details || '';
            document.getElementById('notes').value = bilty.notes || '';

            biltyModal.show();
          })
          .catch(error => {
            console.error('Error loading bilty:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to load bilty details: ' + error.message
            });
          });
      }

    function viewBilty(id) {
      console.log('viewBilty called with id:', id);
      currentBiltyId = id;
      console.log('Loading bilty for view:', id);
        fetch(`/api/bilties/${id}`, {
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        })
          .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Bilty data received:', data);
            const bilty = data.bilty;
            // Handle date formatting
            const biltyDate = bilty.bilty_date ? (bilty.bilty_date.includes('T') ? bilty.bilty_date.split('T')[0] : bilty.bilty_date) : '';
            const content = `
              <div class="row">
                <div class="col-md-6">
                  <strong>Bilty Number:</strong> ${bilty.bilty_number}<br>
                  <strong>Date:</strong> ${biltyDate}<br>
                  <strong>Status:</strong> <span class="badge bg-${bilty.status === 'delivered' ? 'success' : (bilty.status === 'cancelled' ? 'danger' : 'warning')}">${bilty.status}</span><br>
                  <strong>From:</strong> ${bilty.from_location}<br>
                  <strong>To:</strong> ${bilty.to_location}
                </div>
                <div class="col-md-6">
                  <strong>Sender:</strong> ${bilty.sender_name}<br>
                  <strong>Sender Phone:</strong> ${bilty.sender_phone || 'N/A'}<br>
                  <strong>Receiver:</strong> ${bilty.receiver_name}<br>
                  <strong>Receiver Phone:</strong> ${bilty.receiver_phone || 'N/A'}
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-6">
                  <strong>Vehicle Number:</strong> ${bilty.vehicle_number || 'N/A'}<br>
                  <strong>Driver Name:</strong> ${bilty.driver_name || 'N/A'}<br>
                  <strong>ID Card Number:</strong> ${bilty.card_number || 'N/A'}<br>
                  <strong>Driver Phone:</strong> ${bilty.driver_phone || 'N/A'}
                </div>
                <div class="col-md-6">
                  <strong>Goods:</strong> ${bilty.goods_description}<br>
                  <strong>Quantity:</strong> ${bilty.quantity || 'N/A'} ${bilty.quantity_unit || ''}<br>
                  <strong>Total Amount:</strong> ${bilty.total_amount}
                </div>
              </div>
              <hr>
              <div class="row">
                <div class="col-md-3">
                  <strong>Total Amount:</strong> ${bilty.total_amount}
                </div>
                <div class="col-md-3">
                  <strong>Advance:</strong> ${bilty.advance_amount || 0}
                </div>
                <div class="col-md-3">
                  <strong>Remaining Balance:</strong> ${bilty.remaining_balance || 0}
                </div>
                <div class="col-md-3">
                  <strong>Scale:</strong> ${bilty.scale || 'N/A'}
                </div>
              </div>
              ${bilty.notes ? `<hr><strong>Notes:</strong> ${bilty.notes}` : ''}
            `;
            document.getElementById('viewBiltyContent').innerHTML = content;
            viewBiltyModal.show();
          })
          .catch(error => {
            console.error('Error loading bilty:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to load bilty details: ' + error.message
            });
          });
      }

    function printBilty(id) {
      console.log('printBilty called with id:', id);
      window.open(`/bilties/${id}/print`, '_blank');
    }

    function printCurrentBilty() {
      if (currentBiltyId) {
        printBilty(currentBiltyId);
      }
    }

    function deleteBilty(id) {
      console.log('deleteBilty called with id:', id);
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/bilties/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Content-Type': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                title: 'Deleted!',
                text: 'Bilty has been deleted successfully.',
                icon: 'success',
                confirmButtonColor: '#3085d6'
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                title: 'Error',
                text: 'Failed to delete bilty',
                icon: 'error',
                confirmButtonColor: '#d33'
              });
            }
          })
          .catch(error => {
            console.error('Error deleting bilty:', error);
            Swal.fire({
              title: 'Error',
              text: 'Failed to delete bilty',
              icon: 'error',
              confirmButtonColor: '#d33'
            });
          });
        }
      });
    }

    function exportBilties() {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/bilties/export';
      
      const csrfToken = document.createElement('input');
      csrfToken.type = 'hidden';
      csrfToken.name = '_token';
      csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
      form.appendChild(csrfToken);
      
      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Initialize modals
      biltyModal = new bootstrap.Modal(document.getElementById('biltyModal'));
      viewBiltyModal = new bootstrap.Modal(document.getElementById('viewBiltyModal'));

      // Search functionality
      document.getElementById('searchBilties').addEventListener('input', function() {
        filterBilties();
      });

      document.getElementById('filterStatus').addEventListener('change', function() {
        filterBilties();
      });

      document.getElementById('filterCustomer').addEventListener('change', function() {
        filterBilties();
      });
      
      // Initialize modal for create button
      document.querySelector('[data-bs-target="#biltyModal"]').addEventListener('click', openCreateModal);

      // Form submission
      document.getElementById('biltyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = this.action;
        const method = document.getElementById('_method').value;

        fetch(url, {
          method: method,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success || data.message) {
            Swal.fire({
              title: 'Success!',
              text: method === 'PUT' ? 'Bilty updated successfully!' : 'Bilty created successfully!',
              icon: 'success',
              confirmButtonColor: '#3085d6'
            }).then(() => {
              biltyModal.hide();
              location.reload();
            });
          } else {
            // Handle validation errors
            let errorMessage = 'Failed to save bilty';
            if (data.errors) {
              errorMessage = '';
              for (const field in data.errors) {
                errorMessage += data.errors[field].join(', ') + '\n';
              }
            } else if (data.message) {
              errorMessage = data.message;
            }
            Swal.fire({
              title: 'Error',
              text: errorMessage,
              icon: 'error',
              confirmButtonColor: '#d33'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to save bilty: ' + error.message,
            icon: 'error',
            confirmButtonColor: '#d33'
          });
        });
      });
    });
  </script>
@endsection