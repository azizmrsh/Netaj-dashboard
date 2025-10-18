<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryDocument extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     */
    protected $table = 'delivery_documents';

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
        'purchase_order_no',
        'project_name_and_location',
        'note',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_and_time' => 'datetime',
    ];

    /**
     * Get the customer that owns the delivery document.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    /**
     * Get the transporter that owns the delivery document.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'id_transporter');
    }



    /**
     * Get the delivery document products for the delivery document.
     */
    public function deliveryDocumentProducts(): HasMany
    {
        return $this->hasMany(DeliveryDocumentProduct::class, 'delivery_document_id');
    }
}
