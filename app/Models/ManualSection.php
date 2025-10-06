<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManualSection extends Model
{
    protected $fillable = [
        'slug',
        'key',
        'icon',
        'is_active',
        'sort_order',
        'title_translation_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Связь с группой переводов для заголовка
     */
    public function titleGroup(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class, 'title_translation_id');
    }


    /**
     * Связь с переводами (старая система, оставляем для совместимости)
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ManualSectionTranslation::class);
    }

    /**
     * Связь с рекомендациями по обслуживанию
     */
    public function carRecommendations(): HasMany
    {
        return $this->hasMany(CarRecommendation::class);
    }

    /**
     * Получение заголовка через систему переводов
     */
    public function getTitleAttribute($locale = 'ru')
    {
        if ($this->titleGroup) {
            return $this->titleGroup->getTranslation($locale);
        }
        
        // Fallback на старую систему переводов
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation ? $translation->title : $this->slug;
    }
}


