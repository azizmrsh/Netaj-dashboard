<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryDocumentController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\ReceiptDocumentController;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/delivery-documents/{deliveryDocument}/print', [DeliveryDocumentController::class, 'print'])
    ->name('delivery-documents.print');

Route::get('/sales-invoices/{salesInvoice}/print', [SalesInvoiceController::class, 'print'])
    ->name('sales-invoices.print');

Route::get('/receipt-documents/{receiptDocument}/print', [ReceiptDocumentController::class, 'print'])
    ->name('receipt-documents.print');
