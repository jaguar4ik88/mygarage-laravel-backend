<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    protected $fillable = [
        'translation_group_id',
        'locale',
        'title',
        'content',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(TranslationGroup::class, 'translation_group_id');
    }
}


