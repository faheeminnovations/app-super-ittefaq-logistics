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
        <div class="eyebrow">Accounts</div>
        <h1>Billing Management</h1>
        <div class="sub">Monthly billing records, dues & payment tracking</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportBilling()"><i class="bi bi-download me-1"></i> Export CSV</button>
        <button class="btn btn-outline-navy" onclick="exportExcel()"><i class="bi bi-file-earmark-excel me-1"></i> Export Excel</button>
        <button class="btn btn-outline-navy" onclick="showImportModal()"><i class="bi bi-upload me-1"></i> Import Excel</button>
        <button class="btn btn-outline-navy" onclick="generateInvoice()"><i class="bi bi-file-earmark-text me-1"></i> Generate Invoice</button>
        <button class="btn btn-outline-navy" onclick="showMonthlySummary()"><i class="bi bi-bar-chart me-1"></i> Monthly Summary</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#billingModal" onclick="resetBillingForm()"><i class="bi bi-plus-lg me-1"></i> Add Record</button>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Summary Statistics -->
    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-currency-rupee"></i></div>
          <div class="label">Total Rent</div>
          <div class="value" id="totalRent">{{ number_format($totalRent, 2) }}</div>
          <div class="delta">This month</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
        <div class="label">Paid Records</div>
        <div class="value" id="paidCount">{{ $paidCount }}</div>
        <div class="delta up">Completed payments</div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-exclamation-circle"></i></div>
          <div class="label">Pending Records</div>
          <div class="value" id="pendingCount">{{ $pendingCount }}</div>
          <div class="delta down">Awaiting payment</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warning);"><i class="bi bi-wallet"></i></div>
          <div class="label">Total Dues</div>
          <div class="value" id="totalDues">{{ number_format($totalDues, 2) }}</div>
          <div class="delta">Outstanding amount</div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Billing Month</label>
            <input type="month" class="form-control" id="filter_month" value="{{ $currentMonth }}" onchange="filterBillings()">
          </div>
          <div class="col-md-3">
            <label class="form-label">Vehicle No</label>
            <select class="form-select" id="filter_vehicle" onchange="filterBillings()">
              <option value="">All Vehicles</option>
              @foreach($vehicles as $vehicle)
                <option value="{{ $vehicle->reg_no }}">{{ strtoupper($vehicle->reg_no) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Customer</label>
            <select class="form-select" id="filter_customer" onchange="filterBillings()">
              <option value="">All Customers</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->name }}">{{ ucwords(strtolower($customer->name)) }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="filter_status" onchange="filterBillings()">
              <option value="all">All Status</option>
              <option value="paid">Paid</option>
              <option value="pending">Pending</option>
              <option value="partial">Partial</option>
            </select>
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
                <th>Vhl No</th>
                <th>GP#</th>
                <th>Name</th>
                <th>Number</th>
                <th>Bag</th>
                <th>Drop/Delivery Point</th>
                <th>Vhl Type</th>
                <th>Km</th>
                <th>Rate</th>
                <th>FRT</th>
                <th>Rent</th>
                <th>Advance</th>
                <th>Advance Date</th>
                <th>Guarantor</th>
                <th>Dues</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="billingTableBody">
              @foreach($billings as $billing)
                <tr>
                  <td>{{ $billing->formatted_sr }}</td>
                  <td>{{ $billing->formatted_date }}</td>
                  <td>{{ $billing->formatted_vehicle_no }}</td>
                  <td>{{ $billing->formatted_gp_number }}</td>
                  <td>{{ $billing->formatted_customer_name }}</td>
                  <td>{{ $billing->formatted_contact_number }}</td>
                  <td>{{ $billing->formatted_bags }}</td>
                  <td>{{ $billing->formatted_delivery_point }}</td>
                  <td>{{ $billing->formatted_vehicle_type }}</td>
                  <td>{{ $billing->formatted_km_covered }}</td>
                  <td>{{ $billing->formatted_rate }}</td>
                  <td>{{ $billing->formatted_freight }}</td>
                  <td>{{ $billing->formatted_rent_amount }}</td>
                  <td>{{ $billing->formatted_advance_amount }}</td>
                  <td>{{ $billing->formatted_advance_date }}</td>
                  <td>{{ $billing->formatted_guarantor }}</td>
                  <td>{{ $billing->formatted_dues_amount }}</td>
                  <td>
                    <span class="badge bg-{{ $billing->status_badge_class }}">
                      {{ $billing->formatted_status }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editBilling({{ $billing->id }})">
                      <i class="bi bi-pencil"></i>
                    </button>
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
          <h5 class="modal-title" id="billingModalTitle">Add Billing Record</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetWizardOnClose()"></button>
        </div>
        <div class="modal-body">
          <!-- Wizard Progress -->
          <div class="mb-4">
            <div class="progress" style="height: 5px;">
              <div class="progress-bar" id="wizardProgress" role="progressbar" style="width: 50%"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <small class="text-muted">Step 1: Basic Information</small>
              <small class="text-muted">Step 2: Billing Details</small>
            </div>
          </div>

          <form id="billingForm">
            <input type="hidden" id="billing_id">
            
            <!-- Step 1: Basic Information -->
            <div id="step1" class="wizard-step">
              <div class="row g-3">
                <div class="col-md-2">
                  <label class="form-label">Sr <span class="text-muted">(Serial No)</span></label>
                  <input type="number" class="form-control" id="billing_sr" placeholder="1">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Date <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="billing_date" required>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Vehicle No <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="billing_vehicle_no" placeholder="ABC-123" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">GP#</label>
                  <input type="text" class="form-control" id="billing_gp_number" placeholder="1040">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="billing_customer_name" placeholder="Customer Name" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Contact Number</label>
                  <input type="text" class="form-control" id="billing_contact_number" placeholder="0300-1234567">
                </div>
              </div>
            </div>

            <!-- Step 2: Billing Details -->
            <div id="step2" class="wizard-step" style="display: none;">
              <div class="row g-3">
                <div class="col-md-2">
                  <label class="form-label">Bags</label>
                  <input type="number" class="form-control" id="billing_bags" value="0" min="0">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Delivery Point <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="billing_delivery_point" placeholder="Delivery Location" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Vehicle Type</label>
                  <input type="text" class="form-control" id="billing_vehicle_type" placeholder="2T">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Km Covered</label>
                  <input type="number" step="0.01" class="form-control" id="billing_km_covered" value="0" min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Rate</label>
                  <input type="number" step="0.01" class="form-control" id="billing_rate" value="0" min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Freight</label>
                  <input type="number" step="0.01" class="form-control" id="billing_freight" value="0" min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Rent (PKR) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" class="form-control" id="billing_rent" placeholder="0.00" required min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Advance (PKR)</label>
                  <input type="number" step="0.01" class="form-control" id="billing_advance" value="0" min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Advance Date</label>
                  <input type="date" class="form-control" id="billing_advance_date">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Guarantor</label>
                  <input type="text" class="form-control" id="billing_guarantor" placeholder="Guarantor Name">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Dues (PKR)</label>
                  <input type="number" step="0.01" class="form-control" id="billing_dues" value="0" min="0">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Status <span class="text-danger">*</span></label>
                  <select class="form-select" id="billing_status" required>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Partial">Partial</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Billing Month <span class="text-danger">*</span></label>
                  <input type="month" class="form-control" id="billing_month" required>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-outline-primary" id="prevBtn" onclick="prevStep()" style="display: none;">Previous</button>
          <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">Next</button>
          <button type="button" class="btn btn-success" id="submitBtn" onclick="saveBilling()" style="display: none;">Save Record</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Monthly Summary Modal -->
  <div class="modal fade" id="summaryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Monthly Billing Summary</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="summaryContent">
            <!-- Summary will be loaded here -->
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Import Modal -->
  <div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Import Excel File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Note:</strong> The Excel file should follow the format with columns: Sr, Date, Vhl No, GP#, Drop/Delivery Point, Vhl, Km, Rate, FRT
          </div>
          <form id="importForm">
            <div class="mb-3">
              <label class="form-label">Select Excel File</label>
              <input type="file" class="form-control" id="import_file" accept=".xlsx,.xls,.csv" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="processImport()">Import</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    let billingModal;
    let currentStep = 1;
    const totalSteps = 2;

    document.addEventListener('DOMContentLoaded', function() {
      billingModal = new bootstrap.Modal(document.getElementById('billingModal'));
      
      // Configure SweetAlert2 defaults
      Swal.mixin({
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      });
      
      // Set default billing month to current month
      document.getElementById('billing_month').value = '{{ $currentMonth }}';
      document.getElementById('billing_date').valueAsDate = new Date();
    });

    function resetBillingForm() {
      document.getElementById('billingForm').reset();
      document.getElementById('billing_id').value = '';
      document.getElementById('billingModalTitle').textContent = 'Add Billing Record';
      document.getElementById('billing_month').value = document.getElementById('filter_month').value;
      document.getElementById('billing_date').valueAsDate = new Date();
      
      // Reset wizard to step 1
      currentStep = 1;
      updateWizardUI();
    }

    function nextStep() {
      // Validate current step before proceeding
      if (currentStep === 1) {
        const date = document.getElementById('billing_date').value;
        const vehicleNo = document.getElementById('billing_vehicle_no').value;
        const customerName = document.getElementById('billing_customer_name').value;

        if (!date || !vehicleNo || !customerName) {
          Swal.fire({
            icon: 'warning',
            title: 'Missing Required Fields',
            text: 'Please fill in all required fields (Date, Vehicle No, Customer Name)'
          });
          return;
        }
      }

      if (currentStep < totalSteps) {
        currentStep++;
        updateWizardUI();
      }
    }

    function prevStep() {
      if (currentStep > 1) {
        currentStep--;
        updateWizardUI();
      }
    }

    function updateWizardUI() {
      // Hide all steps
      document.querySelectorAll('.wizard-step').forEach(step => {
        step.style.display = 'none';
      });

      // Show current step
      const currentStepEl = document.getElementById(`step${currentStep}`);
      if (currentStepEl) {
        currentStepEl.style.display = 'block';
      }

      // Update progress bar
      const progress = (currentStep / totalSteps) * 100;
      const progressBar = document.getElementById('wizardProgress');
      if (progressBar) {
        progressBar.style.width = progress + '%';
      }

      // Update buttons
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      const submitBtn = document.getElementById('submitBtn');

      if (prevBtn) prevBtn.style.display = currentStep === 1 ? 'none' : 'inline-block';
      if (nextBtn) nextBtn.style.display = currentStep === totalSteps ? 'none' : 'inline-block';
      if (submitBtn) submitBtn.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
    }

    function resetWizardOnClose() {
      // Reset wizard to step 1 when modal is closed
      setTimeout(() => {
        currentStep = 1;
        updateWizardUI();
      }, 300);
    }

    function editBilling(id) {
      // Fetch billing record and populate form
      fetch(`/api/billing/${id}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('billing_id').value = data.id;
          document.getElementById('billing_sr').value = data.sr || '';
          document.getElementById('billing_date').value = data.date || '';
          document.getElementById('billing_vehicle_no').value = data.vehicle_no || '';
          document.getElementById('billing_gp_number').value = data.gp_number || '';
          document.getElementById('billing_customer_name').value = data.customer_name || '';
          document.getElementById('billing_contact_number').value = data.contact_number || '';
          document.getElementById('billing_bags').value = data.bags || 0;
          document.getElementById('billing_delivery_point').value = data.delivery_point || '';
          document.getElementById('billing_vehicle_type').value = data.vehicle_type || '';
          document.getElementById('billing_km_covered').value = data.km_covered || 0;
          document.getElementById('billing_rate').value = data.rate || 0;
          document.getElementById('billing_freight').value = data.freight || 0;
          document.getElementById('billing_rent').value = data.rent || 0;
          document.getElementById('billing_advance').value = data.advance || 0;
          document.getElementById('billing_advance_date').value = data.advance_date || '';
          document.getElementById('billing_guarantor').value = data.guarantor || '';
          document.getElementById('billing_dues').value = data.dues || 0;
          document.getElementById('billing_status').value = data.status || 'Pending';
          document.getElementById('billing_month').value = data.billing_month || '';
          
          document.getElementById('billingModalTitle').textContent = 'Edit Billing Record';
          
          // Reset wizard to step 1 for editing
          currentStep = 1;
          updateWizardUI();
          
          billingModal.show();
        })
        .catch(error => {
          console.error('Error fetching billing record:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error loading billing record'
          });
        });
    }

    function saveBilling() {
      const id = document.getElementById('billing_id').value;
      
      // Validate all required fields before saving
      const date = document.getElementById('billing_date').value;
      const vehicleNo = document.getElementById('billing_vehicle_no').value;
      const customerName = document.getElementById('billing_customer_name').value;
      const deliveryPoint = document.getElementById('billing_delivery_point').value;
      const rent = document.getElementById('billing_rent').value;
      const status = document.getElementById('billing_status').value;
      const billingMonth = document.getElementById('billing_month').value;

      if (!date || !vehicleNo || !customerName || !deliveryPoint || !rent || !status || !billingMonth) {
        Swal.fire({
          icon: 'warning',
          title: 'Missing Required Fields',
          text: 'Please fill in all required fields'
        });
        return;
      }
      
      // Get form values with proper handling
      const sr = document.getElementById('billing_sr').value;
      const gpNumber = document.getElementById('billing_gp_number').value;
      const contactNumber = document.getElementById('billing_contact_number').value;
      const bags = document.getElementById('billing_bags').value;
      const vehicleType = document.getElementById('billing_vehicle_type').value;
      const kmCovered = document.getElementById('billing_km_covered').value;
      const rate = document.getElementById('billing_rate').value;
      const freight = document.getElementById('billing_freight').value;
      const advance = document.getElementById('billing_advance').value;
      const advanceDate = document.getElementById('billing_advance_date').value;
      const guarantor = document.getElementById('billing_guarantor').value;
      const dues = document.getElementById('billing_dues').value;

      const formData = {
        sr: sr ? parseInt(sr) : null,
        date: date,
        vehicle_no: vehicleNo.toUpperCase(),
        gp_number: gpNumber || null,
        customer_name: customerName,
        contact_number: contactNumber,
        bags: bags ? parseInt(bags) : 0,
        delivery_point: deliveryPoint,
        vehicle_type: vehicleType || null,
        km_covered: kmCovered ? parseFloat(kmCovered) : 0,
        rate: rate ? parseFloat(rate) : 0,
        freight: freight ? parseFloat(freight) : 0,
        rent: parseFloat(rent),
        advance: advance ? parseFloat(advance) : 0,
        advance_date: advanceDate || null,
        guarantor: guarantor || null,
        dues: dues ? parseFloat(dues) : 0,
        status: status,
        billing_month: billingMonth
      };

      const url = id ? `/billing/${id}` : '/billing';
      const method = id ? 'PUT' : 'POST';

      // Show loading state
      Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save the billing record',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

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
        Swal.close(); // Close loading state
        if (data.success) {
          billingModal.hide();
          // Reset wizard to step 1 for next use
          currentStep = 1;
          updateWizardUI();
          filterBillings();
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: data.message
          });
        } else {
          console.error('Validation errors:', data.errors);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'An error occurred'
          });
        }
      })
      .catch(error => {
        Swal.close(); // Close loading state
        console.error('Error:', error);
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
          // Show loading state
          Swal.fire({
            title: 'Deleting...',
            text: 'Please wait while we delete the billing record',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          fetch(`/billing/${id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
          })
          .then(response => response.json())
          .then(data => {
            Swal.close(); // Close loading state
            if (data.success) {
              filterBillings();
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
            Swal.close(); // Close loading state
            console.error('Error:', error);
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
      const vehicle = document.getElementById('filter_vehicle').value;
      const customer = document.getElementById('filter_customer').value;
      const status = document.getElementById('filter_status').value;

      fetch('/billing/filter', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          billing_month: month,
          vehicle_no: vehicle,
          customer_name: customer,
          status: status
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.billings) {
          updateBillingTable(data.billings);
        }
        if (data.totalRent !== undefined) {
          updateSummaryStats(data);
        }
      })
      .catch(error => {
        console.error('Error filtering billings:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error filtering billings: ' + error.message
        });
      });
    }

    function updateBillingTable(billings) {
      const tbody = document.getElementById('billingTableBody');
      tbody.innerHTML = '';
      
      billings.forEach(billing => {
        // Use formatted data from the backend or format locally
        const statusClass = billing.status === 'Paid' ? 'success' : (billing.status === 'Pending' ? 'danger' : 'warning');
        const sr = billing.sr || '-';
        const date = billing.date ? new Date(billing.date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '-';
        const vehicleNo = billing.vehicle_no ? billing.vehicle_no.toUpperCase() : '-';
        const customerName = billing.customer_name ? billing.customer_name : '-';
        const contactNumber = billing.contact_number || '-';
        const bags = billing.bags || 0;
        const deliveryPoint = billing.delivery_point || '-';
        const kmCovered = Number(billing.km_covered || 0).toFixed(2);
        const rent = Number(billing.rent || 0).toFixed(2);
        const advance = Number(billing.advance || 0).toFixed(2);
        const advanceDate = billing.advance_date ? new Date(billing.advance_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: '2-digit'}) : '-';
        const guarantor = billing.guarantor || '-';
        const dues = Number(billing.dues || 0).toFixed(2);
        const status = billing.status || 'Pending';
        
        const row = `
          <tr>
            <td>${sr}</td>
            <td>${date}</td>
            <td>${vehicleNo}</td>
            <td>${billing.gp_number || '-'}</td>
            <td>${customerName}</td>
            <td>${contactNumber}</td>
            <td>${bags}</td>
            <td>${deliveryPoint}</td>
            <td>${billing.vehicle_type || '-'}</td>
            <td>${kmCovered}</td>
            <td>${billing.rate || 0}</td>
            <td>${billing.freight || 0}</td>
            <td>${rent}</td>
            <td>${advance}</td>
            <td>${advanceDate}</td>
            <td>${guarantor}</td>
            <td>${dues}</td>
            <td><span class="badge bg-${statusClass}">${status}</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editBilling(${billing.id})">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteBilling(${billing.id})">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          </tr>
        `;
        tbody.innerHTML += row;
      });
    }

    function updateSummaryStats(data) {
      // Update the summary statistics in the stat cards
      const totalRentEl = document.getElementById('totalRent');
      const paidCountEl = document.getElementById('paidCount');
      const pendingCountEl = document.getElementById('pendingCount');
      const totalDuesEl = document.getElementById('totalDues');

      if (totalRentEl) totalRentEl.textContent = Number(data.totalRent).toFixed(2);
      if (paidCountEl) paidCountEl.textContent = data.paidCount;
      if (pendingCountEl) pendingCountEl.textContent = data.pendingCount;
      if (totalDuesEl) totalDuesEl.textContent = Number(data.totalDues).toFixed(2);
    }

    function exportBilling() {
      const month = document.getElementById('filter_month').value;
      if (!month) {
        Swal.fire({
          icon: 'warning',
          title: 'Select Month',
          text: 'Please select a billing month first'
        });
        return;
      }
      
      // Create a temporary link to trigger download
      const link = document.createElement('a');
      link.href = `/billing/export?month=${month}`;
      link.download = `billing_export_${month}.csv`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function showMonthlySummary() {
      const month = document.getElementById('filter_month').value;
      
      fetch(`/billing/monthly-summary?month=${month}`)
        .then(response => response.json())
        .then(data => {
          const summaryContent = document.getElementById('summaryContent');
          summaryContent.innerHTML = `
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title">Overall Summary</h6>
                    <table class="table table-sm">
                      <tr><td>Total Records:</td><td>${data.summary.total_records}</td></tr>
                      <tr><td>Total Rent:</td><td>${Number(data.summary.total_rent).toFixed(2)}</td></tr>
                      <tr><td>Total Advance:</td><td>${Number(data.summary.total_advance).toFixed(2)}</td></tr>
                      <tr><td>Total Dues:</td><td>${Number(data.summary.total_dues).toFixed(2)}</td></tr>
                      <tr><td>Total Bags:</td><td>${data.summary.total_bags}</td></tr>
                      <tr><td>Total KM:</td><td>${Number(data.summary.total_km).toFixed(2)}</td></tr>
                      <tr><td>Paid:</td><td>${data.summary.paid_count}</td></tr>
                      <tr><td>Pending:</td><td>${data.summary.pending_count}</td></tr>
                      <tr><td>Partial:</td><td>${data.summary.partial_count}</td></tr>
                      <tr><td>Pending Dues:</td><td>${Number(data.summary.pending_dues).toFixed(2)}</td></tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title">By Customer</h6>
                    <table class="table table-sm">
                      <thead><tr><th>Customer</th><th>Trips</th><th>Rent</th><th>Dues</th><th>Pending</th></tr></thead>
                      <tbody>
                        ${Object.entries(data.by_customer).map(([name, stats]) => `
                          <tr>
                            <td>${name}</td>
                            <td>${stats.total_trips}</td>
                            <td>${Number(stats.total_rent).toFixed(2)}</td>
                            <td>${Number(stats.total_dues).toFixed(2)}</td>
                            <td>${stats.pending_records}</td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title">By Vehicle</h6>
                    <table class="table table-sm">
                      <thead><tr><th>Vehicle</th><th>Trips</th><th>Rent</th><th>KM</th></tr></thead>
                      <tbody>
                        ${Object.entries(data.by_vehicle).map(([no, stats]) => `
                          <tr>
                            <td>${no}</td>
                            <td>${stats.total_trips}</td>
                            <td>${Number(stats.total_rent).toFixed(2)}</td>
                            <td>${Number(stats.total_km).toFixed(2)}</td>
                          </tr>
                        `).join('')}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          `;
          
          new bootstrap.Modal(document.getElementById('summaryModal')).show();
        });
    }

    function exportExcel() {
      const month = document.getElementById('filter_month').value;
      
      if (!month) {
        Swal.fire({
          icon: 'warning',
          title: 'Select Month',
          text: 'Please select a billing month first'
        });
        return;
      }

      Swal.fire({
        title: 'Enter Invoice Number',
        input: 'text',
        inputValue: '0000',
        showCancelButton: true,
        confirmButtonText: 'Export'
      }).then((result) => {
        if (result.isConfirmed) {
          const invoiceNumber = result.value;
          // Create a temporary link to trigger download
          const link = document.createElement('a');
          link.href = `/billing/export-excel?month=${month}&invoice_number=${invoiceNumber}`;
          link.download = `SIMG_DEPALPUR_${month}.xlsx`;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        }
      });
      
      // Create a temporary link to trigger download
      const link = document.createElement('a');
      link.href = `/billing/export-excel?month=${month}&invoice_number=${invoiceNumber}`;
      link.download = `SIMG_DEPALPUR_${month}.xlsx`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    }

    function showImportModal() {
      new bootstrap.Modal(document.getElementById('importModal')).show();
    }

    function processImport() {
      const fileInput = document.getElementById('import_file');
      const file = fileInput.files[0];
      
      if (!file) {
        Swal.fire({
          icon: 'warning',
          title: 'Select File',
          text: 'Please select a file to import'
        });
        return;
      }
      
      const formData = new FormData();
      formData.append('excel_file', file);
      
      fetch('/billing/import-excel', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Import Successful',
            text: data.message
          });
          if (data.errors && data.errors.length > 0) {
            Swal.fire({
              icon: 'warning',
              title: 'Import with Warnings',
              text: 'Some rows had errors:\n' + data.errors.join('\n')
            });
          }
          bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
          filterBillings();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Import Failed',
            text: data.message
          });
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Import Error',
          text: 'Error importing file: ' + error.message
        });
      });
    }

    function generateInvoice() {
      const month = document.getElementById('filter_month').value;
      
      if (!month) {
        Swal.fire({
          icon: 'warning',
          title: 'Select Month',
          text: 'Please select a billing month first'
        });
        return;
      }

      Swal.fire({
        title: 'Enter Invoice Number',
        input: 'text',
        inputValue: '0000',
        showCancelButton: true,
        confirmButtonText: 'Generate Invoice'
      }).then((result) => {
        if (result.isConfirmed) {
          const invoiceNumber = result.value;
          // Open invoice in new tab
          window.open(`/billing/generate-invoice?month=${month}&invoice_number=${invoiceNumber}`, '_blank');
        }
      });
    }
  </script>
@endsection