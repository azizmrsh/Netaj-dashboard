<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'product_code',
        'performance_grade',
        'modification_type',
        'unit',
        'is_active',
        'price1',
        'price2',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price1' => 'decimal:2',
        'price2' => 'decimal:2',
    ];
}
