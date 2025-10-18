<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;

class ReceiptDocument extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'receipt_documents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'date_and_time',
        'id_customer',
        'id_transporter',
        'purchasing_officer_name',
        'purchasing_officer_signature',
        'warehouse_officer_name',
        'warehouse_officer_signature',
        'recipient_name',
        'recipient_signature',
        'accountant_name',
        'accountant_signature',
        'purchase_invoice_no',
        'material_source',
        'note',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_and_time' => 'datetime',
    ];

    /**
     * Get the supplier that owns the receipt document.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    /**
     * Get the transporter that owns the receipt document.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'id_transporter');
    }

    /**
     * Get the receipt document products for the receipt document.
     */
    public function receiptDocumentProducts(): HasMany
    {
        return $this->hasMany(ReceiptDocumentProduct::class, 'receipt_document_id');
    }
}
