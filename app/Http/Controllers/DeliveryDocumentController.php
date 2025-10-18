<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDocument;
use Illuminate\Http\Request;

class DeliveryDocumentController extends Controller
{
    public function print(DeliveryDocument $deliveryDocument)
    {
        // Load relationships for the delivery document
        $deliveryDocument->load([
            'customer',
            'transporter', 
            'deliveryDocumentProducts.product'
        ]);

        return view('delivery-documents.print', compact('deliveryDocument'));
    }
}