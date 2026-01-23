<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'tax_number',
        'commercial_registration_number',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the transporters (drivers) for the transport company.
     */
    public function transporters(): HasMany
    {
        return $this->hasMany(Transporter::class, 'transport_company_id');
    }

    /**
     * Get active transporters only.
     */
    public function activeTransporters(): HasMany
    {
        return $this->transporters()->where('is_active', true);
    }
}
