<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqQuestion extends Model
{
    protected $fillable = [
        'faq_category_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(FaqQuestionTranslation::class);
    }

    public function getQuestionAttribute($locale = 'ru')
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation ? $translation->question : '';
    }

    public function getAnswerAttribute($locale = 'ru')
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        return $translation ? $translation->answer : '';
    }
}
