<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'currency',
        'is_admin',
        'google_id',
        'apple_id',
        'plan_type',
        'subscription_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'subscription_expires_at' => 'datetime',
        ];
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function serviceStations(): HasMany
    {
        return $this->hasMany(ServiceStation::class);
    }

    public function expensesHistory(): HasMany
    {
        return $this->hasMany(ExpensesHistory::class);
    }

    /**
     * Удаление пользователя со всеми связанными данными
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Удаляем все машины пользователя (каскадно удалятся связанные данные)
            $user->vehicles()->delete();
            
            // Удаляем напоминания
            $user->reminders()->delete();
            
            // Удаляем СТО
            $user->serviceStations()->delete();
            
            // Удаляем траты
            $user->expensesHistory()->delete();
            
            // Удаляем токены авторизации
            $user->tokens()->delete();
        });
    }

    /**
     * Get the user's current subscription plan
     */
    public function getPlanType(): string
    {
        return $this->plan_type ?? 'free';
    }

    /**
     * Check if user has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->plan_type === 'free') {
            return true; // Free plan is always active
        }

        return $this->subscription_expires_at && $this->subscription_expires_at->isFuture();
    }

    /**
     * Check if user has access to a specific feature
     */
    public function hasFeature(string $feature): bool
    {
        $planFeatures = $this->getPlanFeatures();
        return in_array($feature, $planFeatures) && $this->hasActiveSubscription();
    }

    /**
     * Get features available for user's plan
     */
    public function getPlanFeatures(): array
    {
        $features = [
            'free' => [
                'vehicles_limit' => 1,
                'reminders_limit' => 5,
                'basic_reminders',
                'basic_manual',
                'basic_advice',
                'sto_search',
                'basic_reports',
            ],
            'pro' => [
                'vehicles_limit' => 3,
                'reminders_limit' => -1, // unlimited
                'photo_documents',
                'fuel_tracking',
                'mileage_tracking',
                'advanced_analytics',
                'smart_reminders',
                'widgets',
                'export_data',
                'advanced_reports',
            ],
            'premium' => [
                'vehicles_limit' => -1, // unlimited
                'reminders_limit' => -1,
                'gps_integration',
                'obd_diagnosis',
                'ai_assistant',
                'checklists',
                'gamification',
                'cloud_backup',
                'api_integrations',
            ],
            'business' => [
                'vehicles_limit' => -1,
                'reminders_limit' => -1,
                'client_management',
                'business_reports',
                '1c_integration',
                'master_app',
                'business_analytics',
            ]
        ];

        return $features[$this->getPlanType()] ?? $features['free'];
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
