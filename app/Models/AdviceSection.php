<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdviceSection extends Model
{
    protected $fillable = [
        'slug',
        'icon',
        'is_active',
        'sort_order',
        'title_translation_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function titleGroup(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class, 'title_translation_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AdviceItem::class);
    }
}


