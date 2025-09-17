<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranslationGroup extends Model
{
    protected $fillable = [];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}


