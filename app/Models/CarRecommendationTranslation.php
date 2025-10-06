<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarRecommendationTranslation extends Model
{
    protected $fillable = [
        'car_recommendation_id',
        'locale',
        'recommendation',
    ];

    /**
     * Связь с рекомендацией
     */
    public function carRecommendation(): BelongsTo
    {
        return $this->belongsTo(CarRecommendation::class);
    }

    /**
     * Scope для поиска по языку
     */
    public function scopeForLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope для поиска по рекомендации
     */
    public function scopeForRecommendation($query, $carRecommendationId)
    {
        return $query->where('car_recommendation_id', $carRecommendationId);
    }
}