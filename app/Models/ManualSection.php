<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManualSection extends Model
{
    protected $fillable = [
        'key',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function defaults(): HasMany
    {
        return $this->hasMany(DefaultManual::class, 'manual_section_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ManualSectionTranslation::class);
    }

    public function getTitleAttribute($locale = 'ru')
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation ? $translation->title : $this->key;
    }
}


