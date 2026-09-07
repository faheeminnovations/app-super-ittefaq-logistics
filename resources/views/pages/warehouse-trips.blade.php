@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Warehouse Management</div>
        <h1>Warehouse Trips - {{ $billingMonth }}</h1>
        <div class="sub">Excel-like trip management for Depalpur Warehouse</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tripModal">
          <i class="bi bi-plus-lg me-1"></i> Add Trip
        </button>
        <button class="btn btn-outline-navy" onclick="exportTrips()" id="exportBtn">
          <i class="bi bi-download me-1"></i> Export Excel
        </button>
        <button class="btn btn-success" onclick="generateInvoice()" id="invoiceBtn">
          <i class="bi bi-file-earmark-text me-1"></i> Generate Invoice
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
        <div class="col-md-3">
          <label class="form-label">Billing Month</label>
          <select class="form-select" id="billingMonth" onchange="changeBillingMonth()">
            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
              <option value="{{ $month }}-2026" {{ $billingMonth === $month . '-2026' ? 'selected' : '' }}>{{ $month }} 2026</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Warehouse Location</label>
          <select class="form-select" id="warehouseLocation" onchange="changeWarehouseLocation()">
            <option value="DEPALPUR" {{ $warehouseLocation === 'DEPALPUR' ? 'selected' : '' }}>Depalpur</option>
            <option value="MULTAN" {{ $warehouseLocation === 'MULTAN' ? 'selected' : '' }}>Multan</option>
            <option value="SAHIWAL" {{ $warehouseLocation === 'SAHIWAL' ? 'selected' : '' }}>Sahiwal</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" id="searchTrips" placeholder="Search by vehicle, GP#, or delivery point...">
        </div>
        <div class="col-md-3">
          <label class="form-label">&nbsp;</label>
          <button class="btn btn-secondary w-100" onclick="clearFilters()">Clear Filters</button>
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
                <div class="col-md-4 mb-3">
                  <label for="trip_date" class="form-label">Date</label>
                  <input type="date" class="form-control" name="trip_date" id="trip_date" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="vehicle_number" class="form-label">Vehicle Number</label>
                  <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="gp_number" class="form-label">GP Number</label>
                  <input type="text" class="form-control" name="gp_number" id="gp_number" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="delivery_point" class="form-label">Delivery Point</label>
                  <input type="text" class="form-control" name="delivery_point" id="delivery_point" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="vehicle_type" class="form-label">Vehicle Type</label>
                  <select class="form-select" name="vehicle_type" id="vehicle_type" required>
                    <option value="1T">1T</option>
                    <option value="2T">2T</option>
                    <option value="4T">4T</option>
                    <option value="8T">8T</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="billing_month" class="form-label">Billing Month</label>
                  <select class="form-select" name="billing_month" id="billing_month" required>
                    @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                      <option value="{{ $month }}-2026" {{ $billingMonth === $month . '-2026' ? 'selected' : '' }}>{{ $month }} 2026</option>
                    @endforeach
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

              <!-- Additional Information -->
              <h6 class="mb-3 mt-4">Additional Information</h6>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="fuel_type" class="form-label">Fuel Type</label>
                  <select class="form-select" name="fuel_type" id="fuel_type">
                    <option value="">Select</option>
                    <option value="CASH">CASH</option>
                    <option value="CREDIT">CREDIT</option>
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="driver_name" class="form-label">Driver Name</label>
                  <input type="text" class="form-control" name="driver_name" id="driver_name">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="load_id" class="form-label">Load ID</label>
                  <input type="text" class="form-control" name="load_id" id="load_id">
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="freight_bill_no" class="form-label">Freight Bill No</label>
                  <input type="text" class="form-control" name="freight_bill_no" id="freight_bill_no">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="warehouse_location" class="form-label">Warehouse Location</label>
                  <select class="form-select" name="warehouse_location" id="warehouse_location" required>
                    <option value="DEPALPUR">Depalpur</option>
                    <option value="MULTAN">Multan</option>
                    <option value="SAHIWAL">Sahiwal</option>
                  </select>
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

        fetch(url, {
          method: method,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          },
          body: formData
        })
        .then(response => {
          console.log('Response status:', response.status);
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
          Swal.fire({
            title: 'Error',
            text: 'Failed to save trip: ' + error.message,
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
      filterTrips();
    }

    function changeBillingMonth() {
      const billingMonth = document.getElementById('billingMonth').value;
      window.location.href = `?billing_month=${billingMonth}`;
    }

    function changeWarehouseLocation() {
      const warehouseLocation = document.getElementById('warehouseLocation').value;
      const billingMonth = document.getElementById('billingMonth').value;
      window.location.href = `?billing_month=${billingMonth}&warehouse_location=${warehouseLocation}`;
    }

    function openCreateModal() {
      document.getElementById('tripForm').reset();
      document.getElementById('trip_id').value = '';
      document.getElementById('_method').value = 'POST';
      document.getElementById('tripForm').action = '{{ route('warehouse-trips.store') }}';
      document.getElementById('tripModalLabel').textContent = 'Add New Trip';
      document.getElementById('billing_month').value = '{{ $billingMonth }}';
      document.getElementById('warehouse_location').value = '{{ $warehouseLocation }}';
      tripModal.show();
    }

    function editTrip(id) {
      alert('Edit trip function called with ID: ' + id);
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
        alert('Data received: ' + JSON.stringify(data));
        
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

        // Populate form fields with simple direct assignment
        console.log('Populating form with trip data:', trip);
        
        // Simple date formatting
        if (trip.trip_date) {
          try {
            const date = new Date(trip.trip_date);
            if (!isNaN(date)) {
              document.getElementById('trip_date').value = date.toISOString().split('T')[0];
            } else {
              document.getElementById('trip_date').value = '';
            }
          } catch (e) {
            document.getElementById('trip_date').value = '';
          }
        } else {
          document.getElementById('trip_date').value = '';
        }
        
        document.getElementById('vehicle_number').value = trip.vehicle_number || '';
        document.getElementById('gp_number').value = trip.gp_number || '';
        document.getElementById('delivery_point').value = trip.delivery_point || '';
        document.getElementById('vehicle_type').value = trip.vehicle_type || '2T';
        document.getElementById('kilometers').value = trip.kilometers || '';
        document.getElementById('rate_per_km').value = trip.rate_per_km || '';
        document.getElementById('freight').value = trip.freight || '';
        document.getElementById('fuel_type').value = trip.fuel_type || '';
        document.getElementById('driver_name').value = trip.driver_name || '';
        document.getElementById('load_id').value = trip.load_id || '';
        document.getElementById('freight_bill_no').value = trip.freight_bill_no || '';
        document.getElementById('billing_month').value = trip.billing_month || '';
        document.getElementById('warehouse_location').value = trip.warehouse_location || 'DEPALPUR';
        document.getElementById('status').value = trip.status || 'pending';
        document.getElementById('notes').value = trip.notes || '';
        
        console.log('Form populated successfully');

        tripModal.show();
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
      const billingMonth = document.getElementById('billingMonth').value;
      const warehouseLocation = document.getElementById('warehouseLocation').value;
      console.log('Export called with:', { billingMonth, warehouseLocation });
      window.location.href = `/warehouse-trips/export?billing_month=${billingMonth}&warehouse_location=${warehouseLocation}`;
    }

    function generateInvoice() {
      const billingMonth = document.getElementById('billingMonth').value;
      const warehouseLocation = document.getElementById('warehouseLocation').value;
      
      console.log('Generate invoice called with:', { billingMonth, warehouseLocation });
      
      Swal.fire({
        title: 'Generate Invoice?',
        text: `Generate invoice for ${billingMonth} at ${warehouseLocation}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          console.log('User confirmed invoice generation');
          
          fetch('/warehouse-trips/generate-invoice', {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Content-Type': 'application/json',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              billing_month: billingMonth,
              warehouse_location: warehouseLocation
            })
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