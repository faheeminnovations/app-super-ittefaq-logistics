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
                <th>Freight Bill No</th>
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
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $trip->trip_date ? $trip->trip_date->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ $trip->vehicle_number }}</td>
                <td>{{ $trip->freight_bill_no ?? '-' }}</td>
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
                <td colspan="7" style="text-align: right;"><strong>TOTAL:</strong></td>
                <td><strong>{{ number_format($totalKm, 2) }}</strong></td>
                <td>-</td>
                <td><strong>{{ number_format($totalFreight, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Income Section -->
    <div class="income-section">
        <h4 style="color: #28a745; margin-bottom: 15px;">Income Details</h4>
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
    
    <!-- Expense Section -->
    <div class="expense-section">
        <h4 style="color: #dc3545; margin-bottom: 15px;">Expense Details</h4>
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
    
    <!-- Net Amount Section -->
    <div class="net-section">
        <h4 style="color: #007bff; margin-bottom: 15px;">Net Amount / Profit</h4>
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
    
    <!-- Summary -->
    <div style="margin-bottom: 20px;">
        <strong>Summary:</strong>
        <ul style="margin-top: 10px;">
            <li>Total Trips: {{ $trips->count() }}</li>
            <li>Total Kilometers: {{ number_format($totalKm, 2) }}</li>
            <li>Total Freight: {{ number_format($totalFreight, 2) }}</li>
            <li>Total Income: {{ number_format($totalIncome ?? $totalFreight, 2) }}</li>
            <li>Total Expense: {{ number_format($totalExpense ?? 0, 2) }}</li>
            <li>Net Amount: {{ number_format($netAmount ?? ($totalFreight - ($totalExpense ?? 0)), 2) }}</li>
            <li>Average Rate per KM: {{ $totalKm > 0 ? number_format($totalFreight / $totalKm, 2) : '0.00' }}</li>
            <li>Warehouse Location: {{ $warehouseLocation }}</li>
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
        <p>This is a computer-generated invoice with Income & Expense details.</p>
        <p>&copy; {{ date('Y') }} Super Ittefaq Mini Goods / Super Ittefaq Logistics</p>
    </div>
</div>