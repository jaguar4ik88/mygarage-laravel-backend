<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarModel extends EloquentModel
{
    protected $fillable = ['car_maker_id', 'name'];

    public function maker(): BelongsTo
    {
        return $this->belongsTo(CarMaker::class, 'car_maker_id');
    }

    public function engines(): HasMany
    {
        return $this->hasMany(CarEngine::class, 'car_model_id');
    }
}


