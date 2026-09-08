<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Log #{{ $tripLog->sr }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .company-info {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .info-group {
            flex: 1;
        }
        .info-label {
            font-weight: bold;
        }
        .info-value {
            margin-left: 5px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th,
        .details-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .details-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .category-section {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 15px;
        }
        .category-section h3 {
            margin-top: 0;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .field-row {
            display: flex;
            margin-bottom: 8px;
        }
        .field-label {
            font-weight: bold;
            width: 150px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="header">
            <h1>SUPER ITTEFAQ MINI GOODS TRANSPORT CO.</h1>
            <p>RIZVI CHOWK DEPALPUR</p>
        </div>

        <div class="company-info">
            <div class="info-row">
                <div class="info-group">
                    <span class="info-label">Trip Log #:</span>
                    <span class="info-value">{{ $tripLog->sr }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $tripLog->date->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-group">
                    <span class="info-label">Vehicle No:</span>
                    <span class="info-value">{{ $tripLog->vehicle_no }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $tripLog->business_category }}</span>
                </div>
            </div>
        </div>

        <table class="details-table">
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>GP Number</td>
                <td>{{ $tripLog->gp_number ?? '-' }}</td>
            </tr>
            <tr>
                <td>Delivery Point</td>
                <td>{{ $tripLog->delivery_point }}</td>
            </tr>
            <tr>
                <td>Vehicle Category</td>
                <td>{{ $tripLog->vehicle_category }}</td>
            </tr>
            <tr>
                <td>Kilometers</td>
                <td>{{ number_format($tripLog->km, 2) }}</td>
            </tr>
            <tr>
                <td>Rate</td>
                <td>{{ number_format($tripLog->rate, 2) }}</td>
            </tr>
            <tr>
                <td>FRT (Freight)</td>
                <td><strong>{{ number_format($tripLog->frt, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Fuel</td>
                <td>{{ $tripLog->fuel ?? '-' }}</td>
            </tr>
            <tr>
                <td>Driver Name</td>
                <td>{{ $tripLog->driver_name ?? '-' }}</td>
            </tr>
        </table>

        <div class="category-section">
            <h3>Category Specific Details</h3>
            
            @if($tripLog->business_category == 'Buyer Supply Chain')
                <div class="field-row">
                    <div class="field-label">Load ID:</div>
                    <div>{{ $tripLog->load_id ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Freight Bill NO:</div>
                    <div>{{ $tripLog->freight_bill_no ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Cluster:</div>
                    <div>{{ $tripLog->cluster ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Customer Name:</div>
                    <div>{{ $tripLog->customer_name ?? '-' }}</div>
                </div>
            @endif

            @if(in_array($tripLog->business_category, ['Buyer Breading', 'Buyer Marketing Development', 'Buyer S.P.R', 'Syngenta', 'Syngenta Breading']))
                <div class="field-row">
                    <div class="field-label">Loading Point:</div>
                    <div>{{ $tripLog->loading_point ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Unloading Point:</div>
                    <div>{{ $tripLog->unloading_point ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Customer Name:</div>
                    <div>{{ $tripLog->customer_name ?? '-' }}</div>
                </div>
            @endif

            @if($tripLog->business_category == 'Open Market Work')
                <div class="field-row">
                    <div class="field-label">Driver Name:</div>
                    <div>{{ $tripLog->driver_name ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Customer Name:</div>
                    <div>{{ $tripLog->customer_name ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Loading Point:</div>
                    <div>{{ $tripLog->loading_point ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Unloading Point:</div>
                    <div>{{ $tripLog->unloading_point ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Rent Paid:</div>
                    <div>{{ number_format($tripLog->rent_paid, 2) }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Expenses:</div>
                    <div>{{ number_format($tripLog->expenses, 2) }}</div>
                </div>
            @endif

            @if($tripLog->business_category == 'Buyer Seed Supply')
                <div class="field-row">
                    <div class="field-label">Driver Name:</div>
                    <div>{{ $tripLog->driver_name ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Phone Number:</div>
                    <div>{{ $tripLog->phone_number ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Quantity:</div>
                    <div>{{ $tripLog->quantity }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Guarantor:</div>
                    <div>{{ $tripLog->guarantor ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Rent Paid:</div>
                    <div>{{ number_format($tripLog->rent_paid, 2) }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Payment Details:</div>
                    <div>{{ $tripLog->payment_details ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Receipt Details:</div>
                    <div>{{ $tripLog->receiving_details ?? '-' }}</div>
                </div>
                <div class="field-row">
                    <div class="field-label">Status:</div>
                    <div>{{ ucfirst($tripLog->trip_status ?? '-') }}</div>
                </div>
            @endif
        </div>

        <div class="info-row" style="margin-top: 20px;">
            <div class="info-group">
                <span class="info-label">Billing Month:</span>
                <span class="info-value">{{ $tripLog->billing_month }}</span>
            </div>
            <div class="info-group">
                <span class="info-label">Billing Year:</span>
                <span class="info-value">{{ $tripLog->billing_year }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Generated on: {{ now()->format('d/m/Y H:i') }}</p>
            <p>SUPER ITTEFAQ MINI GOODS TRANSPORT CO. - RIZVI CHOWK DEPALPUR</p>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Print
            </button>
            <a href="{{ route('trip-logs.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
</body>
</html>