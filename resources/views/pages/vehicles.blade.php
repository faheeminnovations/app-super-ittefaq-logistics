@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Fleet & People</div>
        <h1>Vehicles / Fleet</h1>
        <div class="sub">Registration, Fitness Certificate & Work Type</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportVehicles()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#vehicleModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Add Vehicle</button>
      </div>
    </div>
    
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <div class="row g-3 mb-3">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-truck-front"></i></div>
          <div class="label">Total Fleet</div>
          <div class="value">{{ $totalVehicles ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> All vehicles</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Available</div>
          <div class="value">{{ $availableVehicles ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Ready for dispatch</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#E8F4FD;color:var(--navy-600);"><i class="bi bi-truck"></i></div>
          <div class="label">On Trip</div>
          <div class="value">{{ $onTripVehicles ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Currently in use</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-tools"></i></div>
          <div class="label">Maintenance</div>
          <div class="value">{{ $maintenanceVehicles ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Under repair</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('available')">Available</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('on_trip')">On Trip</span> 
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('maintenance')">Maintenance</span> 
      <span class="badge-status badge-cancelled" style="cursor:pointer;" onclick="filterByStatus('out_of_service')">Out of Service</span>
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Fleet Register</div>
          <div class="panel-sub mb-0">All vehicles, trailers & compliance dates</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="vehiclesTable">
          <thead>
            <tr>
              <th>Reg No</th>
              <th>Type</th>
              <th>Make / Model</th>
              <th>Year</th>
              <th>VIN</th>
              <th>Fitness Certificate</th>
              <th>Work Type</th>
              <th>Route Permit</th>
              <th>Token Tax</th>
              <th>Insurance</th>
              <th>Fuel Capacity</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($vehicles)
              @foreach($vehicles as $vehicle)
            <tr>
              <td><span class='mono fw-semibold'>{{ $vehicle->reg_no }}</span></td>
              <td>{{ $vehicle->type }}</td>
              <td>{{ $vehicle->make_model }}</td>
              <td>{{ $vehicle->year }}</td>
              <td><span class='mono'>{{ $vehicle->vin ?? 'N/A' }}</span></td>
              <td>{{ $vehicle->fitness_certificate_expiry ? \Carbon\Carbon::parse($vehicle->fitness_certificate_expiry)->format('d M Y') : 'N/A' }}</td>
              <td>
                @switch($vehicle->work_type)
                  @case('company')
                    <span class="badge bg-primary">Company</span>
                    @break
                  @case('private')
                    <span class="badge bg-secondary">Private</span>
                    @break
                  @case('both')
                    <span class="badge bg-success">Both</span>
                    @break
                  @default
                    <span class="badge bg-secondary">N/A</span>
                @endswitch
              </td>
              <td>{{ $vehicle->route_permit ? \Carbon\Carbon::parse($vehicle->route_permit)->format('d M Y') : 'N/A' }}</td>
              <td>{{ $vehicle->token_tax ? \Carbon\Carbon::parse($vehicle->token_tax)->format('d M Y') : 'N/A' }}</td>
              <td>{{ $vehicle->insurance ? \Carbon\Carbon::parse($vehicle->insurance)->format('d M Y') : 'N/A' }}</td>
              <td>{{ $vehicle->fuel_capacity ? $vehicle->fuel_capacity . 'L' : 'N/A' }}</td>
              <td>
                @switch($vehicle->status)
                  @case('available')
                    <span class="badge-status badge-delivered">Available</span>
                    @break
                  @case('on_trip')
                    <span class="badge-status badge-transit">On Trip</span>
                    @break
                  @case('maintenance')
                    <span class="badge-status badge-pending">Maintenance</span>
                    @break
                  @case('out_of_service')
                    <span class="badge-status badge-cancelled">Out of Service</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editVehicle({{ $vehicle->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewVehicle({{ $vehicle->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger" onclick="deleteVehicle({{ $vehicle->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="13" class="text-center">No vehicles found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Vehicle Modal -->
  <div class="modal fade" id="vehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="vehicleModalLabel">New Vehicle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="vehicleForm" action="{{ route('vehicles.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="vehicle_id" id="vehicle_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="reg_no" class="form-label">Registration Number</label>
                <input type="text" class="form-control" name="reg_no" id="reg_no" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="type" class="form-label">Vehicle Type</label>
                <select class="form-select" name="type" id="type" required>
                  <option value="">Select Type</option>
                  <option value="Rigid Truck">Rigid Truck</option>
                  <option value="Artic">Artic</option>
                  <option value="Van">Van</option>
                  <option value="Trailer">Trailer</option>
                  <option value="Pickup">Pickup</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="make_model" class="form-label">Make / Model</label>
                <input type="text" class="form-control" name="make_model" id="make_model">
              </div>
              <div class="col-md-6 mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" name="year" id="year" min="1900" max="{{ date('Y') + 1 }}">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="vin" class="form-label">VIN (Optional)</label>
                <input type="text" class="form-control" name="vin" id="vin">
              </div>
              <div class="col-md-6 mb-3">
                <label for="fuel_capacity" class="form-label">Fuel Capacity (L) (Optional)</label>
                <input type="number" step="0.01" class="form-control" name="fuel_capacity" id="fuel_capacity">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="fitness_certificate_expiry" class="form-label">Fitness Certificate Expiry Date</label>
                <input type="date" class="form-control" name="fitness_certificate_expiry" id="fitness_certificate_expiry">
              </div>
              <div class="col-md-6 mb-3">
                <label for="work_type" class="form-label">Work Type</label>
                <select class="form-select" name="work_type" id="work_type">
                  <option value="">Select Work Type</option>
                  <option value="company">Company</option>
                  <option value="private">Private</option>
                  <option value="both">Both</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Vehicle Documents</label>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="route_permit" class="form-label">Route Permit Expiry</label>
                  <input type="date" class="form-control" name="route_permit" id="route_permit">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="token_tax" class="form-label">Token Tax Expiry</label>
                  <input type="date" class="form-control" name="token_tax" id="token_tax">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="insurance" class="form-label">Insurance Expiry</label>
                  <input type="date" class="form-control" name="insurance" id="insurance">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status">
                  <option value="">Select Status</option>
                  <option value="available">Available</option>
                  <option value="on_trip">On Trip</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="out_of_service">Out of Service</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Vehicle</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Vehicle Modal -->
  <div class="modal fade" id="viewVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Vehicle Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewVehicleContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#vehiclesTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 12 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search vehicles..."
        }
    });

    // Form submission
    $('#vehicleForm').on('submit', function(e) {
        e.preventDefault();

        var vehicleId = $('#vehicle_id').val();
        var url = vehicleId ? '/vehicles/' + vehicleId : '{{ route("vehicles.store") }}';

        // Set _method to PUT for updates
        if (vehicleId) {
            $('#_method').val('PUT');
        } else {
            $('#_method').val('POST');
        }

        // Serialize form after setting _method
        var formData = $(this).serialize();

        // Always use POST for AJAX, Laravel will read _method field
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#vehicleModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving vehicle';
                if (errors) {
                    errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        errorMessage = 'Error saving vehicle: ' + xhr.status;
                    }
                }
                alert(errorMessage);
            }
        });
    });
});

function resetForm() {
    $('#vehicleForm')[0].reset();
    $('#vehicle_id').val('');
    $('#_method').val('POST');
    $('#vehicleModalLabel').text('New Vehicle');
}

function editVehicle(id) {
    $.ajax({
        url: '/vehicles/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(vehicle) {
            fillVehicleForm(vehicle);
            $('#vehicleModal').modal('show');
        },
        error: function() {
            alert('Error loading vehicle data');
        }
    });
}

function fillVehicleForm(vehicle) {
    $('#vehicle_id').val(vehicle.id);
    $('#reg_no').val(vehicle.reg_no);
    $('#type').val(vehicle.type);
    $('#make_model').val(vehicle.make_model);
    $('#year').val(vehicle.year);
    $('#vin').val(vehicle.vin);
    $('#fuel_capacity').val(vehicle.fuel_capacity);
    $('#status').val(vehicle.status);
    $('#notes').val(vehicle.notes);
    
    // Format dates for input type="date"
    if (vehicle.fitness_certificate_expiry) {
        var fitnessDate = new Date(vehicle.fitness_certificate_expiry);
        var formattedFitness = fitnessDate.toISOString().split('T')[0];
        $('#fitness_certificate_expiry').val(formattedFitness);
    }
    
    if (vehicle.route_permit) {
        var routePermitDate = new Date(vehicle.route_permit);
        var formattedRoutePermit = routePermitDate.toISOString().split('T')[0];
        $('#route_permit').val(formattedRoutePermit);
    }
    
    if (vehicle.token_tax) {
        var tokenTaxDate = new Date(vehicle.token_tax);
        var formattedTokenTax = tokenTaxDate.toISOString().split('T')[0];
        $('#token_tax').val(formattedTokenTax);
    }
    
    if (vehicle.insurance) {
        var insuranceDate = new Date(vehicle.insurance);
        var formattedInsurance = insuranceDate.toISOString().split('T')[0];
        $('#insurance').val(formattedInsurance);
    }
    
    if (vehicle.work_type) {
        $('#work_type').val(vehicle.work_type);
    }
    
    $('#vehicleModalLabel').text('Edit Vehicle');
}

function viewVehicle(id) {
    $.ajax({
        url: '/vehicles/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var vehicle = data.vehicle || data;
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Vehicle Information</h6>
                        <p><strong>Registration:</strong> ${vehicle.reg_no}</p>
                        <p><strong>Type:</strong> ${vehicle.type}</p>
                        <p><strong>Make/Model:</strong> ${vehicle.make_model}</p>
                        <p><strong>Year:</strong> ${vehicle.year}</p>
                        <p><strong>VIN:</strong> ${vehicle.vin || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Compliance Details</h6>
                        <p><strong>Fitness Certificate:</strong> ${vehicle.fitness_certificate_expiry ? new Date(vehicle.fitness_certificate_expiry).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Work Type:</strong> ${vehicle.work_type || 'N/A'}</p>
                        <p><strong>Route Permit:</strong> ${vehicle.route_permit ? new Date(vehicle.route_permit).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Token Tax:</strong> ${vehicle.token_tax ? new Date(vehicle.token_tax).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Insurance:</strong> ${vehicle.insurance ? new Date(vehicle.insurance).toLocaleDateString() : 'N/A'}</p>
                        <p><strong>Fuel Capacity:</strong> ${vehicle.fuel_capacity ? vehicle.fuel_capacity + 'L' : 'N/A'}</p>
                        <p><strong>Status:</strong> ${vehicle.status}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p>${vehicle.notes || 'No notes provided'}</p>
                    </div>
                </div>
            `;
            $('#viewVehicleContent').html(html);
            $('#viewVehicleModal').modal('show');
        },
        error: function() {
            alert('Error loading vehicle details');
        }
    });
}

function deleteVehicle(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this vehicle!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/vehicles/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Vehicle has been deleted.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error deleting vehicle';
                    Swal.fire(
                        'Error!',
                        message,
                        'error'
                    );
                }
            });
        }
    });
}

function filterByStatus(status) {
    var table = $('#vehiclesTable').DataTable();
    if (status === 'all') {
        table.column(11).search('').draw();
    } else {
        table.column(11).search(status).draw();
    }
}

function exportVehicles() {
    fetch('/vehicles/export', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'text/csv'
        },
    })
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'vehicles_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting vehicles data');
    });
}
</script>
@endpush

