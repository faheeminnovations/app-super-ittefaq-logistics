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

    .income-section {
        background: #f8fff8;
        border: 2px solid #28a745;
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
    }

    .expense-section {
        background: #fff8f8;
        border: 2px solid #dc3545;
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
    }

    .net-section {
        background: #f8f8ff;
        border: 2px solid #007bff;
        padding: 15px;
        margin: 20px 0;
        border-radius: 5px;
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

        /* Make table scrollable on mobile */
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

        .km-input, .rate-input {
            width: 60px !important;
            padding: 3px !important;
            font-size: 11px !important;
        }

        .income-section, .expense-section, .net-section {
            padding: 10px;
            margin: 15px 0;
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

        h4 {
            font-size: 16px;
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

        .km-input, .rate-input {
            width: 50px !important;
            padding: 2px !important;
            font-size: 10px !important;
        }

        .income-section, .expense-section, .net-section {
            padding: 8px;
            margin: 10px 0;
        }

        h4 {
            font-size: 14px;
        }
    }

    @media print {
        body {
            background: white;
            padding: 0;
            margin: 0;
        }

        .invoice-container {
            border: none;
            box-shadow: none;
            padding: 10px;
            margin: 0;
            max-width: 100%;
        }

        .header {
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 16px;
        }

        .company-details {
            font-size: 11px;
        }

        .invoice-info {
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 13px;
            display: block !important;
            visibility: visible !important;
        }

        .billing-info {
            font-size: 11px;
        }

        table {
            font-size: 10px;
            width: 100%;
        }

        th {
            padding: 5px 3px;
            font-size: 10px;
        }

        td {
            padding: 4px 2px;
            font-size: 10px;
        }

        .km-input, .rate-input {
            border: none !important;
            background: transparent !important;
            padding: 2px !important;
            font-size: 10px !important;
            width: 50px !important;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        #invoiceNumber, #vendorCode {
            border: none !important;
            background: transparent !important;
            padding: 2px !important;
            font-size: 10px !important;
        }

        .income-section, .expense-section, .net-section {
            padding: 8px;
            margin: 8px 0;
        }

        h4 {
            font-size: 12px;
            margin-bottom: 8px;
        }

        .signature-section {
            margin-top: 20px;
        }

        .signature-line {
            margin-top: 40px;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            font-size: 9px;
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
            <div class="invoice-number" id="invoiceLabel" style="font-size: 20px; font-weight: bold; color: #0066cc;">{{ $companySettings->warehouse_invoice_label ?? 'Warehouse Invoice' }} - {{ $invoiceNumber }}</div>
            @if(isset($warehouseLocation) && $warehouseLocation && $warehouseLocation !== '')
            <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">{{ $warehouseLocation }}</div>
            @endif
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
                <td class="freight-cell" data-freight="{{ number_format($trip->freight, 2) }}">{{ number_format($trip->freight, 2) }}</td>
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
    
    <!-- Income Section -->
    <div class="income-section">
        <h4 style="color: #28a745; margin-bottom: 15px;">Income Details</h4>
        <div class="table-responsive">
            <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Freight (from trips)</td>
                    <td>{{ number_format($totalFreight, 2) }}</td>
                </tr>
                @if($totalIncome > $totalFreight)
                <tr>
                    <td>Interest Income (2% on credit expenses)</td>
                    <td>{{ number_format($totalIncome - $totalFreight, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td><strong>Total Income</strong></td>
                    <td><strong>{{ number_format($totalIncome ?? $totalFreight, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
    
    <!-- Expense Section -->
    <div class="expense-section">
        <h4 style="color: #dc3545; margin-bottom: 15px;">Expense Details</h4>
        <div class="table-responsive">
            <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($expenseEntries) && count($expenseEntries) > 0)
                    @foreach($expenseEntries as $entry)
                    <tr>
                        <td>{{ ucfirst($entry['category']) }}</td>
                        <td>{{ $entry['description'] ?? '-' }}</td>
                        <td>{{ ucfirst($entry['payment_type'] ?? '-') }}</td>
                        <td>{{ number_format($entry['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">No expense entries recorded</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td colspan="3"><strong>Total Expense</strong></td>
                    <td><strong>{{ number_format($totalExpense ?? 0, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
    
    <!-- Net Amount Section -->
    <div class="net-section">
        <h4 style="color: #007bff; margin-bottom: 15px;">Net Amount / Profit</h4>
        <div class="table-responsive">
            <table>
            <tbody>
                <tr>
                    <td><strong>Total Income</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($totalIncome ?? $totalFreight, 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Total Expense</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($totalExpense ?? 0, 2) }}</strong></td>
                </tr>
                <tr class="total-row" style="background: #e7f3ff;">
                    <td><strong>Net Amount (Profit/Loss)</strong></td>
                    <td style="text-align: right;"><strong>{{ number_format($netAmount ?? ($totalFreight - ($totalExpense ?? 0)), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
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
        <p>This is a computer-generated invoice with Income & Expense details.</p>
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