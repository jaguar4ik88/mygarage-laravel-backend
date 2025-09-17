<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqQuestionTranslation extends Model
{
    protected $fillable = [
        'faq_question_id',
        'locale',
        'question',
        'answer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(FaqQuestion::class);
    }
}
