<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'starts_at',
        'expires_at',
        'is_active',
        'platform',
        'transaction_id',
        'original_transaction_id',
        'receipt_data',
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get subscription
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->whereNull('cancelled_at');
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
                    ->whereNotNull('expires_at');
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        if (!$this->is_active || $this->cancelled_at) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Cancel subscription
     */
    public function cancel(): bool
    {
        $this->cancelled_at = now();
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Renew subscription
     */
    public function renew(int $days = null): bool
    {
        $days = $days ?? $this->subscription->duration_days;
        
        $this->starts_at = now();
        $this->expires_at = now()->addDays($days);
        $this->is_active = true;
        $this->cancelled_at = null;
        
        return $this->save();
    }
}
