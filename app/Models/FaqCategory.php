<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    protected $fillable = [
        'key',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(FaqCategoryTranslation::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(FaqQuestion::class);
    }

    public function getNameAttribute($locale = 'ru')
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation ? $translation->name : $this->key;
    }
}
