<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarEngine extends Model
{
    protected $fillable = ['car_maker_id', 'car_model_id', 'label', 'raw'];

    protected $casts = [
        'raw' => 'array',
    ];

    public function maker(): BelongsTo
    {
        return $this->belongsTo(CarMaker::class, 'car_maker_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
}


