<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceHistory extends Model
{
    use HasFactory;

    protected $table = 'service_history';

    protected $fillable = [
        'vehicle_id',
        'type',
        'title',
        'description',
        'cost',
        'mileage',
        'service_date',
        'station_name',
    ];

    protected $casts = [
        'service_date' => 'datetime',
        'cost' => 'decimal:2',
        'mileage' => 'integer',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
