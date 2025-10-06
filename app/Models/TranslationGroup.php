<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationGroup extends Model
{
    protected $fillable = [];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    /**
     * Получение перевода для конкретного языка
     */
    public function getTranslation($locale = 'ru')
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
        
        return $translation ? $translation->title : '';
    }
}


