<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilty Receipt - {{ $bilty->bilty_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&family=Roboto:wght@400;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', 'Noto Nastaliq Urdu', sans-serif;
            background: #f0f0f0;
            padding: 20px;
        }
        
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 2px solid #333;
            position: relative;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 72px;
            font-weight: bold;
            color: rgba(0, 100, 200, 0.1);
            white-space: nowrap;
            pointer-events: none;
            z-index: 0;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 5px;
        }
        
        .company-name-urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 28px;
            font-weight: bold;
            color: #0066cc;
            margin-bottom: 10px;
        }
        
        .proprietor {
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .registration {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .contact-info {
            font-size: 11px;
            line-height: 1.4;
        }
        
        .form-section {
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            background: #0066cc;
            color: white;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .section-title-urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 18px;
            font-weight: bold;
            background: #28a745;
            color: white;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            text-align: center;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 10px;
            gap: 15px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group.small {
            flex: 0.5;
        }
        
        .label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #333;
        }
        
        .label-urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
            color: #333;
        }
        
        .value {
            font-size: 14px;
            padding: 6px 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            min-height: 30px;
        }
        
        .value-urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 16px;
            padding: 6px 8px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            min-height: 35px;
            text-align: right;
        }
        
        .goods-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .goods-table th {
            background: #28a745;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #1e7e34;
        }
        
        .goods-table th.urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 14px;
        }
        
        .goods-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 13px;
        }
        
        .goods-table td.urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 15px;
            text-align: right;
        }
        
        .financial-summary {
            margin-top: 20px;
            padding: 15px;
            background: #f0f8ff;
            border: 1px solid #0066cc;
            border-radius: 4px;
        }
        
        .financial-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .financial-row.total {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #0066cc;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 11px;
            color: #666;
        }
        
        .footer-urdu {
            font-family: 'Noto Nastaliq Urdu', serif;
            font-size: 14px;
            margin-top: 10px;
            color: #333;
            text-align: center;
            background: #fff3cd;
            padding: 10px;
            border: 1px solid #ffc107;
            border-radius: 4px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #ffc107;
            color: #333;
        }
        
        .status-in_transit {
            background: #17a2b8;
            color: white;
        }
        
        .status-delivered {
            background: #28a745;
            color: white;
        }
        
        .status-cancelled {
            background: #dc3545;
            color: white;
        }
        
        .print-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 12px;
            color: #666;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt {
                border: none;
                box-shadow: none;
            }
            
            .print-info {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-info">
        <p>Press Ctrl+P or Cmd+P to print this receipt</p>
    </div>
    
    <div class="receipt">
        <div class="watermark">SUPER ITTEFAQ LOGISTICS</div>
        
        <!-- Header Section -->
        <div class="header">
            <div class="company-name-urdu">سپر اتفاق منی گڈز</div>
            <div class="company-name">SUPER ITTEFAQ MINI GOODS</div>
            <div class="company-name" style="font-size: 18px; margin-top: 5px;">SUPER ITTEFAQ LOGISTICS</div>
            <div class="proprietor">
                <strong>Address:</strong> Razvi Chowk, Dipalpur Okara Road, Depalpur, Pakistan
            </div>
            <div class="registration">
                <strong>رجسٹر نمبر:</strong> {{ $bilty->registration_number ?? '4252472-5' }}
            </div>
            <div class="contact-info">
                <div><strong>Contact:</strong> +92 300 6967450</div>
            </div>
        </div>
        
        <!-- Basic Information -->
        <div class="form-section">
            <div class="section-title">بِلٹی معلومات (Bilty Information)</div>
            <div class="form-row">
                <div class="form-group small">
                    <div class="label">بِلٹی نمبر</div>
                    <div class="value">{{ $bilty->bilty_number }}</div>
                </div>
                <div class="form-group small">
                    <div class="label">تاریخ (Date)</div>
                    <div class="value">{{ $bilty->bilty_date->format('d/m/Y') }}</div>
                </div>
                <div class="form-group small">
                    <div class="label">حالت (Status)</div>
                    <div class="value">
                        <span class="status-badge status-{{ $bilty->status }}">
                            {{ ucfirst(str_replace('_', ' ', $bilty->status)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Location Information -->
        <div class="form-section">
            <div class="section-title">مقام (Location)</div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label-urdu">از (From)</div>
                    <div class="value-urdu">{{ $bilty->from_location }}</div>
                </div>
                <div class="form-group">
                    <div class="label-urdu">تا (To)</div>
                    <div class="value-urdu">{{ $bilty->to_location }}</div>
                </div>
            </div>
        </div>
        
        <!-- Sender and Receiver Information -->
        <div class="form-section">
            <div class="section-title">بھیجنے اور لینے والے (Sender & Receiver)</div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label-urdu">بھیجنے والے کا نام (Sender Name)</div>
                    <div class="value-urdu">{{ $bilty->sender_name }}</div>
                </div>
                <div class="form-group small">
                    <div class="label">فون (Phone)</div>
                    <div class="value">{{ $bilty->sender_phone ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <div class="label-urdu">لینے والے کا نام (Receiver Name)</div>
                    <div class="value-urdu">{{ $bilty->receiver_name }}</div>
                </div>
                <div class="form-group small">
                    <div class="label">فون (Phone)</div>
                    <div class="value">{{ $bilty->receiver_phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Vehicle and Driver Information -->
        <div class="form-section">
            <div class="section-title">گاڑی اور ڈرائیور (Vehicle & Driver)</div>
            <div class="form-row">
                <div class="form-group small">
                    <div class="label-urdu">گاڑی نمبر (Vehicle No.)</div>
                    <div class="value-urdu">{{ $bilty->vehicle_number ?? 'N/A' }}</div>
                </div>
                <div class="form-group">
                    <div class="label-urdu">نام ڈرائیور (Driver Name)</div>
                    <div class="value-urdu">{{ $bilty->driver_name ?? 'N/A' }}</div>
                </div>
                <div class="form-group small">
                    <div class="label-urdu">آئی ڈی کارڈ نمبر (ID Card No.)</div>
                    <div class="value-urdu">{{ $bilty->card_number ?? 'N/A' }}</div>
                </div>
                <div class="form-group small">
                    <div class="label">Driver Phone</div>
                    <div class="value">{{ $bilty->driver_phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Goods Information -->
        <div class="form-section">
            <div class="section-title-urdu">مال کی تفصیلات (Goods Details)</div>
            <table class="goods-table">
                <thead>
                    <tr>
                        <th class="urdu">تعداد</th>
                        <th class="urdu">تفصیل مال</th>
                        <th class="urdu">کرایہ روپے</th>
                        <th class="urdu">پیشگی</th>
                        <th class="urdu">بقایا کرایہ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="urdu">{{ $bilty->quantity ?? '-' }} {{ $bilty->quantity_unit ?? '' }}</td>
                        <td class="urdu">{{ $bilty->goods_description }}</td>
                        <td>{{ $bilty->formatted_total_amount }}</td>
                        <td>{{ $bilty->formatted_advance_amount }}</td>
                        <td>{{ $bilty->formatted_remaining_balance }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Financial Summary -->
        <div class="financial-summary">
            <div class="section-title">مالیاتی خلاصہ (Financial Summary)</div>
            <div class="financial-row">
                <span><strong>کل کرایہ (Total Rent):</strong></span>
                <span>{{ $bilty->formatted_total_amount }}</span>
            </div>
            <div class="financial-row">
                <span><strong>پیشگی (Advance):</strong></span>
                <span>{{ $bilty->formatted_advance_amount }}</span>
            </div>
            @if($bilty->rent_amount)
            <div class="financial-row">
                <span><strong>کرایہ (Rent Amount):</strong></span>
                <span>{{ $bilty->formatted_rent_amount }}</span>
            </div>
            @endif
            @if($bilty->scale)
            <div class="financial-row">
                <span><strong>Scale:</strong></span>
                <span>{{ $bilty->formatted_scale }}</span>
            </div>
            @endif
            <div class="financial-row total">
                <span><strong>بقایا کرایہ (Remaining Balance):</strong></span>
                <span>{{ $bilty->formatted_remaining_balance }}</span>
            </div>
        </div>
        
        <!-- Additional Information -->
        @if($bilty->notes)
        <div class="form-section">
            <div class="section-title">اضافی معلومات (Additional Information)</div>
            <div class="value">{{ $bilty->notes }}</div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-urdu">
                نوٹ: مال مویشی کی رسید راہداری یا قریبی تھانہ کی تصدیق بیو پاری کی ذمہ داری ہے۔
            </div>
            <div style="margin-top: 15px;">
                <strong>نوٹ:</strong> The receipt/permit for livestock/goods or the verification from the nearest police station is the responsibility of the trader.
            </div>
            <div style="margin-top: 10px;">
                &copy; {{ date('Y') }} Super Ittefaq Mini Goods / Super Ittefaq Logistics | Computer Generated Receipt
            </div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>