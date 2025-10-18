<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند التسليم - {{ $deliveryDocument->purchase_order_no ?? 'غير محدد' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
            direction: rtl;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }

        .logo-section {
            width: 120px;
            height: 120px;
            border: 2px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }

        .logo-section svg {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
        }

        .company-info {
            flex: 1;
            text-align: center;
            margin: 0 20px;
        }

        .company-name-ar {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }

        .company-name-en {
            font-size: 18px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            direction: ltr;
        }

        .document-title {
            font-size: 20px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .document-title-en {
            font-size: 16px;
            font-weight: bold;
            color: #000;
            direction: ltr;
        }

        .document-number {
            width: 120px;
            height: 120px;
            border: 2px solid #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .document-number .number-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .document-number .number-value {
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }

        .document-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-section {
            border: 2px solid #000;
            padding: 15px;
        }

        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-align: center;
            text-decoration: underline;
        }

        .info-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #000;
        }

        .info-value {
            color: #000;
            flex: 1;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            border: 2px solid #000;
        }

        .products-table th,
        .products-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
        }

        .products-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .products-table .product-name {
            text-align: right;
            padding-right: 10px;
        }

        .summary-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .signature-box {
            border: 2px solid #000;
            padding: 15px;
            text-align: center;
            min-height: 100px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin: 20px 0 10px 0;
            height: 1px;
        }

        .totals {
            border: 2px solid #000;
            padding: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }

        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 2px solid #000;
            margin-top: 10px;
        }

        .notes {
            margin-top: 20px;
            border: 2px solid #000;
            padding: 15px;
        }

        .notes h4 {
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }

        @media print {
            body {
                font-size: 11px;
            }
            
            .container {
                max-width: none;
                margin: 0;
                padding: 15px;
            }
            
            .header {
                margin-bottom: 20px;
                padding-bottom: 15px;
            }
            
            .document-info {
                margin-bottom: 20px;
            }
            
            .products-table {
                margin: 20px 0;
            }
            
            .summary-section {
                margin-top: 20px;
            }
            
            .notes {
                margin-top: 20px;
            }
            
            .footer {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <svg width="100" height="100" viewBox="0 0 375 374.999991" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <clipPath id="clip1">
                            <path d="M 0 0 L 375 0 L 375 374.999991 L 0 374.999991 Z M 0 0"/>
                        </clipPath>
                    </defs>
                    <g clip-path="url(#clip1)">
                        <path fill="#1f5f99" d="M 187.5 0 C 291.421875 0 375 83.578125 375 187.5 C 375 291.421875 291.421875 375 187.5 375 C 83.578125 375 0 291.421875 0 187.5 C 0 83.578125 83.578125 0 187.5 0 Z M 187.5 0"/>
                        <path fill="#ffffff" d="M 187.5 37.5 C 270.703125 37.5 337.5 104.296875 337.5 187.5 C 337.5 270.703125 270.703125 337.5 187.5 337.5 C 104.296875 337.5 37.5 270.703125 37.5 187.5 C 37.5 104.296875 104.296875 37.5 187.5 37.5 Z M 187.5 37.5"/>
                        <path fill="#1f5f99" d="M 187.5 75 C 249.035156 75 300 125.964844 300 187.5 C 300 249.035156 249.035156 300 187.5 300 C 125.964844 300 75 249.035156 75 187.5 C 75 125.964844 125.964844 75 187.5 75 Z M 187.5 75"/>
                        <path fill="#ffffff" d="M 150 150 L 225 150 L 225 225 L 150 225 Z M 150 150"/>
                        <path fill="#1f5f99" d="M 162.5 162.5 L 212.5 162.5 L 212.5 212.5 L 162.5 212.5 Z M 162.5 162.5"/>
                    </g>
                </svg>
            </div>
            
            <div class="company-info">
                <div class="company-name-ar">شركة نتاج للمواد البترولية</div>
                <div class="company-name-en">NETAJ PETROLEUM MATERIALS COMPANY</div>
                <div class="document-title">سند التسليم</div>
                <div class="document-title-en">DELIVERY RECEIPT</div>
            </div>
            
            <div class="document-number">
                <div class="number-label">رقم السند / No.</div>
                <div class="number-value">{{ $deliveryDocument->id ?? '001' }}</div>
                <div style="font-size: 10px; margin-top: 5px;">
                    {{ \Carbon\Carbon::parse($deliveryDocument->date_and_time)->format('Y/m/d') }}
                </div>
            </div>
        </div>

        <!-- Document Information -->
        <div class="document-info">
            <!-- Customer Information -->
            <div class="info-section">
                <h3>معلومات العميل / Customer Information</h3>
                <div class="info-row">
                    <span class="info-label">اسم العميل / Name:</span>
                    <span class="info-value">{{ $deliveryDocument->customer->name ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الهاتف / Phone:</span>
                    <span class="info-value">{{ $deliveryDocument->customer->phone ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">البريد الإلكتروني / Email:</span>
                    <span class="info-value">{{ $deliveryDocument->customer->email ?? 'غير محدد' }}</span>
                </div>
                @if($deliveryDocument->customer->address)
                <div class="info-row">
                    <span class="info-label">العنوان / Address:</span>
                    <span class="info-value">{{ $deliveryDocument->customer->address }}</span>
                </div>
                @endif
            </div>

            <!-- Order Information -->
            <div class="info-section">
                <h3>معلومات الطلب / Order Information</h3>
                <div class="info-row">
                    <span class="info-label">رقم أمر الشراء / P.O. No:</span>
                    <span class="info-value">{{ $deliveryDocument->purchase_order_no ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">اسم المشروع / Project:</span>
                    <span class="info-value">{{ $deliveryDocument->project_name_and_location ?? 'غير محدد' }}</span>
                </div>
                @if($deliveryDocument->purchasing_officer_name)
                <div class="info-row">
                    <span class="info-label">مسؤول المشتريات / Purchasing Officer:</span>
                    <span class="info-value">{{ $deliveryDocument->purchasing_officer_name }}</span>
                </div>
                @endif
                @if($deliveryDocument->warehouse_officer_name)
                <div class="info-row">
                    <span class="info-label">مسؤول المستودع / Warehouse Officer:</span>
                    <span class="info-value">{{ $deliveryDocument->warehouse_officer_name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Transporter Information -->
        <div class="document-info">
            <div class="info-section">
                <h3>معلومات الناقل / Transporter Information</h3>
                <div class="info-row">
                    <span class="info-label">اسم الناقل / Transporter:</span>
                    <span class="info-value">{{ $deliveryDocument->transporter->name ?? 'غير محدد' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">رقم الهاتف / Phone:</span>
                    <span class="info-value">{{ $deliveryDocument->transporter->phone ?? 'غير محدد' }}</span>
                </div>
                @if($deliveryDocument->transporter->driver_name)
                <div class="info-row">
                    <span class="info-label">اسم السائق / Driver:</span>
                    <span class="info-value">{{ $deliveryDocument->transporter->driver_name }}</span>
                </div>
                @endif
                @if($deliveryDocument->transporter->car_no)
                <div class="info-row">
                    <span class="info-label">رقم السيارة / Vehicle No:</span>
                    <span class="info-value">{{ $deliveryDocument->transporter->car_no }}</span>
                </div>
                @endif
            </div>

            <div class="info-section">
                <h3>تفاصيل إضافية / Additional Details</h3>
                <div class="info-row">
                    <span class="info-label">التاريخ والوقت / Date & Time:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($deliveryDocument->date_and_time)->format('Y/m/d H:i') }}</span>
                </div>
                @if($deliveryDocument->recipient_name)
                <div class="info-row">
                    <span class="info-label">اسم المستلم / Recipient:</span>
                    <span class="info-value">{{ $deliveryDocument->recipient_name }}</span>
                </div>
                @endif
                @if($deliveryDocument->accountant_name)
                <div class="info-row">
                    <span class="info-label">اسم المحاسب / Accountant:</span>
                    <span class="info-value">{{ $deliveryDocument->accountant_name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 5%;">م / No.</th>
                    <th style="width: 30%;">اسم المنتج / Product Name</th>
                    <th style="width: 10%;">الكمية / Qty</th>
                    <th style="width: 10%;">الوحدة / Unit</th>
                    <th style="width: 12%;">سعر الوحدة / Unit Price</th>
                    <th style="width: 8%;">الضريبة / Tax %</th>
                    <th style="width: 15%;">المجموع / Total</th>
                    <th style="width: 10%;">ملاحظات / Notes</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $totalTax = 0;
                @endphp
                @foreach($deliveryDocument->deliveryDocumentProducts as $index => $item)
                    @php
                        $itemTotal = $item->quantity * ($item->unit_price ?? 0);
                        $itemTax = $itemTotal * (($item->tax_rate ?? 0) / 100);
                        $itemTotalWithTax = $itemTotal + $itemTax;
                        $subtotal += $itemTotal;
                        $totalTax += $itemTax;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="product-name">{{ $item->product->name ?? 'غير محدد' }}</td>
                        <td>{{ number_format($item->quantity, 3) }}</td>
                        <td>{{ $item->product->unit ?? 'غير محدد' }}</td>
                        <td>{{ $item->unit_price ? number_format($item->unit_price, 2) : '0.00' }}</td>
                        <td>{{ $item->tax_rate ? $item->tax_rate . '%' : '0%' }}</td>
                        <td>{{ number_format($itemTotalWithTax, 2) }}</td>
                        <td></td>
                    </tr>
                @endforeach
                @for($i = count($deliveryDocument->deliveryDocumentProducts); $i < 8; $i++)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="signature-title">مسؤول المستودع<br>Warehouse Officer</div>
                    <div>الاسم / Name: {{ $deliveryDocument->warehouse_officer_name ?? '........................' }}</div>
                    <div class="signature-line"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 10px;">التوقيع / Signature</div>
                </div>

                <div class="signature-box">
                    <div class="signature-title">المستلم<br>Recipient</div>
                    <div>الاسم / Name: {{ $deliveryDocument->recipient_name ?? '........................' }}</div>
                    <div class="signature-line"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 10px;">التوقيع / Signature</div>
                </div>

                @if($deliveryDocument->accountant_name)
                <div class="signature-box">
                    <div class="signature-title">المحاسب<br>Accountant</div>
                    <div>الاسم / Name: {{ $deliveryDocument->accountant_name }}</div>
                    <div class="signature-line"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 10px;">التوقيع / Signature</div>
                </div>
                @endif

                <div class="signature-box">
                    <div class="signature-title">السائق<br>Driver</div>
                    <div>الاسم / Name: {{ $deliveryDocument->transporter->driver_name ?? '........................' }}</div>
                    <div class="signature-line"></div>
                    <div style="text-align: center; margin-top: 5px; font-size: 10px;">التوقيع / Signature</div>
                </div>
            </div>

            <!-- Totals -->
            <div class="totals">
                <h3 style="text-align: center; margin-bottom: 15px; text-decoration: underline;">الإجماليات / Totals</h3>
                <div class="total-row">
                    <span>المجموع الفرعي / Subtotal:</span>
                    <span>{{ number_format($subtotal, 2) }} ريال</span>
                </div>
                <div class="total-row">
                    <span>إجمالي الضريبة / Total Tax:</span>
                    <span>{{ number_format($totalTax, 2) }} ريال</span>
                </div>
                <div class="total-row final">
                    <span>المجموع الإجمالي / Grand Total:</span>
                    <span>{{ number_format($subtotal + $totalTax, 2) }} ريال</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($deliveryDocument->note)
        <div class="notes">
            <h4>ملاحظات / Notes:</h4>
            <p>{{ $deliveryDocument->note }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>شركة نتاج للمواد البترولية - سند تسليم مطبوع بتاريخ {{ now()->format('Y/m/d H:i') }}</p>
            <p>NETAJ PETROLEUM MATERIALS COMPANY - Delivery Receipt Printed on {{ now()->format('Y/m/d H:i') }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>