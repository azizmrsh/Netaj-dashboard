<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    use HasFactory;
    protected $fillable = [
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
}
