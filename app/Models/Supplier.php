<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'note',
        'name_company',
        'country',
        'address',
        'tax_number',
        'zip_code',
        'is_active',
        'national_number',
        'commercial_registration_number',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
