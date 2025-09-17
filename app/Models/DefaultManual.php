<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class DefaultManual extends Model
{
    protected $fillable = [
        'manual_section_id',
        'title',
        'content',
        'pdf_path',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ManualSection::class, 'manual_section_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(DefaultManualTranslation::class);
    }

    public function getPdfUrlAttribute(): ?string
    {
        if (!$this->pdf_path) {
            return null;
        }
        return Storage::disk('public')->url($this->pdf_path);
    }
}


