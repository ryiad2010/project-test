<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'related_products',
    ];

    // Automatically cast related_products as array
    protected $casts = [
        'related_products' => 'array',
    ];
}
