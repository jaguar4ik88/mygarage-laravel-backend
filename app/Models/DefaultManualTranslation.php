<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultManualTranslation extends Model
{
    protected $fillable = [
        'default_manual_id',
        'locale',
        'title',
        'content',
    ];

    public function defaultManual(): BelongsTo
    {
        return $this->belongsTo(DefaultManual::class);
    }
}
