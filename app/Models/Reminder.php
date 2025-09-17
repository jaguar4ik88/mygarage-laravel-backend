<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'last_service_date',
        'last_service_mileage',
        'next_service_mileage',
        'next_service_date',
        'is_active',
    ];

    protected $casts = [
        'last_service_date' => 'datetime',
        'next_service_date' => 'datetime',
        'last_service_mileage' => 'integer',
        'next_service_mileage' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Проверяет и обновляет статус активности напоминания
     * Если дата следующего обслуживания прошла, устанавливает is_active = false
     */
    public function updateActiveStatus(): void
    {
        if ($this->next_service_date && $this->next_service_date->isPast()) {
            $this->is_active = false;
            $this->save();
        }
    }

    /**
     * Scope для получения только активных напоминаний
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope для получения неактивных напоминаний
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}