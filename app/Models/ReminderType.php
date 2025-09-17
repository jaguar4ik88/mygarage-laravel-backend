<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReminderType extends Model
{
    protected $fillable = [
        'key',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ReminderTypeTranslation::class);
    }

    public function getTitleAttribute($locale = 'ru')
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation ? $translation->title : $this->key;
    }
}
