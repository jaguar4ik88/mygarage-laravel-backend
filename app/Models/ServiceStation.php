<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'phone',
        'rating',
        'distance',
        'latitude',
        'longitude',
        'types',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'rating' => 'decimal:1',
        'distance' => 'decimal:1',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'types' => 'array',
    ];
}
