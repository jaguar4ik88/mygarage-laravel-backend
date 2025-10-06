<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarTyre extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'year',
        'dimension',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
    ];

    /**
     * Scope для поиска по марке и модели автомобиля
     */
    public function scopeForCar($query, $brand, $model, $year = null)
    {
        $query->where('brand', $brand)
              ->where('model', $model);
              
        if ($year) {
            $query->where('year', $year);
        }
        
        return $query;
    }

    /**
     * Scope для поиска по размеру шин
     */
    public function scopeForDimension($query, $dimension)
    {
        return $query->where('dimension', $dimension);
    }

    /**
     * Scope для поиска по году
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }
}