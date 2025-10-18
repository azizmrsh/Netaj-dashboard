<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryDocumentController;

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/delivery-documents/{deliveryDocument}/print', [DeliveryDocumentController::class, 'print'])
    ->name('delivery-documents.print');
