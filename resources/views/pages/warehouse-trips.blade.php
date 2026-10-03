@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Warehouse Management</div>
        <h1>Warehouse Trips</h1>
        <div class="sub">Excel-like trip management for Depalpur Warehouse</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tripModal">
          <i class="bi bi-plus-lg me-1"></i> Add Trip
        </button>
        <button class="btn btn-outline-navy" onclick="exportTrips()" id="exportBtn">
          <i class="bi bi-download me-1"></i> Export Excel
        </button>
        <button class="btn btn-success" onclick="generateInvoice('basic')" id="invoiceBtn">
          <i class="bi bi-file-earmark-text me-1"></i> Generate Invoice
        </button>
        <button class="btn btn-info" onclick="generateInvoice('with_expenses')" id="invoiceBtnWithExpenses">
          <i class="bi bi-file-earmark-text me-1"></i> Invoice with Income & Expense
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Total Trips</div>
            <div class="stat-value">{{ $totalTrips }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Total Kilometers</div>
            <div class="stat-value text-info">{{ number_format($totalKm, 2) }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Total Freight</div>
            <div class="stat-value text-success">{{ $totalFreight }}</div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card">
          <div class="card-body">
            <div class="stat-label">Average Rate</div>
            <div class="stat-value text-warning">{{ $totalTrips > 0 ? number_format($totalFreight / $totalKm, 2) : '0.00' }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="panel mb-3">
      <div class="row">
        <div class="col-md-2">
          <label class="form-label">Vhl No</label>
          <input type="text" class="form-control" id="vehicleNumber" value="{{ request('vehicle_number') }}" placeholder="Vehicle Number">
        </div>
        <div class="col-md-2">
          <label class="form-label">Date From</label>
          <input type="date" class="form-control" id="dateFrom" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label">Date To</label>
          <input type="date" class="form-control" id="dateTo" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Warehouse Location</label>
          <select class="form-select" id="warehouseLocation" onchange="changeWarehouseLocation()">
            <option value="">All Warehouses</option>
            @foreach($warehouses as $warehouse)
              <option value="{{ $warehouse->location }}" {{ $warehouseLocation === $warehouse->location ? 'selected' : '' }}>{{ $warehouse->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" id="searchTrips" placeholder="Search by vehicle, GP#, or delivery point...">
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-12 text-end">
          <button class="btn btn-secondary" onclick="applyFilters()" id="applyFiltersBtn" style="pointer-events: auto !important; opacity: 1 !important;">Apply Filters</button>
          <button class="btn btn-outline-secondary" onclick="clearFilters()" id="clearFiltersBtn" style="pointer-events: auto !important; opacity: 1 !important;">Clear Filters</button>
        </div>
      </div>
    </div>

    <!-- Excel-like Table -->
    <div class="panel">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tripsTable">
          <thead class="table-light">
            <tr>
              <th style="width: 50px;">Sr</th>
              <th style="width: 100px;">Date</th>
              <th style="width: 120px;">Vhl No</th>
              <th style="width: 100px;">GP#</th>
              <th style="width: 200px;">Drop/Delivery Point</th>
              <th style="width: 80px;">Vhl</th>
              <th style="width: 80px;">Km</th>
              <th style="width: 80px;">Rate</th>
              <th style="width: 100px;">FRT</th>
              <th style="width: 100px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($trips as $index => $trip)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $trip->trip_date->format('d/m/Y') }}</td>
                <td>{{ $trip->vehicle_number }}</td>
                <td>{{ $trip->gp_number }}</td>
                <td>{{ $trip->delivery_point }}</td>
                <td>{{ $trip->vehicle_type }}</td>
                <td>{{ number_format($trip->kilometers, 2) }}</td>
                <td>{{ number_format($trip->rate_per_km, 2) }}</td>
                <td><strong>{{ number_format($trip->freight, 2) }}</strong></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editTrip({{ $trip->id }}); return false;" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteTrip({{ $trip->id }})" title="Delete">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot class="table-light">
            <tr>
              <td colspan="6" class="text-end"><strong>TOTAL:</strong></td>
              <td><strong>{{ number_format($totalKm, 2) }}</strong></td>
              <td>-</td>
              <td><strong>{{ number_format($totalFreight, 2) }}</strong></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
          Showing {{ $trips->firstItem() }} to {{ $trips->lastItem() }} of {{ $trips->total() }} entries
        </div>
        {{ $trips->links() }}
      </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Warehouse Invoice</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="invoiceContent">
            <!-- Invoice content will be loaded here -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="printInvoice()">
              <i class="bi bi-printer me-1"></i> Print Invoice
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Trip Modal -->
    <div class="modal fade" id="tripModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tripModalLabel">Add New Trip</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="tripForm" action="/warehouse-trips" method="POST">
            @csrf
            <div class="modal-body">
              <input type="hidden" name="trip_id" id="trip_id">
              <input type="hidden" name="_method" id="_method" value="POST">
              
              <!-- Basic Information -->
              <h6 class="mb-3">Trip Information</h6>
              <div class="row">
                <div class="col-md-3 mb-3">
                  <label for="trip_date" class="form-label">Date</label>
                  <input type="date" class="form-control" name="trip_date" id="trip_date" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="business_category" class="form-label">Business Category *</label>
                  <select class="form-select" name="business_category" id="business_category" required>
                    <option value="">Select Category</option>
                    <option value="Open Market Work">Open Market Work</option>
                    <option value="Buyer Supply Chain">Buyer Supply Chain</option>
                    <option value="Buyer Breading">Buyer Breading</option>
                    <option value="Buyer Seed Supply">Buyer Seed Supply</option>
                    <option value="Buyer Marketing Development">Buyer Marketing Development</option>
                    <option value="Buyer S.P.R">Buyer S.P.R</option>
                    <option value="Syngenta">Syngenta</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="vehicle_number" class="form-label">Vehicle Number</label>
                  <select class="form-select" name="vehicle_number" id="vehicle_number" required>
                    <option value="">Select Vehicle</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="gp_number" class="form-label">GP Number</label>
                  <input type="text" class="form-control" name="gp_number" id="gp_number" required>
                </div>
              </div>

              <!-- Hidden field for billing_month -->
              <input type="hidden" name="billing_month" id="billing_month">

              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="delivery_point" class="form-label">Delivery Point</label>
                  <input type="text" class="form-control" name="delivery_point" id="delivery_point" required>
                </div>
                <div class="col-md-2 mb-3">
                  <label for="vehicle_type" class="form-label">Vehicle Type</label>
                  <select class="form-select" name="vehicle_type" id="vehicle_type" required>
                    <option value="1T">1T</option>
                    <option value="2T">2T</option>
                    <option value="4T">4T</option>
                    <option value="8T">8T</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="warehouse_location" class="form-label">Warehouse Location</label>
                  <select class="form-select" name="warehouse_location" id="warehouse_location" required>
                    <option value="">Select Warehouse</option>
                    @foreach($warehouses as $warehouse)
                      <option value="{{ $warehouse->location }}" {{ $warehouseLocation === $warehouse->location ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="driver_name" class="form-label">Driver Name</label>
                  <select class="form-select" name="driver_name" id="driver_name">
                    <option value="">Select Driver</option>
                  </select>
                </div>
              </div>

              <!-- Financial Information -->
              <h6 class="mb-3 mt-4">Financial Information</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="kilometers" class="form-label">Kilometers</label>
                  <input type="number" step="0.01" class="form-control" name="kilometers" id="kilometers" required oninput="calculateFreight()">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="rate_per_km" class="form-label">Rate per KM</label>
                  <input type="number" step="0.01" class="form-control" name="rate_per_km" id="rate_per_km" required oninput="calculateFreight()">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="freight" class="form-label">Freight (Auto-calculated)</label>
                  <input type="number" step="0.01" class="form-control" name="freight" id="freight" readonly>
                </div>
              </div>

              <!-- Expense Entries -->
              <h6 class="mb-3 mt-4">Expense Entries</h6>
              <div class="card mb-3" style="background-color: #f8f9fa; border-left: 4px solid #dc3545;">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="total_expense" class="form-label fw-bold">Total Expense</label>
                      <input type="number" step="0.01" class="form-control bg-light" id="total_expense" name="total_expense" readonly value="0.00" style="font-weight: bold; color: #dc3545;">
                      <small class="text-muted">Auto-calculated from expense entries below</small>
                    </div>
                  </div>

                  <!-- Expense Entries Container -->
                  <div id="expenseEntriesContainer">
                    <div class="expense-entry row mb-3 p-3 border rounded" style="background-color: white;">
                      <div class="col-md-3 mb-2">
                        <label class="form-label">Expense Category</label>
                        <select class="form-select expense-category" name="expense_entries[0][expense_category]">
                          <option value="">Select Category</option>
                          <option value="fuel">Fuel</option>
                          <option value="toll">Toll</option>
                          <option value="food">Food</option>
                          <option value="challan">Challan</option>
                          <option value="police">Police</option>
                          <option value="driver_payment">Driver Payment</option>
                          <option value="maintenance">Maintenance</option>
                          <option value="loading_charges">Loading</option>
                          <option value="unloading_charges">Unloading</option>
                          <option value="other">Others</option>
                        </select>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label class="form-label">Payment Type</label>
                        <select class="form-select expense-payment-type" name="expense_entries[0][payment_type]">
                          <option value="">Select Type</option>
                          <option value="credit">Credit</option>
                          <option value="cash">Cash</option>
                        </select>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control expense-amount" name="expense_entries[0][amount]" placeholder="0.00" oninput="calculateTotalExpense()">
                      </div>
                      <div class="col-md-3 mb-2">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control expense-description" name="expense_entries[0][description]" placeholder="Description">
                      </div>
                      <div class="col-12">
                        <button type="button" class="btn btn-sm btn-danger d-none remove-expense-entry" onclick="removeExpenseEntry(this)">
                          <i class="bi bi-trash"></i> Remove
                        </button>
                      </div>
                    </div>
                  </div>

                  <button type="button" class="btn btn-outline-danger btn-sm" onclick="addExpenseEntry()">
                    <i class="bi bi-plus-circle"></i> Add Expense Entry
                  </button>
                </div>
              </div>

              <!-- Additional Information -->
              <h6 class="mb-3 mt-4">Additional Information</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="freight_bill_no" class="form-label">Freight Bill No</label>
                  <input type="text" class="form-control" name="freight_bill_no" id="freight_bill_no">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select class="form-select" name="status" id="status" required>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="billed">Billed</option>
                  </select>
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
                <i class="bi bi-check-lg me-1"></i> Save Trip
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Mini Goods &middot; Warehouse Management System
    </div>
  </div>

  <script>
    let tripModal, invoiceModal;

    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded, initializing...');

      // Enable filter buttons
      const applyBtn = document.getElementById('applyFiltersBtn');
      const clearBtn = document.getElementById('clearFiltersBtn');
      if (applyBtn) {
        applyBtn.disabled = false;
        applyBtn.removeAttribute('disabled');
        applyBtn.style.pointerEvents = 'auto';
        applyBtn.style.opacity = '1';
      }
      if (clearBtn) {
        clearBtn.disabled = false;
        clearBtn.removeAttribute('disabled');
        clearBtn.style.pointerEvents = 'auto';
        clearBtn.style.opacity = '1';
      }

      // Initialize modals
      tripModal = new bootstrap.Modal(document.getElementById('tripModal'));
      invoiceModal = new bootstrap.Modal(document.getElementById('invoiceModal'));
      console.log('Modals initialized');

      // Search functionality
      document.getElementById('searchTrips').addEventListener('input', function() {
        filterTrips();
      });

      // Initialize modal for create button
      document.querySelector('[data-bs-target="#tripModal"]').addEventListener('click', openCreateModal);

      // Test button clicks
      document.getElementById('exportBtn').addEventListener('click', function(e) {
        console.log('Export button clicked via event listener');
      });
      
      document.getElementById('invoiceBtn').addEventListener('click', function(e) {
        console.log('Invoice button clicked via event listener');
      });

      // Form submission
      document.getElementById('tripForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = this.action;
        const method = document.getElementById('_method').value;

        console.log('Submitting form to:', url);
        console.log('Method:', method);
        console.log('Form data:', Object.fromEntries(formData));

        // Ensure _method is set in FormData
        if (!formData.has('_method')) {
          formData.append('_method', method);
        }

        fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(response => {
          console.log('Response status:', response.status);
          if (!response.ok) {
            return response.json().then(err => {
              throw err;
            });
          }
          return response.json();
        })
        .then(data => {
          console.log('Response data:', data);
          if (data.success) {
            Swal.fire({
              title: 'Success!',
              text: method === 'PUT' ? 'Trip updated successfully!' : 'Trip added successfully!',
              icon: 'success',
              confirmButtonColor: '#3085d6'
            }).then(() => {
              tripModal.hide();
              // Reload the page to show the new trip
              window.location.reload();
            });
          } else {
            Swal.fire({
              title: 'Error',
              text: data.message || 'Failed to save trip',
              icon: 'error',
              confirmButtonColor: '#d33'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          let errorMessage = 'Failed to save trip';

          if (error.errors) {
            // Display validation errors
            const errorMessages = Object.values(error.errors).flat();
            errorMessage = errorMessages.join('\n');
          } else if (error.message) {
            errorMessage = error.message;
          }

          Swal.fire({
            title: 'Error',
            text: errorMessage,
            icon: 'error',
            confirmButtonColor: '#d33'
          });
        });
      });
    });

    // Global functions
    function filterTrips() {
      const search = document.getElementById('searchTrips').value.toLowerCase();
      const rows = document.querySelectorAll('#tripsTable tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
      });
    }

    function clearFilters() {
      document.getElementById('searchTrips').value = '';
      document.getElementById('vehicleNumber').value = '';
      document.getElementById('dateFrom').value = '';
      document.getElementById('dateTo').value = '';
      document.getElementById('warehouseLocation').value = '';
      window.location.href = '?';
    }

    function calculateFreight() {
      const kilometers = parseFloat(document.getElementById('kilometers').value) || 0;
      const ratePerKm = parseFloat(document.getElementById('rate_per_km').value) || 0;
      const freight = kilometers * ratePerKm;
      document.getElementById('freight').value = freight.toFixed(2);
    }

    function calculateTotalExpense() {
      const expenseAmounts = document.querySelectorAll('.expense-amount');
      let total = 0;
      expenseAmounts.forEach(input => {
        total += parseFloat(input.value) || 0;
      });
      document.getElementById('total_expense').value = total.toFixed(2);
    }

    function addExpenseEntry() {
      const container = document.getElementById('expenseEntriesContainer');
      const entryCount = container.querySelectorAll('.expense-entry').length;
      const newEntry = document.createElement('div');
      newEntry.className = 'expense-entry row mb-3 p-3 border rounded';
      newEntry.style.backgroundColor = 'white';
      newEntry.innerHTML = `
        <div class="col-md-3 mb-2">
          <label class="form-label">Expense Category</label>
          <select class="form-select expense-category" name="expense_entries[${entryCount}][expense_category]">
            <option value="">Select Category</option>
            <option value="fuel">Fuel</option>
            <option value="toll">Toll</option>
            <option value="food">Food</option>
            <option value="challan">Challan</option>
            <option value="police">Police</option>
            <option value="driver_payment">Driver Payment</option>
            <option value="maintenance">Maintenance</option>
            <option value="loading_charges">Loading</option>
            <option value="unloading_charges">Unloading</option>
            <option value="other">Others</option>
          </select>
        </div>
        <div class="col-md-3 mb-2">
          <label class="form-label">Payment Type</label>
          <select class="form-select expense-payment-type" name="expense_entries[${entryCount}][payment_type]">
            <option value="">Select Type</option>
            <option value="credit">Credit</option>
            <option value="cash">Cash</option>
          </select>
        </div>
        <div class="col-md-3 mb-2">
          <label class="form-label">Amount</label>
          <input type="number" step="0.01" class="form-control expense-amount" name="expense_entries[${entryCount}][amount]" placeholder="0.00" oninput="calculateTotalExpense()">
        </div>
        <div class="col-md-3 mb-2">
          <label class="form-label">Description</label>
          <input type="text" class="form-control expense-description" name="expense_entries[${entryCount}][description]" placeholder="Description">
        </div>
        <div class="col-12">
          <button type="button" class="btn btn-sm btn-danger" onclick="removeExpenseEntry(this)">
            <i class="bi bi-trash"></i> Remove
          </button>
        </div>
      `;
      container.appendChild(newEntry);
    }

    function removeExpenseEntry(button) {
      const entry = button.closest('.expense-entry');
      entry.remove();
      calculateTotalExpense();
    }

    function changeWarehouseLocation() {
      applyFilters();
    }

    function applyFilters() {
      const vehicleNumber = document.getElementById('vehicleNumber').value;
      const warehouseLocation = document.getElementById('warehouseLocation').value;
      const dateFrom = document.getElementById('dateFrom').value;
      const dateTo = document.getElementById('dateTo').value;

      let params = new URLSearchParams();
      if (vehicleNumber) params.append('vehicle_number', vehicleNumber);
      if (warehouseLocation) params.append('warehouse_location', warehouseLocation);
      if (dateFrom) params.append('date_from', dateFrom);
      if (dateTo) params.append('date_to', dateTo);

      window.location.href = `?${params.toString()}`;
    }

    function openCreateModal() {
      document.getElementById('tripForm').reset();
      document.getElementById('trip_id').value = '';
      document.getElementById('_method').value = 'POST';
      document.getElementById('tripForm').action = '{{ route('warehouse-trips.store') }}';
      document.getElementById('tripModalLabel').textContent = 'Add New Trip';

      // Set current billing month
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
      const now = new Date();
      const billingMonth = monthNames[now.getMonth()] + '-' + now.getFullYear();
      document.getElementById('billing_month').value = billingMonth;

      // Load vehicles and drivers
      loadVehicles();
      loadDrivers();

      tripModal.show();
    }

    function loadVehicles() {
      return fetch('/vehicles')
        .then(response => response.json())
        .then(data => {
          const vehicleSelect = document.getElementById('vehicle_number');
          vehicleSelect.innerHTML = '<option value="">Select Vehicle</option>';
          if (data.vehicles && data.vehicles.length > 0) {
            data.vehicles.forEach(vehicle => {
              const option = document.createElement('option');
              option.value = vehicle.reg_no;
              option.textContent = vehicle.reg_no;
              vehicleSelect.appendChild(option);
            });
          }
        })
        .catch(error => {
          console.error('Error loading vehicles:', error);
          throw error;
        });
    }

    function loadDrivers() {
      return fetch('/drivers')
        .then(response => response.json())
        .then(data => {
          const driverSelect = document.getElementById('driver_name');
          driverSelect.innerHTML = '<option value="">Select Driver</option>';
          if (data.drivers && data.drivers.length > 0) {
            data.drivers.forEach(driver => {
              const option = document.createElement('option');
              option.value = driver.name;
              option.textContent = driver.name;
              driverSelect.appendChild(option);
            });
          }
        })
        .catch(error => {
          console.error('Error loading drivers:', error);
          throw error;
        });
    }

    function editTrip(id) {
      console.log('editTrip called with id:', id);

      // Use the dedicated edit data route
      fetch(`/warehouse-trips/${id}/edit-data`, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        }
      })
      .then(response => {
        console.log('Edit response status:', response.status);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log('Edit trip data:', data);

        if (!data.trip) {
          console.error('No trip data in response');
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load trip data'
          });
          return;
        }
        
        const trip = data.trip;
        document.getElementById('trip_id').value = trip.id;
        document.getElementById('_method').value = 'PUT';
        document.getElementById('tripForm').action = `/warehouse-trips/${id}`;
        document.getElementById('tripModalLabel').textContent = 'Edit Trip';

        // Load vehicles and drivers first, then populate form
        Promise.all([loadVehicles(), loadDrivers()]).then(() => {
          // Populate form fields with simple direct assignment
          console.log('Populating form with trip data:', trip);

          // Simple date formatting
          if (trip.trip_date) {
            try {
              const date = new Date(trip.trip_date);
              if (!isNaN(date)) {
                document.getElementById('trip_date').value = date.toISOString().split('T')[0];

                // Calculate and set billing month from trip date
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const billingMonth = monthNames[date.getMonth()] + '-' + date.getFullYear();
                document.getElementById('billing_month').value = billingMonth;
              } else {
                document.getElementById('trip_date').value = '';
                document.getElementById('billing_month').value = '';
              }
            } catch (e) {
              document.getElementById('trip_date').value = '';
              document.getElementById('billing_month').value = '';
            }
          } else {
            document.getElementById('trip_date').value = '';
            document.getElementById('billing_month').value = '';
          }

          document.getElementById('vehicle_number').value = trip.vehicle_number || '';
          document.getElementById('gp_number').value = trip.gp_number || '';
          document.getElementById('delivery_point').value = trip.delivery_point || '';
          document.getElementById('vehicle_type').value = trip.vehicle_type || '2T';
          document.getElementById('business_category').value = trip.business_category || '';
          document.getElementById('driver_name').value = trip.driver_name || '';
          document.getElementById('kilometers').value = trip.kilometers || '';
          document.getElementById('rate_per_km').value = trip.rate_per_km || '';
          document.getElementById('freight').value = trip.freight || '';
          document.getElementById('freight_bill_no').value = trip.freight_bill_no || '';
          document.getElementById('warehouse_location').value = trip.warehouse_location || '';
          document.getElementById('status').value = trip.status || 'pending';
          document.getElementById('notes').value = trip.notes || '';

          console.log('Form populated successfully');

          tripModal.show();
        }).catch(error => {
          console.error('Error loading vehicles/drivers:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load vehicle/driver data'
          });
        });
      })
      .catch(error => {
        console.error('Error loading trip:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to load trip details'
        });
      });
    }

    function deleteTrip(id) {
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
          fetch(`/warehouse-trips/${id}`, {
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
                text: 'Trip has been deleted successfully.',
                icon: 'success',
                confirmButtonColor: '#3085d6'
              }).then(() => {
                location.reload();
              });
            } else {
              Swal.fire({
                title: 'Error',
                text: 'Failed to delete trip',
                icon: 'error',
                confirmButtonColor: '#d33'
              });
            }
          })
          .catch(error => {
            console.error('Error deleting trip:', error);
            Swal.fire({
              title: 'Error',
              text: 'Failed to delete trip',
              icon: 'error',
              confirmButtonColor: '#d33'
            });
          });
        }
      });
    }

    function calculateFreight() {
      const km = parseFloat(document.getElementById('kilometers').value) || 0;
      const rate = parseFloat(document.getElementById('rate_per_km').value) || 0;
      const freight = km * rate;
      document.getElementById('freight').value = freight.toFixed(2);
    }

    function exportTrips() {
      console.log('exportTrips function called');
      const vehicleNumber = document.getElementById('vehicleNumber').value;
      const warehouseLocation = document.getElementById('warehouseLocation').value;
      const dateFrom = document.getElementById('dateFrom').value;
      const dateTo = document.getElementById('dateTo').value;

      let params = new URLSearchParams();
      if (vehicleNumber) params.append('vehicle_number', vehicleNumber);
      if (warehouseLocation) params.append('warehouse_location', warehouseLocation);
      if (dateFrom) params.append('date_from', dateFrom);
      if (dateTo) params.append('date_to', dateTo);

      console.log('Export called with:', { vehicleNumber, warehouseLocation, dateFrom, dateTo });
      window.location.href = `/warehouse-trips/export?${params.toString()}`;
    }

    function generateInvoice(invoiceType = 'basic') {
      const vehicleNumber = document.getElementById('vehicleNumber').value;
      const warehouseLocation = document.getElementById('warehouseLocation').value || 'Depalpur';
      const dateFrom = document.getElementById('dateFrom').value;
      const dateTo = document.getElementById('dateTo').value;

      // Calculate billing month from date range or use current month
      let billingMonth = '{{ date('F-Y') }}';
      let useCurrentMonth = false;

      // Check if date range is selected
      if (dateFrom && dateTo) {
        const date = new Date(dateFrom);
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        billingMonth = monthNames[date.getMonth()] + '-' + date.getFullYear();
      } else {
        useCurrentMonth = true;
      }

      console.log('Generate invoice called with:', { billingMonth, warehouseLocation, invoiceType, dateFrom, dateTo, vehicleNumber, useCurrentMonth });

      const invoiceTitle = invoiceType === 'with_expenses' ? 'Invoice with Income & Expense' : 'Generate Invoice';

      // Check if any filters are applied
      const hasFilters = vehicleNumber || warehouseLocation !== 'Depalpur' || (dateFrom && dateTo);

      // Show warning only if no filters at all and no date range
      if (!hasFilters && useCurrentMonth) {
        Swal.fire({
          title: 'No Filters Applied',
          text: 'You have not selected any filters. The invoice will be generated for the current month (' + billingMonth + ') at ' + warehouseLocation + '. Do you want to continue?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, generate',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            proceedWithInvoiceGeneration(billingMonth, warehouseLocation, invoiceType, dateFrom, dateTo, vehicleNumber);
          }
        });
      } else {
        // Build confirmation message based on filters
        let confirmText = `Generate ${invoiceType === 'with_expenses' ? 'invoice with income & expense details' : 'basic invoice'}`;
        if (useCurrentMonth) {
          confirmText += ` for ${billingMonth}`;
        }
        if (vehicleNumber) {
          confirmText += ` for vehicle ${vehicleNumber}`;
        }
        confirmText += ` at ${warehouseLocation}?`;

        Swal.fire({
          title: invoiceTitle,
          text: confirmText,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, generate!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            proceedWithInvoiceGeneration(billingMonth, warehouseLocation, invoiceType, dateFrom, dateTo, vehicleNumber);
          }
        });
      }
    }

    function proceedWithInvoiceGeneration(billingMonth, warehouseLocation, invoiceType, dateFrom, dateTo, vehicleNumber) {
      console.log('Proceeding with invoice generation:', { billingMonth, warehouseLocation, invoiceType, dateFrom, dateTo, vehicleNumber });

      const payload = {
        billing_month: billingMonth,
        warehouse_location: warehouseLocation,
        invoice_type: invoiceType
      };

      // Add filters if provided
      if (dateFrom) payload.date_from = dateFrom;
      if (dateTo) payload.date_to = dateTo;
      if (vehicleNumber) payload.vehicle_number = vehicleNumber;

      fetch('/warehouse-trips/generate-invoice', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      })
      .then(response => {
        console.log('Response received:', response.status);
        return response.json();
      })
      .then(data => {
        console.log('Response data:', data);
        if (data.success) {
          // Show invoice in modal
          document.getElementById('invoiceContent').innerHTML = data.invoice_html;
          invoiceModal.show();

          Swal.fire({
            title: 'Invoice Generated!',
            text: data.message,
            icon: 'success',
            confirmButtonColor: '#3085d6',
            timer: 1500,
            showConfirmButton: false
          });
        } else {
          Swal.fire({
            title: 'Error',
            text: data.message || 'Failed to generate invoice',
            icon: 'error',
            confirmButtonColor: '#d33'
          });
        }
      })
      .catch(error => {
        console.error('Error generating invoice:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to generate invoice',
          icon: 'error',
          confirmButtonColor: '#d33'
        });
      });
    }

    function printInvoice() {
      console.log('printInvoice function called');
      const invoiceContent = document.getElementById('invoiceContent').innerHTML;
      console.log('Invoice content length:', invoiceContent.length);
      
      const printWindow = window.open('', '_blank');
      if (!printWindow) {
        console.error('Failed to open print window');
        alert('Please allow popups for this site to print invoices');
        return;
      }
      
      var htmlContent = '<!DOCTYPE html><html><head><title>Warehouse Invoice</title>';
      htmlContent += '<style>body{font-family:Arial,sans-serif;margin:20px;background:#f0f0f0}';
      htmlContent += '.invoice-container{max-width:900px;margin:0 auto;background:white;padding:30px;border:1px solid #ddd;box-shadow:0 0 10px rgba(0,0,0,0.1)}';
      htmlContent += '.header{text-align:center;border-bottom:2px solid #333;padding-bottom:20px;margin-bottom:20px}';
      htmlContent += '.company-name{font-size:24px;font-weight:bold;color:#0066cc;margin-bottom:5px}';
      htmlContent += '.company-details{font-size:14px;color:#666;margin-bottom:10px}';
      htmlContent += '.invoice-info{display:flex;justify-content:space-between;margin-bottom:20px}';
      htmlContent += '.invoice-number{font-size:18px;font-weight:bold}';
      htmlContent += '.billing-info{font-size:14px}';
      htmlContent += 'table{width:100%;border-collapse:collapse;margin-bottom:20px}';
      htmlContent += 'th{background:#0066cc;color:white;padding:12px;text-align:left;font-weight:bold;border:1px solid #0055aa}';
      htmlContent += 'td{padding:10px;border:1px solid #ddd}';
      htmlContent += '.total-row{background:#f0f8ff;font-weight:bold}';
      htmlContent += '.footer{margin-top:30px;padding-top:20px;border-top:1px solid #ddd;text-align:center;font-size:12px;color:#666}';
      htmlContent += '.signature-section{margin-top:40px;display:flex;justify-content:space-between}';
      htmlContent += '.signature-box{width:200px;text-align:center}';
      htmlContent += '.signature-line{border-top:1px solid #333;margin-top:60px;padding-top:5px}';
      htmlContent += '.print-btn{position:fixed;top:20px;right:20px;padding:10px 20px;background:#0066cc;color:white;border:none;border-radius:5px;cursor:pointer;font-size:16px;z-index:1000}';
      htmlContent += '.print-btn:hover{background:#0055aa}';
      htmlContent += '@media print{body{background:white;padding:0}.invoice-container{border:none;box-shadow:none}.print-btn{display:none}}';
      htmlContent += '</style></head><body>';
      htmlContent += '<button class="print-btn" onclick="window.print()">🖨️ Print</button>';
      htmlContent += '<div class="invoice-container">' + invoiceContent + '</div>';
      htmlContent += '<script>window.onload=function(){setTimeout(function(){window.print()},500)}<\/script>';
      htmlContent += '</body></html>';
      
      printWindow.document.write(htmlContent);
      printWindow.document.close();
      console.log('Print window created and closed');
    }
  </script>
@endsection