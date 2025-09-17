<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualSectionTranslation extends Model
{
    protected $fillable = [
        'manual_section_id',
        'locale',
        'title',
    ];

    public function manualSection(): BelongsTo
    {
        return $this->belongsTo(ManualSection::class);
    }
}
