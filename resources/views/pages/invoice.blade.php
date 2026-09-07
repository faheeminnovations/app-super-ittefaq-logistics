<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $billingMonth }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 20px;
            background: #f5f5f5;
        }
        .invoice-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .company-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-address {
            margin-bottom: 5px;
        }
        .company-contact {
            margin-bottom: 5px;
        }
        .company-info {
            margin-bottom: 5px;
        }
        .invoice-info {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .invoice-info-row {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .sr-col { width: 50px; text-align: center; }
        .date-col { width: 100px; text-align: center; }
        .vehicle-col { width: 100px; text-align: center; }
        .gp-col { width: 80px; text-align: center; }
        .delivery-col { width: 300px; }
        .vhl-col { width: 60px; text-align: center; }
        .km-col { width: 80px; text-align: right; }
        .rate-col { width: 80px; text-align: right; }
        .frt-col { width: 100px; text-align: right; }
        .totals {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .print-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .print-btn:hover {
            background: #0056b3;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                padding: 20px;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="company-header">
            <div class="company-name">SUPER ITTEFAQ MINI GOODS TRANSPORT COMPANY</div>
            <div class="company-address">Rizvi Chowk, Bypass Okara Road Depalpur</div>
            <div class="company-contact">Contact Detail: 0300-6967450  zahidafzal5152@gmail.com</div>
            <div class="company-info">NTN : 4252472-5              Vendor Code : 0006781511</div>
        </div>

        <div class="invoice-info">
            <div class="invoice-info-row">
                <strong>Invoice No :</strong> {{ $invoiceNumber }}                              <strong>Date :</strong> {{ Carbon\Carbon::createFromFormat('Y-m', $billingMonth)->format('F-Y') }}
            </div>
            <div class="invoice-info-row">
                <strong>Billing Month :</strong> {{ Carbon\Carbon::createFromFormat('Y-m', $billingMonth)->format('F-Y') }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="sr-col">Sr</th>
                    <th class="date-col">Date</th>
                    <th class="vehicle-col">Vhl No</th>
                    <th class="gp-col">GP#</th>
                    <th class="delivery-col">Drop/Delivery Point</th>
                    <th class="vhl-col">Vhl</th>
                    <th class="km-col">Km</th>
                    <th class="rate-col">Rate</th>
                    <th class="frt-col">FRT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billings as $billing)
                <tr>
                    <td class="sr-col">{{ $billing->sr ?? '' }}</td>
                    <td class="date-col">{{ $billing->date ? $billing->date->format('d-M-y') : '' }}</td>
                    <td class="vehicle-col">{{ strtoupper($billing->vehicle_no ?? '') }}</td>
                    <td class="gp-col">{{ $billing->gp_number ?? '' }}</td>
                    <td class="delivery-col">{{ $billing->delivery_point ?? '' }}</td>
                    <td class="vhl-col">{{ strtoupper($billing->vehicle_type ?? '') }}</td>
                    <td class="km-col">{{ number_format($billing->km_covered ?? 0, 0) }}</td>
                    <td class="rate-col">{{ number_format($billing->rate ?? 0, 0) }}</td>
                    <td class="frt-col">{{ number_format($billing->freight ?? 0, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="total-row">
                <strong>Total Records:</strong>
                <span>{{ $billings->count() }}</span>
            </div>
            <div class="total-row">
                <strong>Total Kilometers:</strong>
                <span>{{ number_format($totalKm, 0) }} km</span>
            </div>
            <div class="total-row">
                <strong>Total Freight:</strong>
                <span>PKR {{ number_format($totalFreight, 0) }}</span>
            </div>
        </div>

        <button class="print-btn" onclick="window.print()">Print Invoice</button>
    </div>
</body>
</html>