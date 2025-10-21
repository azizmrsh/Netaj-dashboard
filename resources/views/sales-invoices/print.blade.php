<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>فاتورة مبيعات - {{ $salesInvoice->invoice_no ?? 'غير محدد' }}</title>
<link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* تحسينات الطباعة */
@media print {
    @page {
        size: A4;
        margin: 0;
    }
    body {
        margin: 0;
        padding: 0;
        direction: rtl;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .container {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        padding: 10mm;
        box-sizing: border-box;
        border: 1px solid #ccc;
        position: relative;
        page-break-inside: avoid;
    }
    .print-button {
        display: none;
    }
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}

@media screen {
    body {
        margin: 0;
        padding: 20px;
        background-color: #f0f0f0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }
    .container {
        width: 210mm;
        min-height: 297mm;
        background-color: white;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        padding: 10mm;
        box-sizing: border-box;
        position: relative;
    }
}

* {
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
    margin: 0;
    background-color: #ffffff;
    color: #333;
    font-size: 13px;
    line-height: 1.3;
    direction: rtl;
}

.container {
    max-width: 100%;
    margin: 0 auto;
    background-color: white;
    display: flex;
    flex-direction: column;
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

.date-line {
    display: flex;
    justify-content: space-between;
    margin: 4px 0;
    font-size: 12px;
    font-weight: 500;
    padding: 4px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.date-item.center-item {
    flex: 1;
    justify-content: center;
    font-weight: 600;
}

.date-input {
    width: 120px;
    padding: 3px 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
    font-size: 12px;
    text-align: center;
    background-color: #f9f9f9;
}

.intro-text {
    text-align: center;
    margin: 4px 0;
    font-size: 13px;
    line-height: 1.3;
    direction: rtl;
    padding: 4px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 6px;
    font-size: 11px;
    direction: rtl;
    table-layout: fixed;
}

th, td {
    border: 1px solid #333;
    padding: 4px 3px;
    text-align: center;
    vertical-align: middle;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

th {
    color: #000;
    font-weight: 600;
    font-size: 10px;
    background-color: #f5f5f5;
}

tbody tr:nth-child(odd) {
    background-color: #f8f8f8;
}

tbody tr:nth-child(even) {
    background-color: #ffffff;
}

.section-title {
    font-weight: 600;
    margin-top: 6px;
    margin-bottom: 3px;
    text-align: center;
    font-size: 13px;
    padding: 4px;
}

.signature-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    margin-bottom: 8px;
    font-size: 11px;
    direction: rtl;
}

.signature-table th {
    background-color: #f5f5f5;
    font-weight: 600;
    padding: 4px 3px;
    font-size: 10px;
}

.signature-table td {
    height: 35px;
    padding: 4px 3px;
    vertical-align: bottom;
}

.signature-table .name-field {
    height: 20px;
    padding: 3px;
    vertical-align: middle;
}

.footer {
    margin-top: auto;
    text-align: center;
    font-size: 13px;
    padding: 6px;
    border-top: 2px solid #d4af37;
    font-weight: 600;
}

.bilingual {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.bilingual .arabic {
    direction: rtl;
    text-align: right;
    flex: 1;
    padding-right: 1px;
    font-size: inherit;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
}

.bilingual .english {
    direction: ltr;
    text-align: left;
    flex: 1;
    padding-left: 1px;
    font-size: inherit;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
}

th .bilingual {
    flex-direction: column;
    gap: 0px;
}

th .bilingual .arabic {
    font-weight: 600;
    font-size: 10px;
}

th .bilingual .english {
    font-weight: 500;
    font-size: 9px;
}

.company-info {
    margin-bottom: 2px;
}

.company-info strong {
    font-weight: 600;
    font-size: 14px;
    display: block;
    margin-bottom: 4px;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
}

.company-info .info-details {
    font-size: 11px;
    line-height: 1.4;
    font-family: 'Segoe UI', 'Cairo', Arial, sans-serif;
}

.print-button {
    position: fixed;
    top: 20px;
    left: 20px;
    padding: 10px 20px;
    background-color: #d4af37;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    z-index: 1000;
}

.print-button:hover {
    background-color: #b8942a;
}

.table-container {
    width: 100%;
    overflow: hidden;
    margin-bottom: 5px;
}

input[type="text"], input[type="date"], input[type="number"] {
    font-size: 11px;
    padding: 3px;
    width: 100%;
    border: none;
    background: transparent;
    text-align: center;
    outline: none;
}

@media print {
    .header { page-break-after: avoid; }
    .section-title { page-break-after: avoid; }
    .signature-table { page-break-before: avoid; }
    input { border: none !important; background: transparent !important; }
    input[type="text"], input[type="date"], input[type="number"] { border: none; box-shadow: none; }
}

@media screen {
    input[type="text"], input[type="date"], input[type="number"] {
        border: 1px solid #ddd;
        background-color: #f9f9f9;
    }
}

.compact-row { margin-bottom: 3px; }
.compact-table { margin-bottom: 4px; }
.compact-section { margin-top: 3px; margin-bottom: 3px; }

.client-table th:nth-child(1) { width: 20%; }
.client-table th:nth-child(2) { width: 30%; }
.client-table th:nth-child(3) { width: 25%; }
.client-table th:nth-child(4) { width: 25%; }

.invoice-info-table th:nth-child(1) { width: 25%; }
.invoice-info-table th:nth-child(2) { width: 25%; }
.invoice-info-table th:nth-child(3) { width: 25%; }
.invoice-info-table th:nth-child(4) { width: 25%; }

.materials-table th:nth-child(1) { width: 8%; }
.materials-table th:nth-child(2) { width: 35%; }
.materials-table th:nth-child(3) { width: 8%; }
.materials-table th:nth-child(4) { width: 12%; }
.materials-table th:nth-child(5) { width: 12%; }
.materials-table th:nth-child(6) { width: 13%; }
.materials-table th:nth-child(7) { width: 12%; }

.totals-table {
    width: 50%;
    margin-left: auto;
    margin-right: 0;
    font-size: 12px;
}

.totals-table td {
    padding: 5px 8px;
    font-weight: 500;
}

.totals-table .label-cell {
    text-align: right;
    background-color: #f5f5f5;
    font-weight: 600;
}

.totals-table .value-cell {
    text-align: center;
    background-color: #fff;
}

.totals-table .total-row {
    background-color: #d4af37 !important;
    color: #000;
    font-weight: 700;
    font-size: 13px;
}

.signature-table th:nth-child(1) { width: 25%; }
.signature-table th:nth-child(2) { width: 25%; }
.signature-table th:nth-child(3) { width: 25%; }
.signature-table th:nth-child(4) { width: 25%; }

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
</style>
</head>
<body>
<button class="print-button" onclick="window.print()">طباعة PDF</button>

<div class="container">
<!-- Header -->
<div class="header">
<div class="header-left">
<div class="header-content">
<div class="company-title">شركة نتاج المتطورة التجارية</div>
<div class="company-details">
رقم السجل التجاري: ١٢٣٤٥٦٧٨٩٠<br>
رقم ضريبة القيمة المضافة: ٣٠٠١٢٣٤٥٦٧٨٩٠<br>
البريد الإلكتروني: info@netaj.com<br>
الرمز البريدي: ١١٥٦٤<br>
العنوان: ص.ب: ١٢٣٤٥ الرياض<br>
المنطقة - الدولة: الرياض - المملكة العربية السعودية
</div>
</div>
</div>

<div class="header-center">
<div class="logo-container">
<img src="{{ asset('images/logo.svg') }}" alt="Company Logo" style="width: 100%; height: 100%; object-fit: contain;">
</div>
<h2>Netaj Almotatwrah Commercial Company</h2>
<h2>Sales Invoice - Tax Invoice</h2>
<h2>فاتورة مبيعات - فاتورة ضريبية</h2>
</div>

<div class="header-right">
<div class="header-content">
<div class="company-title">Netaj Almotatwrah Commercial Company</div>
<div class="company-details">
P.O. Box: 12345 Riyadh 11564<br>
Tel: 011-1234567<br>
State - Country: Riyadh - Kingdom of Saudi Arabia<br>
Postal code: 11564<br>
E-mail: info@netaj.com<br>
Commercial Registration Number: 1234567890<br>
VAT Number: 3001234567890
</div>
</div>
</div>
</div>

<!-- Date Line -->
<div class="date-line">
<div class="date-item arabic-date">
<span class="arabic">التاريخ&nbsp;:</span>
<input type="text" class="date-input" value="{{ \Carbon\Carbon::parse($salesInvoice->invoice_date)->format('Y/m/d') }}" readonly>
</div>
<div class="date-item center-item">
<span style="font-weight: 600; font-size: 13px;">{{ $salesInvoice->invoice_no ?? 'INV-' . $salesInvoice->id }}</span>
</div>
<div class="date-item english-date">
<input type="text" class="date-input" value="{{ \Carbon\Carbon::parse($salesInvoice->invoice_date)->format('Y/m/d') }}" readonly>
<span class="english">:Date&nbsp;&nbsp;</span>
</div>
</div>

<!-- Intro Text -->
<div class="intro-text">
<div class="bilingual">
<div class="arabic">تم بيع المواد للعميل حسب المعلومات التالية:</div>
<div class="english">The following materials are sold to the client as detailed below:</div>
</div>
</div>

<!-- Client Information -->
<div class="section-title compact-section">
<div class="bilingual">
<div class="arabic">معلومات العميل</div>
<div class="english">Client Information</div>
</div>
</div>
<div class="table-container compact-table">
<table class="client-table">
<tr>
<th>
<div class="bilingual">
<div class="arabic">رقم الجوال</div>
<div class="english">Mobile No</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">الرقم الضريبي</div>
<div class="english">Tax Number</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">العنوان</div>
<div class="english">Address</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">اسم العميل</div>
<div class="english">Client Name</div>
</div>
</th>
</tr>
<tr>
<td>{{ $salesInvoice->customer_phone ?? '' }}</td>
<td>{{ $salesInvoice->customer_tax_number ?? '' }}</td>
<td>{{ $salesInvoice->customer_address ?? '' }}</td>
<td>{{ $salesInvoice->customer_name ?? '' }}</td>
</tr>
</table>
</div>

<!-- Invoice Information -->
<div class="section-title compact-section">
<div class="bilingual">
<div class="arabic">معلومات الفاتورة</div>
<div class="english">Invoice Information</div>
</div>
</div>
<div class="table-container compact-table">
<table class="invoice-info-table">
<tr>
<th>
<div class="bilingual">
<div class="arabic">طريقة الدفع</div>
<div class="english">Payment Method</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">الحالة</div>
<div class="english">Status</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">تاريخ الاستحقاق</div>
<div class="english">Due Date</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">رقم سند التسليم</div>
<div class="english">Delivery Document No</div>
</div>
</th>
</tr>
<tr>
<td>{{ $salesInvoice->payment_method ?? '' }}</td>
<td>
@php
$statusLabels = [
    'draft' => 'مسودة / Draft',
    'sent' => 'مرسلة / Sent',
    'paid' => 'مدفوعة / Paid',
    'cancelled' => 'ملغاة / Cancelled',
];
@endphp
{{ $statusLabels[$salesInvoice->status] ?? $salesInvoice->status }}
</td>
<td>{{ $salesInvoice->due_date ? \Carbon\Carbon::parse($salesInvoice->due_date)->format('Y/m/d') : '' }}</td>
<td>{{ $salesInvoice->deliveryDocument->document_number ?? 'DEL-' . $salesInvoice->delivery_document_id }}</td>
</tr>
</table>
</div>

<!-- Materials Table -->
<div class="section-title compact-section">
<div class="bilingual">
<div class="arabic">المواد التالية حسب الجدول أدناه</div>
<div class="english">The following materials are listed in the table below</div>
</div>
</div>
<div class="table-container compact-table">
<table class="materials-table">
<thead>
<tr>
<th>
<div class="bilingual">
<div class="arabic">م</div>
<div class="english">No.</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">الوصف / المنتج</div>
<div class="english">Description / Product</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">الوحدة</div>
<div class="english">Unit</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">الكمية</div>
<div class="english">Quantity</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">سعر الوحدة</div>
<div class="english">Unit Price</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">المبلغ قبل الضريبة</div>
<div class="english">Amount Before Tax</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">الضريبة ({{ $salesInvoice->tax_rate ?? 15 }}%)</div>
<div class="english">Tax ({{ $salesInvoice->tax_rate ?? 15 }}%)</div>
</div>
</th>
</tr>
</thead>
<tbody>
@php
    $subtotal = 0;
    $totalTax = 0;
@endphp
@foreach($salesInvoice->deliveryDocumentProducts as $index => $item)
    @php
        $itemTotal = $item->quantity * ($item->unit_price ?? 0);
        $itemTax = $itemTotal * (($item->tax_rate ?? 0) / 100);
        $subtotal += $itemTotal;
        $totalTax += $itemTax;
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>
        <td style="text-align: right; padding-right: 8px;">{{ $item->product->name ?? '' }}</td>
        <td>{{ $item->product->unit ?? '' }}</td>
        <td>{{ number_format($item->quantity, 3) }}</td>
        <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
        <td>{{ number_format($itemTotal, 2) }}</td>
        <td>{{ number_format($itemTax, 2) }}</td>
    </tr>
@endforeach
@for($i = count($salesInvoice->deliveryDocumentProducts); $i < 10; $i++)
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
</div>

<!-- Totals Table -->
<div class="table-container compact-table">
<table class="totals-table">
<tr>
<td class="label-cell">
<div class="bilingual">
<div class="arabic">المبلغ الإجمالي قبل الضريبة</div>
<div class="english">Total Amount Before Tax</div>
</div>
</td>
<td class="value-cell">{{ number_format($salesInvoice->subtotal ?? $subtotal, 2) }} ريال</td>
</tr>
<tr>
<td class="label-cell">
<div class="bilingual">
<div class="arabic">الخصم</div>
<div class="english">Discount</div>
</div>
</td>
<td class="value-cell">{{ number_format($salesInvoice->discount_amount ?? 0, 2) }} ريال</td>
</tr>
<tr>
<td class="label-cell">
<div class="bilingual">
<div class="arabic">ضريبة القيمة المضافة ({{ $salesInvoice->tax_rate ?? 15 }}%)</div>
<div class="english">VAT ({{ $salesInvoice->tax_rate ?? 15 }}%)</div>
</div>
</td>
<td class="value-cell">{{ number_format($salesInvoice->tax_amount ?? $totalTax, 2) }} ريال</td>
</tr>
<tr class="total-row">
<td class="label-cell total-row">
<div class="bilingual">
<div class="arabic">المبلغ الإجمالي شامل الضريبة</div>
<div class="english">Total Amount Including Tax</div>
</div>
</td>
<td class="value-cell total-row">{{ number_format($salesInvoice->total_amount ?? ($subtotal + $totalTax - ($salesInvoice->discount_amount ?? 0)), 2) }} ريال</td>
</tr>
</table>
</div>

<!-- Notes Section (if exists) -->
@if($salesInvoice->notes)
<div class="section-title compact-section">
<div class="bilingual">
<div class="arabic">ملاحظات</div>
<div class="english">Notes</div>
</div>
</div>
<div class="table-container compact-table">
<table>
<tr>
<td style="text-align: right; padding: 8px; min-height: 40px;">{{ $salesInvoice->notes }}</td>
</tr>
</table>
</div>
@endif

<!-- Signature Table - 4 columns and 3 rows -->
<div class="table-container compact-table">
<table class="signature-table">
<tr>
<th>
<div class="bilingual">
<div class="arabic">ختم الشركة</div>
<div class="english">Company Stamp</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">توقيع المدير المالي</div>
<div class="english">CFO Signature</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">اسم وتوقيع المحاسب</div>
<div class="english">Accountant Name & Signature</div>
</div>
</th>
<th>
<div class="bilingual">
<div class="arabic">اسم وتوقيع العميل</div>
<div class="english">Client Name & Signature</div>
</div>
</th>
</tr>
<tr>
<td class="name-field"></td>
<td class="name-field"></td>
<td class="name-field"></td>
<td class="name-field">{{ $salesInvoice->customer_name ?? '' }}</td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
</table>
</div>

<!-- Footer -->
<div class="footer">
<div class="bilingual">
<div class="arabic">فاتورة مبيعات - فاتورة ضريبية</div>
<div class="english">Sales Invoice - Tax Invoice</div>
</div>
</div>
</div>
</body>
</html>
