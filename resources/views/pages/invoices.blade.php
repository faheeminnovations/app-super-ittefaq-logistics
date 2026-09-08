@extends('layouts.app')

@section('content')
  <div class="page-wrap">
    <div class="page-head">
      <div>
        <div class="eyebrow">Accounts</div>
        <h1>Invoices</h1>
        <div class="sub">Customer invoices, VAT & payment status</div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-navy" onclick="exportInvoices()"><i class="bi bi-download me-1"></i> Export</button>
        <button class="btn btn-navy" data-bs-toggle="modal" data-bs-target="#invoiceModal" onclick="resetForm()"><i class="bi bi-plus-lg me-1"></i> Create Invoice</button>
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
          <div class="icon-badge" style="background:#EAF0FB;color:var(--navy-800);"><i class="bi bi-receipt"></i></div>
          <div class="label">Total Invoiced</div>
          <div class="value">{{ \App\Helpers\CurrencyHelper::formatCurrency($totalAmount ?? 0) }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> {{ $totalInvoices ?? 0 }} invoices</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#EAF7EF;color:var(--success);"><i class="bi bi-check-circle"></i></div>
          <div class="label">Sent</div>
          <div class="value">{{ $sentInvoices ?? 0 }}</div>
          <div class="delta up"><i class="bi bi-arrow-up-short"></i> Sent to client</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FFF3E0;color:var(--warn);"><i class="bi bi-hourglass-split"></i></div>
          <div class="label">Draft</div>
          <div class="value">{{ $draftInvoices ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Pending verification</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="icon-badge" style="background:#FBE9E7;color:var(--danger);"><i class="bi bi-exclamation-triangle"></i></div>
          <div class="label">Paid</div>
          <div class="value">{{ $paidInvoices ?? 0 }}</div>
          <div class="delta down"><i class="bi bi-arrow-down-short"></i> Payment received</div>
        </div>
      </div>
    </div>
    
    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <span class="badge-status badge-pending" style="cursor:pointer;" onclick="filterByStatus('all')">All</span> 
      <span class="badge-status badge-transit" style="cursor:pointer;" onclick="filterByStatus('draft')">Draft</span> 
      <span class="badge-status badge-delivered" style="cursor:pointer;" onclick="filterByStatus('sent')">Sent</span> 
      <span class="badge-status badge-success" style="cursor:pointer;" onclick="filterByStatus('paid')">Paid</span> 
      <span class="badge-status badge-delayed" style="cursor:pointer;" onclick="filterByStatus('cancelled')">Cancelled</span> 
    </div>
    
    <div class="panel">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="panel-title mb-0">Invoice Register</div>
          <div class="panel-sub mb-0">All customer invoices & VAT breakdown</div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-striped" id="invoicesTable">
          <thead>
            <tr>
              <th>Invoice #</th>
              <th>Billing Month</th>
              <th>Client</th>
              <th>Subtotal</th>
              <th>Tax Amount</th>
              <th>Total Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @isset($invoices)
              @foreach($invoices as $invoice)
            <tr>
              <td><span class='mono fw-semibold'>{{ $invoice->invoice_number }}</span></td>
              <td>{{ $invoice->billing_month }}</td>
              <td>{{ $invoice->client_name ?? 'N/A' }}</td>
              <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($invoice->subtotal) }}</td>
              <td>{{ \App\Helpers\CurrencyHelper::formatCurrency($invoice->tax_amount) }}</td>
              <td><strong>{{ \App\Helpers\CurrencyHelper::formatCurrency($invoice->total_amount) }}</strong></td>
              <td>
                @switch($invoice->status)
                  @case('draft')
                    <span class="badge-status badge-transit">Draft</span>
                    @break
                  @case('sent')
                    <span class="badge-status badge-delivered">Sent</span>
                    @break
                  @case('paid')
                    <span class="badge-status badge-success">Paid</span>
                    @break
                  @case('cancelled')
                    <span class="badge-status badge-pending">Cancelled</span>
                    @break
                  @default
                    <span class="badge-status badge-pending">{{ ucfirst(str_replace('_', ' ', $invoice->status)) }}</span>
                @endswitch
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <button type="button" class="btn btn-outline-primary" onclick="editInvoice({{ $invoice->id }})" title="Edit">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="viewInvoice({{ $invoice->id }})" title="View">
                    <i class="bi bi-eye"></i>
                  </button>
                  @if($invoice->status === 'draft')
                  <button type="button" class="btn btn-outline-success" onclick="calculateTotals({{ $invoice->id }})" title="Calculate Totals">
                    <i class="bi bi-calculator"></i>
                  </button>
                  @endif
                  <button type="button" class="btn btn-outline-danger" onclick="deleteInvoice({{ $invoice->id }})" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
            @else
            <tr><td colspan="8" class="text-center">No invoices found</td></tr>
            @endisset
          </tbody>
        </table>
      </div>
    </div>
    <div class="text-center text-muted mt-4" style="font-size:11.5px;">
      &copy; 2026 Super Ittefaq Logistics &middot; Transport Management System
    </div>
  </div>

  <!-- Create/Edit Invoice Modal -->
  <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="invoiceModalLabel">Create Invoice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <input type="hidden" name="invoice_id" id="invoice_id">
            <input type="hidden" name="_method" id="_method" value="POST">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="invoice_number" class="form-label">Invoice Number</label>
                <input type="text" class="form-control" name="invoice_number" id="invoice_number" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="invoice_date" class="form-label">Invoice Date</label>
                <input type="date" class="form-control" name="invoice_date" id="invoice_date" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="billing_month" class="form-label">Billing Month</label>
                <input type="text" class="form-control" name="billing_month" id="billing_month" placeholder="e.g., May-2026" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="billing_year" class="form-label">Year</label>
                <input type="number" class="form-control" name="billing_year" id="billing_year" required>
              </div>
              <div class="col-md-4 mb-3">
                <label for="billing_month_number" class="form-label">Month Number</label>
                <input type="number" class="form-control" name="billing_month_number" id="billing_month_number" min="1" max="12" required>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="client_name" class="form-label">Client Name</label>
                <input type="text" class="form-control" name="client_name" id="client_name" value="Bayer Pakistan (Pvt.) Ltd.">
              </div>
              <div class="col-md-6 mb-3">
                <label for="warehouse" class="form-label">Warehouse</label>
                <input type="text" class="form-control" name="warehouse" id="warehouse" value="Depalpur">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="gl_number" class="form-label">GL Number</label>
                <input type="text" class="form-control" name="gl_number" id="gl_number" value="4022265">
              </div>
              <div class="col-md-6 mb-3">
                <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                <input type="number" step="0.01" class="form-control" name="tax_rate" id="tax_rate" value="16">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" name="status" id="status" required>
                  <option value="draft">Draft</option>
                  <option value="sent">Sent</option>
                  <option value="paid">Paid</option>
                  <option value="cancelled">Cancelled</option>
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
            <button type="submit" class="btn btn-primary">Save Invoice</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- View Invoice Modal -->
  <div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Invoice Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="viewInvoiceContent">
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
    $('#invoicesTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: 7 } // Actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search invoices..."
        }
    });

    // Form submission
    $('#invoiceForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var invoiceId = $('#invoice_id').val();
        var url = invoiceId ? '/invoices/' + invoiceId : '{{ route("invoices.store") }}';
        var method = 'POST';
        
        // Set _method to PUT for updates
        if (invoiceId) {
            $('#_method').val('PUT');
        } else {
            $('#_method').val('POST');
        }
        
        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#invoiceModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                var errorMessage = 'Error saving invoice';
                if (errors) {
                    errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });
                }
                alert(errorMessage);
            }
        });
    });
});

function resetForm() {
    $('#invoiceForm')[0].reset();
    $('#invoice_id').val('');
    $('#_method').val('POST');
    $('#invoiceModalLabel').text('Create Invoice');
}

function editInvoice(id) {
    $.ajax({
        url: '/invoices/' + id + '/edit',
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(invoice) {
            fillInvoiceForm(invoice);
            $('#invoiceModal').modal('show');
        },
        error: function() {
            alert('Error loading invoice data');
        }
    });
}

function fillInvoiceForm(invoice) {
    $('#invoice_id').val(invoice.id);
    $('#invoice_number').val(invoice.invoice_number);
    $('#invoice_date').val(invoice.invoice_date);
    $('#billing_month').val(invoice.billing_month);
    $('#billing_year').val(invoice.billing_year);
    $('#billing_month_number').val(invoice.billing_month_number);
    $('#client_name').val(invoice.client_name);
    $('#warehouse').val(invoice.warehouse);
    $('#gl_number').val(invoice.gl_number);
    $('#tax_rate').val(invoice.tax_rate);
    $('#status').val(invoice.status);
    $('#notes').val(invoice.notes);
    
    $('#invoiceModalLabel').text('Edit Invoice');
}

function viewInvoice(id) {
    $.ajax({
        url: '/invoices/' + id,
        type: 'GET',
        headers: {
            'Accept': 'application/json'
        },
        success: function(data) {
            var invoice = data.invoice || data;
            var tripLogs = invoice.trip_logs || [];
            var tripLogsHtml = '';
            
            if (tripLogs.length > 0) {
                tripLogsHtml = `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Trip Details (${tripLogs.length} trips)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sr</th>
                                            <th>Date</th>
                                            <th>Vehicle</th>
                                            <th>Delivery Point</th>
                                            <th>KM</th>
                                            <th>Rate</th>
                                            <th>FRT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${tripLogs.map(function(trip) {
                                            return `
                                                <tr>
                                                    <td>${trip.sr}</td>
                                                    <td>${trip.date}</td>
                                                    <td>${trip.vehicle_no}</td>
                                                    <td>${trip.delivery_point.substring(0, 30)}...</td>
                                                    <td>${trip.km}</td>
                                                    <td>${trip.rate}</td>
                                                    <td>${trip.frt}</td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            var html = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Invoice Information</h6>
                        <p><strong>Invoice Number:</strong> ${invoice.invoice_number}</p>
                        <p><strong>Billing Month:</strong> ${invoice.billing_month}</p>
                        <p><strong>Invoice Date:</strong> ${invoice.invoice_date}</p>
                        <p><strong>Client:</strong> ${invoice.client_name || 'N/A'}</p>
                        <p><strong>Warehouse:</strong> ${invoice.warehouse || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Financial Details</h6>
                        <p><strong>Subtotal:</strong> PKR ${parseFloat(invoice.subtotal).toFixed(2)}</p>
                        <p><strong>Tax Rate:</strong> ${invoice.tax_rate}%</p>
                        <p><strong>Tax Amount:</strong> PKR ${parseFloat(invoice.tax_amount).toFixed(2)}</p>
                        <p><strong>Total Amount:</strong> <strong>PKR ${parseFloat(invoice.total_amount).toFixed(2)}</strong></p>
                        <p><strong>Status:</strong> ${invoice.status}</p>
                    </div>
                </div>
                ${tripLogsHtml}
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Notes</h6>
                        <p>${invoice.notes || 'No notes provided'}</p>
                    </div>
                </div>
            `;
            $('#viewInvoiceContent').html(html);
            $('#viewInvoiceModal').modal('show');
        },
        error: function() {
            alert('Error loading invoice details');
        }
    });
}

function deleteInvoice(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this invoice!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '/invoices/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        'Deleted!',
                        'Invoice has been deleted.',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error deleting invoice';
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
    var table = $('#invoicesTable').DataTable();
    if (status === 'all') {
        table.column(6).search('').draw();
    } else {
        table.column(6).search(status).draw();
    }
}

function calculateTotals(id) {
    if (confirm('Calculate invoice totals from trip logs?')) {
        $.ajax({
            url: '/invoices/' + id + '/calculate',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            },
            error: function() {
                alert('Error calculating invoice totals');
            }
        });
    }
}

function exportInvoices() {
    fetch('/invoices/export', {
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
        a.download = 'invoices_export_' + new Date().toISOString().split('T')[0] + '.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    })
    .catch(error => {
        console.error('Export error:', error);
        alert('Error exporting invoices data');
    });
}
</script>
@endpush

