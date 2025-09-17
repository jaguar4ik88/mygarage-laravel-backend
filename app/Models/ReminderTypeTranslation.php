<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderTypeTranslation extends Model
{
    protected $fillable = [
        'reminder_type_id',
        'locale',
        'title',
    ];

    public function reminderType(): BelongsTo
    {
        return $this->belongsTo(ReminderType::class);
    }
}
