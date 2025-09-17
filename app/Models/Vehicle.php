<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vin',
        'year',
        'make',
        'model',
        'engine_type',
        'mileage',
        'image_url',
        'added_at',
        'last_modified_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceHistory(): HasMany
    {
        return $this->hasMany(ServiceHistory::class);
    }

    public function manuals(): HasMany
    {
        return $this->hasMany(VehicleManual::class)->where('is_active', true)->orderBy('sort_order');
    }
}
