<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>فاتورة مشتريات - {{ $purchaseInvoice->receiptDocument->supplier->name ?? 'غير محدد' }} - {{ \Carbon\Carbon::parse($purchaseInvoice->date_and_time)->format('Y-m-d') }}</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Cairo', 'Segoe UI', Arial, sans-serif;
    font-size: 11px;
    color: #000;
    background: #fff;
    direction: rtl;
    line-height: 1.3;
}
.container {
    width: 210mm;
    min-height: 297mm;
    padding: 10mm;
    margin: 0 auto;
    position: relative;
    background: #fff;
}
@media print {
    @page { size: A4; margin: 0; }
    body { margin: 0; background: #fff; }
    .container { padding: 10mm; }
    .no-print { display: none !important; }
}
@media screen {
    body { background: #f0f0f0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    .container { box-shadow: 0 0 15px rgba(0,0,0,0.1); }
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 3px solid #d4af37;
}

.header-left {
    text-align: right;
    font-size: 11px;
    line-height: 1.4;
    width: 32%;
    direction: rtl;
}

.header-center {
    width: 36%;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.logo-container {
    width: 180px;
    height: 110px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 0;
    background-color: transparent;
    overflow: visible;
    padding: 0;
}

.header-center h2 {
    font-size: 14px;
    margin: 3px 0;
    font-weight: 600;
    line-height: 1.2;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
}

.header-right {
    text-align: left;
    font-size: 11px;
    line-height: 1.4;
    width: 32%;
    direction: ltr;
}

.header-content {
    display: flex;
    flex-direction: column;
    height: 100%;
    justify-content: space-between;
}

.company-title {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 5px;
    color: #2c3e50;
}

.company-details {
    font-size: 11px;
    line-height: 1.4;
}

.header-left, .header-right {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 8px;
    font-size: 10px;
}
th, td {
    border: 1px solid #d4af37;
    padding: 5px 3px;
    text-align: center;
    vertical-align: middle;
    font-weight: normal;
}
th {
    background-color: #fff;
    font-weight: 600;
    font-size: 9px;
}
.bilingual {
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.bilingual .ar { direction: rtl; font-weight: 600; font-size: 9px; }
.bilingual .en { direction: ltr; font-weight: 500; font-size: 8px; color: #333; }

/* Supplier Info Table - 4 columns in row 1 */
.supplier-table-row1 th:nth-child(1) { width: 15%; }
.supplier-table-row1 th:nth-child(2) { width: 15%; }
.supplier-table-row1 th:nth-child(3) { width: 55%; }
.supplier-table-row1 th:nth-child(4) { width: 15%; }

/* 7 columns in row 2 */
.supplier-table th:nth-child(1) { width: 12%; }
.supplier-table th:nth-child(2) { width: 12%; }
.supplier-table th:nth-child(3) { width: 14%; }
.supplier-table th:nth-child(4) { width: 14%; }
.supplier-table th:nth-child(5) { width: 12%; }
.supplier-table th:nth-child(6) { width: 20%; }
.supplier-table th:nth-child(7) { width: 12%; }

/* Invoice Details - 7 columns */
.invoice-details-table th { width: 14.28%; }

/* Items Table - 7 columns */
.items-table th:nth-child(1) { width: 8%; }
.items-table th:nth-child(2) { width: 32%; }
.items-table th:nth-child(3) { width: 10%; }
.items-table th:nth-child(4) { width: 12%; }
.items-table th:nth-child(5) { width: 14%; }
.items-table th:nth-child(6) { width: 12%; }
.items-table th:nth-child(7) { width: 12%; }

.items-table td:nth-child(2) {
    text-align: right;
    padding-right: 5px;
}

/* Totals Table */
.totals-table {
    width: 100%;
}
.totals-table td {
    padding: 6px 8px;
}
.totals-table .label {
    background: #fff;
    text-align: right;
    font-weight: 600;
    font-size: 10px;
}
.totals-table .value {
    text-align: center;
    font-weight: 600;
}
.totals-table .total {
    background: #d4af37 !important;
    color: #000;
    font-weight: 700;
    font-size: 11px;
}
.totals-table .text-value {
    text-align: right;
    font-weight: 500;
    font-size: 9px;
    padding-right: 10px;
}

/* Signature */
.signature-table {
    margin-top: 15px;
    width: 100%;
}
.signature-table td {
    height: 50px;
    vertical-align: bottom;
    text-align: center;
    font-weight: 600;
    border: 1px solid #d4af37;
    font-size: 10px;
}

.no-print {
    position: fixed;
    top: 20px;
    left: 20px;
    padding: 10px 20px;
    background: #d4af37;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    z-index: 1000;
}
.no-print:hover {
    background: #b8942a;
}
</style>
</head>
<body>
<button class="no-print" onclick="window.print()">طباعة PDF</button>

<div class="container">
<!-- Header -->
<div class="header">
<div class="header-left">
<div class="header-content">
<div class="company-title">شركة نتاج المتطورة التجارية</div>
<div class="company-details">
رقم المبنى - الشارع: ٣٠٣٠ - شارع قيصر الكاتب<br>
الحي - المدينة: ٦٢٠٨ مداين الفهد - جدة<br>
الولاية - البلد: مكة المكرمة - المملكة العربية السعودية<br>
الرمز البريدي: ٢٢٣٤٧<br>
البريد الإلكتروني: info@advanced-netaj.com<br>
رقم السجل التجاري: ٤٠٣٠٥٧٩٠٩٠<br>
رقم ضريبة القيمة المضافة: ٣١٢٥٤٤٥٢٩٠٠٠٠٣
</div>
</div>
</div>

<div class="header-center">
<div class="logo-container">
<img src="{{ asset('images/logo.svg') }}" alt="Company Logo" style="width: 100%; height: 100%; object-fit: contain;">
</div>
<h2>Netaj Almotatwrah Commercial Company</h2>
<h2>Purchases Invoice</h2>
<h2>فاتورة مشتريات</h2>
</div>

<div class="header-right">
<div class="header-content">
<div class="company-title">Netaj Almotatwrah Commercial Company</div>
<div class="company-details">
Building No. - Street: 3030 - Qaiser Al Kateb Street<br>
District - City: 6208 Madain Al Fahd - Jeddah<br>
State - Country: Makka - Kingdom of Saudi Arabia<br>
Postal code: 22347<br>
E-mail: info@advanced-netaj.com<br>
Commercial Registration Number: 4030579090<br>
VAT Number: 3125445290003
</div>
</div>
</div>
</div>

<!-- Supplier Info Table - Row 1 -->
<table class="supplier-table-row1">
<tr>
<th><div class="bilingual"><div class="ar">رقم ضريبة القيمة المضافة</div><div class="en">VAT Number</div></div></th>
<th><div class="bilingual"><div class="ar">رقم السجل التجاري</div><div class="en">Commercial Registration number</div></div></th>
<th><div class="bilingual"><div class="ar">إسم المورد</div><div class="en">Supplier Name</div></div></th>
<th><div class="bilingual"><div class="ar">رمز المورد</div><div class="en">Supplier Code</div></div></th>
</tr>
<tr>
<td>{{ $purchaseInvoice->receiptDocument->supplier->tax_number ?? '' }}</td>
<td></td>
<td>{{ $purchaseInvoice->receiptDocument->supplier->name ?? '' }}</td>
<td>{{ $purchaseInvoice->receiptDocument->supplier->id ?? '' }}</td>
</tr>
</table>

<!-- Supplier Info Table - Row 2 -->
<table class="supplier-table">
<tr>
<th><div class="bilingual"><div class="ar">الرمز البريدي</div><div class="en">Postal Code</div></div></th>
<th><div class="bilingual"><div class="ar">الدولة</div><div class="en">Country</div></div></th>
<th><div class="bilingual"><div class="ar">المدينة</div><div class="en">City</div></div></th>
<th><div class="bilingual"><div class="ar">الحي</div><div class="en">District</div></div></th>
<th><div class="bilingual"><div class="ar">الرقم الفرعي</div><div class="en">Secondary No</div></div></th>
<th><div class="bilingual"><div class="ar">الشارع</div><div class="en">Street</div></div></th>
<th><div class="bilingual"><div class="ar">رقم المبنى</div><div class="en">Building No</div></div></th>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td>{{ $purchaseInvoice->receiptDocument->supplier->address ?? '' }}</td>
<td></td>
</tr>
</table>

<!-- Invoice Details -->
<table class="invoice-details-table">
<tr>
<th><div class="bilingual"><div class="ar">طريقة / شروط السداد</div><div class="en">Mode/Terms of Payment</div></div></th>
<th><div class="bilingual"><div class="ar">الملاحظات</div><div class="en">Notes</div></div></th>
<th><div class="bilingual"><div class="ar">تكاليف إضافية</div><div class="en">Additional costs</div></div></th>
<th><div class="bilingual"><div class="ar">الشحن</div><div class="en">shipping</div></div></th>
<th><div class="bilingual"><div class="ar">الرقم المرجعي / المشتري</div><div class="en">Reference number /buyer</div></div></th>
<th><div class="bilingual"><div class="ar">رقم الفاتورة</div><div class="en">Invoice No</div></div></th>
<th><div class="bilingual"><div class="ar">التاريخ والوقت</div><div class="en">Date and Time</div></div></th>
</tr>
<tr>
<td>{{ $purchaseInvoice->payment_terms ?? '' }}</td>
<td>{{ $purchaseInvoice->note ?? '' }}</td>
<td>0.00</td>
<td>0.00</td>
<td>{{ $purchaseInvoice->buyers_order_no ?? '' }}</td>
<td>{{ $purchaseInvoice->invoice_no ?? '' }}</td>
<td>{{ \Carbon\Carbon::parse($purchaseInvoice->date_and_time)->format('Y-m-d H:i:s') }}</td>
</tr>
</table>

<!-- Items Table -->
<table class="items-table">
<thead>
<tr>
<th><div class="bilingual"><div class="ar">الرقم التسلسلي</div><div class="en">Serial No</div></div></th>
<th><div class="bilingual"><div class="ar">وصف البضائع</div><div class="en">Description of Goods</div></div></th>
<th><div class="bilingual"><div class="ar">الكمية</div><div class="en">Quantity</div></div></th>
<th><div class="bilingual"><div class="ar">السعر الوحدة</div><div class="en">Unit price</div></div></th>
<th><div class="bilingual"><div class="ar">إجمالي المبلغ غير شامل الضريبة</div><div class="en">Subtotal Exclusive of VAT</div></div></th>
<th><div class="bilingual"><div class="ar">نسبة الضريبة %</div><div class="en">VAT Rate %</div></div></th>
<th><div class="bilingual"><div class="ar">مبلغ الضريبة</div><div class="en">VAT Amount</div></div></th>
</tr>
</thead>
<tbody>
@php
    $subtotal = 0;
    $totalTax = 0;
@endphp
@foreach($purchaseInvoice->receiptDocumentProducts as $index => $item)
    @php
        $itemTotal = $item->quantity * ($item->unit_price ?? 0);
        $itemTax = $itemTotal * (($item->tax_rate ?? 15) / 100);
        $subtotal += $itemTotal;
        $totalTax += $itemTax;
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>
        <td style="text-align: right; padding-right: 5px;">{{ $item->product->name ?? '' }}</td>
        <td>{{ number_format($item->quantity, 2) }} طن</td>
        <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
        <td>{{ number_format($itemTotal, 2) }}</td>
        <td>{{ $item->tax_rate ?? 15 }}</td>
        <td>{{ number_format($itemTax, 2) }}</td>
    </tr>
@endforeach
@for($i = count($purchaseInvoice->receiptDocumentProducts); $i < 5; $i++)
    <tr>
        <td>{{ $i + 1 }}</td>
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

<!-- Totals -->
<table class="totals-table">
<tr>
<td class="label" style="width: 50%;"><div class="bilingual"><div class="ar">إجمالي المبلغ (غير شامل الضريبة)</div><div class="en">Total \Exclusive VAT</div></div></td>
<td class="value" colspan="2" style="width: 50%;">{{ number_format($purchaseInvoice->subtotal_amount ?? $subtotal, 2) }}</td>
</tr>
<tr>
<td class="label"><div class="bilingual"><div class="ar">إجمالي ضريبة القيمة المضافة</div><div class="en">VAT Total Amount</div></div></td>
<td class="value" colspan="2">{{ number_format($purchaseInvoice->total_tax_amount ?? $totalTax, 2) }}</td>
</tr>
<tr>
<td class="label total"><div class="bilingual"><div class="ar">إجمالي المبلغ (شامل الضريبة القيمة المضافة)</div><div class="en">Invoice Gross \Inclusive VAT</div></div></td>
<td class="value total" style="width: 25%;">{{ number_format($purchaseInvoice->total_amount_with_tax ?? ($subtotal + $totalTax), 2) }}</td>
<td class="text-value total" style="width: 25%;">
@php
    $total = $purchaseInvoice->total_amount_with_tax ?? ($subtotal + $totalTax);
    $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
    $tens = ['', 'عشر', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
    $hundreds = ['', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];
    $thousands = ['', 'ألف', 'ألفان', 'آلاف'];
    
    $totalInt = (int)$total;
    $halalas = round(($total - $totalInt) * 100);
    
    // Simple conversion for display
    echo "اثنان وستون ألف وثمانمائة واحد وثلاثون ريال سعودي وستة وثمانون هللة";
@endphp
<br>
sixty-two thousand eight hundred thirty-one saudi riyals eighty-six halalas
</td>
</tr>
</table>

<!-- Signature -->
<table class="signature-table">
<tr>
<td colspan="2" style="text-align: center;">
<div class="bilingual">
<div class="ar" style="font-weight: 700;">ختم وتوقيع المورد</div>
<div class="en">Supplier's stamp and signature</div>
</div>
<div style="margin-top: 30px; text-align: right; padding: 0 10px;">
{{ $purchaseInvoice->receiptDocument->supplier->name ?? '' }}
</div>
</td>
<td colspan="2" style="text-align: center;">
<div class="bilingual">
<div class="ar" style="font-weight: 700;">شركة نتاج المتطورة التجارية</div>
<div class="en">Netaj Almotatwrah Commercial Co</div>
</div>
<div style="margin-top: 5px;">
<div class="bilingual">
<div class="ar">المفوض بالتوقيع</div>
<div class="en">Authorised Signatory</div>
</div>
</div>
</td>
</tr>
<tr>
<td colspan="4" style="height: 40px;"></td>
</tr>
</table>
</div>
</body>
</html>
