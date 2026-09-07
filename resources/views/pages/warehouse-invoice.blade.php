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
    
    @media print {
        body {
            background: white;
            padding: 0;
        }
        
        .invoice-container {
            border: none;
            box-shadow: none;
        }
    }
</style>

<div class="invoice-container">
    <!-- Header -->
    <div class="header">
        <div class="company-name">SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY</div>
        <div class="company-details">
            Rizvi Chowk, Bypass Okara Road<br>
            Contact: 0300-6967450<br>
            NTN: 4252472-5
        </div>
    </div>
    
    <!-- Invoice Information -->
    <div class="invoice-info">
        <div>
            <div class="invoice-number">Invoice # {{ $invoiceNumber }}</div>
            <div class="billing-info">
                <strong>Billing Month:</strong> {{ $billingMonth }}<br>
                <strong>Warehouse:</strong> {{ $warehouseLocation }}
            </div>
        </div>
        <div style="text-align: right;">
            <div class="billing-info">
                <strong>Date:</strong> {{ date('d/m/Y') }}<br>
                <strong>Total Trips:</strong> {{ $trips->count() }}
            </div>
        </div>
    </div>
    
    <!-- Trips Table -->
    <table>
        <thead>
            <tr>
                <th>Sr</th>
                <th>Date</th>
                <th>Vehicle No</th>
                <th>GP#</th>
                <th>Delivery Point</th>
                <th>Vehicle Type</th>
                <th>KM</th>
                <th>Rate</th>
                <th>Freight</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trips as $index => $trip)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $trip->trip_date ? $trip->trip_date->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $trip->vehicle_number }}</td>
                <td>{{ $trip->gp_number }}</td>
                <td>{{ $trip->delivery_point }}</td>
                <td>{{ $trip->vehicle_type }}</td>
                <td>{{ number_format($trip->kilometers, 2) }}</td>
                <td>{{ number_format($trip->rate_per_km, 2) }}</td>
                <td>{{ number_format($trip->freight, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align: right;"><strong>TOTAL:</strong></td>
                <td><strong>{{ number_format($totalKm, 2) }}</strong></td>
                <td>-</td>
                <td><strong>{{ number_format($totalFreight, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Summary -->
    <div style="margin-bottom: 20px;">
        <strong>Summary:</strong>
        <ul style="margin-top: 10px;">
            <li>Total Trips: {{ $trips->count() }}</li>
            <li>Total Kilometers: {{ number_format($totalKm, 2) }}</li>
            <li>Total Freight: {{ number_format($totalFreight, 2) }}</li>
            <li>Average Rate per KM: {{ $totalKm > 0 ? number_format($totalFreight / $totalKm, 2) : '0.00' }}</li>
        </ul>
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