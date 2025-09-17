<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdviceItem extends Model
{
    protected $fillable = [
        'advice_section_id',
        'title_translation_id',
        'content_translation_id',
        'icon',
        'pdf_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(AdviceSection::class, 'advice_section_id');
    }

    public function titleGroup(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class, 'title_translation_id');
    }

    public function contentGroup(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class, 'content_translation_id');
    }
}


