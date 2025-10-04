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
        'next_service_date',
        'is_active',
    ];

    protected $casts = [
        'next_service_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // При создании напоминания проверяем дату
        static::creating(function ($reminder) {
            // Все напоминания создаются активными по умолчанию
            // Логика деактивации просроченных напоминаний в scopeActive()
            $reminder->is_active = true;
        });
    }

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
     * Автоматически деактивирует просроченные напоминания
     */
    public function scopeActive($query)
    {
        // Сначала деактивируем просроченные напоминания
        static::where('is_active', true)
            ->where('next_service_date', '<', now())
            ->update(['is_active' => false]);
            
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