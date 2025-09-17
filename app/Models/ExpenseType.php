<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseType extends Model
{
    protected $fillable = [
        'slug',
        'is_active',
        'translation_group_id',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(ExpensesHistory::class, 'expense_type_id');
    }

    public function translationGroup(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class, 'translation_group_id');
    }

    public function getTranslatedName($locale = 'uk')
    {
        if (!$this->translationGroup) {
            return $this->slug;
        }

        $translation = $this->translationGroup->translations()
            ->where('locale', $locale)
            ->first();

        return $translation ? $translation->title : $this->slug;
    }
}
