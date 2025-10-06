<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarRecommendation extends Model
{
    protected $fillable = [
        'maker',
        'model',
        'year',
        'mileage_interval',
        'manual_section_id',
    ];

    protected $casts = [
        'mileage_interval' => 'integer',
    ];

    /**
     * Связь с секцией руководства
     */
    public function manualSection(): BelongsTo
    {
        return $this->belongsTo(ManualSection::class);
    }

    /**
     * Scope для поиска по марке и модели
     */
    public function scopeForCar($query, $maker, $model, $year = null)
    {
        $query->where('maker', $maker)
              ->where('model', $model);
              
        if ($year) {
            $query->where('year', $year);
        }
        
        return $query;
    }

    /**
     * Scope для поиска по пробегу
     */
    public function scopeForMileage($query, $mileage)
    {
        return $query->where('mileage_interval', '<=', $mileage)
                    ->orderBy('mileage_interval', 'desc');
    }

    /**
     * Scope для поиска по типу обслуживания (через секцию мануала)
     */
    public function scopeForItem($query, $itemKey)
    {
        return $query->whereHas('manualSection', function($q) use ($itemKey) {
            $q->where('key', $itemKey);
        });
    }

    /**
     * Связь с переводами рекомендаций
     */
    public function translations(): HasMany
    {
        return $this->hasMany(CarRecommendationTranslation::class);
    }

    /**
     * Получение перевода рекомендации для конкретного языка
     */
    public function getRecommendationTranslation($locale = 'ru')
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        
        if (!$translation) {
            // Fallback на украинский
            $translation = $this->translations()->where('locale', 'uk')->first();
        }
        
        if (!$translation) {
            // Fallback на английский
            $translation = $this->translations()->where('locale', 'en')->first();
        }
        
        if (!$translation) {
            // Fallback на первый доступный
            $translation = $this->translations()->first();
        }
        
        return $translation ? $translation->recommendation : 'Рекомендация недоступна';
    }

    /**
     * Создание или обновление перевода рекомендации
     */
    public function setRecommendationTranslation($locale, $recommendation)
    {
        return $this->translations()->updateOrCreate(
            ['locale' => $locale],
            ['recommendation' => $recommendation]
        );
    }

    /**
     * Получение всех переводов рекомендации
     */
    public function getAllRecommendationTranslations()
    {
        return $this->translations->mapWithKeys(function($translation) {
            return [$translation->locale => $translation->recommendation];
        })->toArray();
    }
}