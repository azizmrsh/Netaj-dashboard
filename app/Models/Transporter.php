<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transporter extends Model
{
    use HasFactory;
    protected $fillable = [
        'transport_company_id',
        'name',
        'phone',
        'email',
        'note',
        'id_number',
        'tax_number',
        'driver_name',
        'document_no',
        'car_no',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the transport company that owns the transporter.
     */
    public function transportCompany(): BelongsTo
    {
        return $this->belongsTo(TransportCompany::class, 'transport_company_id');
    }
}
