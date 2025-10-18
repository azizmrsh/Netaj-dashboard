<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>سند تسليم - {{ $deliveryDocument->purchase_order_no ?? 'غير محدد' }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    @page {
      size: A4;
      margin: 12mm;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #ffffff;
      color: #333;
      font-size: 14px;
      line-height: 1.6;
      direction: rtl;
      min-height: 297mm;
    }

    .container {
      max-width: 100%;
      margin: 0 auto;
      background-color: white;
      padding: 15px;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 18px;
      padding-bottom: 12px;
      border-bottom: 3px solid #d4af37;
    }

    .header-left {
      text-align: right;
      font-size: 12px;
      line-height: 1.5;
      width: 32%;
      direction: rtl;
    }

    .header-center {
      width: 36%;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .logo-container {
      width: 100px;
      height: 100px;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px solid #d4af37;
      border-radius: 50%;
    }

    .logo-container svg {
      max-width: 80px;
      max-height: 80px;
      width: auto;
      height: auto;
    }

    .header-center h2 {
      font-size: 16px;
      margin: 4px 0;
      font-weight: 600;
    }

    .header-right {
      text-align: left;
      font-size: 12px;
      line-height: 1.5;
      width: 32%;
      direction: ltr;
    }

    .date-line {
      display: flex;
      justify-content: space-between;
      margin: 18px 0;
      font-size: 14px;
      font-weight: 500;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }

    .date-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .date-item label {
      font-weight: 600;
      color: #333;
    }

    .date-item input {
      border: none;
      border-bottom: 1px solid #333;
      background: transparent;
      padding: 2px 5px;
      font-size: 14px;
      min-width: 80px;
      text-align: center;
    }

    .intro-text {
      text-align: center;
      margin: 20px 0;
      font-size: 16px;
      font-weight: 500;
      color: #333;
      line-height: 1.8;
    }

    .info-tables {
      display: flex;
      justify-content: space-between;
      margin: 20px 0;
      gap: 15px;
    }

    .info-table {
      flex: 1;
      border: 2px solid #333;
      border-radius: 8px;
    }

    .info-table-header {
      background-color: #f8f9fa;
      padding: 10px;
      text-align: center;
      font-weight: 600;
      font-size: 16px;
      border-bottom: 2px solid #333;
      color: #333;
    }

    .info-table-body {
      padding: 15px;
    }

    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      padding: 5px 0;
      border-bottom: 1px dotted #ccc;
    }

    .info-row:last-child {
      border-bottom: none;
    }

    .info-label {
      font-weight: 500;
      color: #555;
    }

    .info-value {
      font-weight: 600;
      color: #333;
    }

    .materials-table {
      width: 100%;
      border-collapse: collapse;
      margin: 25px 0;
      border: 2px solid #333;
      border-radius: 8px;
      overflow: hidden;
    }

    .materials-table th {
      background-color: #f8f9fa;
      padding: 12px 8px;
      text-align: center;
      font-weight: 600;
      font-size: 14px;
      border: 1px solid #333;
      color: #333;
    }

    .materials-table td {
      padding: 10px 8px;
      text-align: center;
      border: 1px solid #333;
      font-size: 13px;
      color: #333;
    }

    .materials-table .product-name {
      text-align: right;
      padding-right: 12px;
    }

    .materials-table tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .materials-table tbody tr:hover {
      background-color: #f0f8ff;
    }

    .signature-section {
      margin-top: 40px;
      display: flex;
      justify-content: space-between;
      gap: 30px;
    }

    .signature-box {
      flex: 1;
      border: 2px solid #333;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .signature-title {
      font-weight: 600;
      font-size: 16px;
      color: #333;
      margin-bottom: 15px;
    }

    .signature-line {
      border-top: 2px solid #333;
      margin-top: 60px;
      padding-top: 8px;
      font-size: 12px;
      color: #666;
    }

    .footer {
      margin-top: 30px;
      text-align: center;
      font-size: 12px;
      color: #666;
      border-top: 1px solid #ddd;
      padding-top: 15px;
    }

    @media print {
      body {
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      
      .container {
        padding: 0;
      }
      
      .signature-section {
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Header Section -->
    <div class="header">
      <div class="header-left">
        <div>شركة الجاري للتجارة والمقاولات</div>
        <div>Al-Gary Trading & Contracting Company</div>
        <div>ص.ب: 12345 الرياض 11564</div>
        <div>P.O. Box: 12345 Riyadh 11564</div>
        <div>هاتف: 011-1234567</div>
        <div>Tel: 011-1234567</div>
      </div>
      
      <div class="header-center">
        <div class="logo-container">
          <svg width="80" height="80" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <circle cx="50" cy="50" r="45" fill="#d4af37" stroke="#333" stroke-width="2"/>
            <text x="50" y="55" text-anchor="middle" font-family="Arial" font-size="20" font-weight="bold" fill="#333">LOGO</text>
          </svg>
        </div>
        <h2>سند تسليم</h2>
        <h2>DELIVERY RECEIPT</h2>
      </div>
      
      <div class="header-right">
        <div>Document No: {{ $deliveryDocument->id ?? '001' }}</div>
        <div>Date: {{ \Carbon\Carbon::parse($deliveryDocument->date_and_time)->format('Y/m/d') }}</div>
        <div>Time: {{ \Carbon\Carbon::parse($deliveryDocument->date_and_time)->format('H:i') }}</div>
      </div>
    </div>

    <!-- Date Line -->
    <div class="date-line">
      <div class="date-item">
        <label>التاريخ / Date:</label>
        <input type="text" value="{{ \Carbon\Carbon::parse($deliveryDocument->date_and_time)->format('Y/m/d') }}" readonly>
      </div>
      <div class="date-item">
        <label>الوقت / Time:</label>
        <input type="text" value="{{ \Carbon\Carbon::parse($deliveryDocument->date_and_time)->format('H:i') }}" readonly>
      </div>
      <div class="date-item">
        <label>رقم السند / Doc No:</label>
        <input type="text" value="{{ $deliveryDocument->id ?? '001' }}" readonly>
      </div>
    </div>

    <!-- Intro Text -->
    <div class="intro-text">
      نحن الموقعون أدناه نقر بأننا قد استلمنا المواد المذكورة أدناه بحالة جيدة وكاملة
      <br>
      We, the undersigned, acknowledge that we have received the materials mentioned below in good and complete condition
    </div>

    <!-- Information Tables -->
    <div class="info-tables">
      <!-- Customer Information -->
      <div class="info-table">
        <div class="info-table-header">معلومات العميل / Customer Information</div>
        <div class="info-table-body">
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
      </div>

      <!-- Order Information -->
      <div class="info-table">
        <div class="info-table-header">معلومات الطلب / Order Information</div>
        <div class="info-table-body">
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
    </div>

    <!-- Materials Table -->
    <table class="materials-table">
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

    <!-- Signature Section -->
    <div class="signature-section">
      <div class="signature-box">
        <div class="signature-title">مسؤول المستودع<br>Warehouse Officer</div>
        <div>الاسم / Name: {{ $deliveryDocument->warehouse_officer_name ?? '........................' }}</div>
        <div class="signature-line">التوقيع / Signature</div>
      </div>

      <div class="signature-box">
        <div class="signature-title">المستلم<br>Recipient</div>
        <div>الاسم / Name: {{ $deliveryDocument->recipient_name ?? '........................' }}</div>
        <div class="signature-line">التوقيع / Signature</div>
      </div>

      <div class="signature-box">
        <div class="signature-title">السائق<br>Driver</div>
        <div>الاسم / Name: {{ $deliveryDocument->transporter->driver_name ?? '........................' }}</div>
        <div class="signature-line">التوقيع / Signature</div>
      </div>

      @if($deliveryDocument->accountant_name)
      <div class="signature-box">
        <div class="signature-title">المحاسب<br>Accountant</div>
        <div>الاسم / Name: {{ $deliveryDocument->accountant_name }}</div>
        <div class="signature-line">التوقيع / Signature</div>
      </div>
      @endif
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>شركة الجاري للتجارة والمقاولات - سند تسليم مطبوع بتاريخ {{ now()->format('Y/m/d H:i') }}</p>
      <p>Al-Gary Trading & Contracting Company - Delivery Receipt Printed on {{ now()->format('Y/m/d H:i') }}</p>
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