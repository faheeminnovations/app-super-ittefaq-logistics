<style>
    .invoice-container {
        max-width: 900px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .header {
        text-align: center;
        border-bottom: 2px solid #333;
        padding-bottom: 20px;
        margin-bottom: 20px;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
        color: #0066cc;
        margin-bottom: 5px;
    }

    .company-details {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .invoice-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .invoice-number {
        font-size: 18px;
        font-weight: bold;
    }

    .billing-info {
        font-size: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th {
        background: #0066cc;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #0055aa;
    }

    td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    .total-row {
        background: #f0f8ff;
        font-weight: bold;
    }

    .footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
        text-align: center;
        font-size: 12px;
        color: #666;
    }

    .signature-section {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }

    .signature-box {
        width: 200px;
        text-align: center;
    }

    .signature-line {
        border-top: 1px solid #333;
        margin-top: 60px;
        padding-top: 5px;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .invoice-container {
            padding: 15px;
            margin: 10px;
            max-width: none;
        }

        .company-name {
            font-size: 18px;
        }

        .company-details {
            font-size: 12px;
        }

        .invoice-info {
            flex-direction: column;
            gap: 15px;
        }

        .invoice-number {
            font-size: 16px;
        }

        .billing-info {
            font-size: 12px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            font-size: 12px;
            min-width: 800px;
        }

        th, td {
            padding: 8px 6px;
            font-size: 11px;
        }

        .km-input, .rate-input, .required-km-input {
            width: 60px !important;
            padding: 3px !important;
            font-size: 11px !important;
        }

        .signature-section {
            flex-direction: column;
            gap: 30px;
        }

        .signature-box {
            width: 100%;
        }

        .signature-line {
            margin-top: 40px;
        }

        ul {
            font-size: 12px;
            padding-left: 20px;
        }
    }

    @media (max-width: 480px) {
        .invoice-container {
            padding: 10px;
            margin: 5px;
        }

        .company-name {
            font-size: 16px;
        }

        .company-details {
            font-size: 11px;
        }

        th, td {
            padding: 6px 4px;
            font-size: 10px;
        }

        .km-input, .rate-input, .required-km-input {
            width: 50px !important;
            padding: 2px !important;
            font-size: 10px !important;
        }
    }

    @media print {
        body {
            background: white;
            padding: 0;
        }

        .invoice-container {
            border: none;
            box-shadow: none;
        }

        .km-input, .rate-input {
            border: none !important;
            background: transparent !important;
            padding: 5px !important;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        #invoiceNumber, #summaryText, #vendorCode {
            border: none !important;
            background: transparent !important;
            padding: 5px !important;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        #summaryText {
            resize: none;
        }
    }
</style>

<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $companySettings->company_name ?? 'SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY' }}</div>
        <div class="company-details">
            {{ $companySettings->address ?? 'Rizvi Chowk, Bypass Okara Road Depalpur' }} | Contact: {{ $companySettings->contact_phone ?? '0300-6967450' }} | Email: {{ $companySettings->contact_email ?? 'zahidafzal5152@gmail.com' }}<br>
            NTN: {{ $companySettings->ntn ?? '4252472-5' }} | VENDOR CODE: <input type="text" id="vendorCode" value="{{ $companySettings->vendor_code ?? '0006781511' }}" style="border: 1px solid #ddd; padding: 5px; width: 120px;">
        </div>
    </div>

    <!-- Invoice Information -->
    <div class="invoice-info">
        <div>
            <div class="invoice-number">{{ $companySettings->warehouse_invoice_label ?? 'Warehouse Invoice' }} # <input type="text" id="invoiceNumber" value="{{ $invoiceNumber }}" style="border: 1px solid #ddd; padding: 5px; width: 200px;"></div>
        </div>
        <div style="text-align: right;">
            <div class="billing-info">
                <strong>Date:</strong> {{ date('d/m/Y') }}<br>
                <strong>Total Trips:</strong> {{ $trips->count() }}
            </div>
        </div>
    </div>
    
    <!-- Trips Table -->
    <div class="table-responsive">
        <table>
        <thead>
            <tr>
                <th>Sr</th>
                <th>Date</th>
                <th>Vehicle No</th>
                <th>GP#</th>
                <th style="width: 25%;">Delivery Point</th>
                <th>Vehicle Type</th>
                <th>KM</th>
                <th>Rate</th>
                <th>Freight</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trips as $index => $trip)
            <tr data-trip-id="{{ $trip->id }}">
                <td>{{ $index + 1 }}</td>
                <td>{{ $trip->trip_date ? $trip->trip_date->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $trip->vehicle_number }}</td>
                <td>{{ $trip->gp_number }}</td>
                <td>{{ $trip->delivery_point }}</td>
                <td>{{ $trip->vehicle_type }}</td>
                <td><input type="number" step="0.01" value="{{ number_format($trip->kilometers, 2) }}" class="km-input" data-trip-id="{{ $trip->id }}" style="width: 80px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;" oninput="updateFreight(this)"></td>
                <td><input type="number" step="0.01" value="{{ number_format($trip->rate_per_km, 2) }}" class="rate-input" data-trip-id="{{ $trip->id }}" style="width: 80px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;" oninput="updateFreight(this)"></td>
                <td class="freight-cell">{{ number_format($trip->freight, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>TOTAL:</strong></td>
                <td>-</td>
                <td><strong id="totalKm">{{ number_format($totalKm, 2) }}</strong></td>
                <td>-</td>
                <td><strong id="totalFreight">{{ number_format($totalFreight, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    </div>
    
    <!-- Summary -->
    <div style="margin-bottom: 20px;">
        <strong>Summary:</strong>
        <ul style="margin-top: 10px;">
            <li>Total Trips: {{ $trips->count() }}</li>
            <li>Total Kilometers: {{ number_format($totalKm, 2) }}</li>
            <li>Total Freight: {{ number_format($totalFreight, 2) }}</li>
            <li>Average Rate per KM: {{ $totalKm > 0 ? number_format($totalFreight / $totalKm, 2) : '0.00' }}</li>
        </ul>
        <div style="margin-top: 15px;">
            <strong>Summary Text:</strong>
            <textarea id="summaryText" rows="3" style="width: 100%; margin-top: 10px; border: 1px solid #ddd; padding: 10px;" placeholder="Enter summary text here..."></textarea>
        </div>
    </div>
    
    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Prepared By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Approved By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Receiver Signature</div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated invoice.</p>
        <p>&copy; {{ date('Y') }} Super Ittefaq Mini Goods / Super Ittefaq Logistics</p>
    </div>
</div>

<script>
function updateInvoiceRequiredKm(input) {
    const tripId = input.getAttribute('data-trip-id');
    const value = input.value;
    
    fetch(`/warehouse-trips/${tripId}/update-required-km`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            required_km: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                input.style.backgroundColor = '';
            }, 1000);
        } else {
            alert('Failed to update Required KM: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update Required KM');
    });
}

function updateFreight(input) {
    const row = input.closest('tr');
    const kmInput = row.querySelector('.km-input');
    const rateInput = row.querySelector('.rate-input');
    const freightCell = row.querySelector('.freight-cell');
    
    const km = parseFloat(kmInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 0;
    const freight = km * rate;
    
    freightCell.textContent = freight.toFixed(2);
    updateTotals();
}

function updateTotals() {
    let totalKm = 0;
    let totalFreight = 0;
    
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const kmInput = row.querySelector('.km-input');
        const rateInput = row.querySelector('.rate-input');
        
        if (kmInput && rateInput) {
            const km = parseFloat(kmInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            totalKm += km;
            totalFreight += km * rate;
        }
    });
    
    document.getElementById('totalKm').textContent = totalKm.toFixed(2);
    document.getElementById('totalFreight').textContent = totalFreight.toFixed(2);
}
</script>