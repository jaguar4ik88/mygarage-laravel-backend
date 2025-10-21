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
        'platform',
        'transaction_id',
        'reminder_expenses_enabled',
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
            'reminder_expenses_enabled' => 'boolean',
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function vehicleDocuments(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
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
            
            // Удаляем подписки
            $user->subscriptions()->delete();
            
            // Удаляем документы
            $user->vehicleDocuments()->delete();
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
     * Get current active subscription
     */
    public function currentSubscription()
    {
        return $this->subscriptions()
                    ->with('subscription')
                    ->active()
                    ->latest()
                    ->first();
    }

    /**
     * Get max vehicles for user's plan
     */
    public function getMaxVehicles(): int
    {
        $limits = [
            'free' => 1,
            'pro' => 3,
            'premium' => 3,
        ];

        return $limits[$this->getPlanType()] ?? 1;
    }

    /**
     * Get max reminders for user's plan
     */
    public function getMaxReminders(): ?int
    {
        $limits = [
            'free' => 5,
            'pro' => null, // unlimited
            'premium' => null, // unlimited
        ];

        $planType = $this->getPlanType();
        
        // Check if key exists in array (not using ?? because it treats null as "not set")
        if (array_key_exists($planType, $limits)) {
            return $limits[$planType];
        }
        
        return 5; // default for unknown plans
    }

    /**
     * Check if user can add more vehicles
     */
    public function canAddVehicle(): bool
    {
        $maxVehicles = $this->getMaxVehicles();
        $currentCount = $this->vehicles()->count();
        
        return $currentCount < $maxVehicles;
    }

    /**
     * Check if user can add more reminders
     */
    public function canAddReminder(): bool
    {
        $maxReminders = $this->getMaxReminders();
        
        // null means unlimited
        if ($maxReminders === null) {
            return true;
        }
        
        $currentCount = $this->reminders()->count();
        
        return $currentCount < $maxReminders;
    }

    /**
     * Check if user has PRO features access
     */
    public function isPro(): bool
    {
        return in_array($this->getPlanType(), ['pro', 'premium']) && $this->hasActiveSubscription();
    }

    /**
     * Check if user has PREMIUM features access
     */
    public function isPremium(): bool
    {
        return $this->getPlanType() === 'premium' && $this->hasActiveSubscription();
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
                'statistics',
                'expenses_history',
            ],
            'pro' => [
                'vehicles_limit' => 3,
                'reminders_limit' => null, // unlimited
                'photo_documents', // фото документов
                'receipt_photos', // фото чеков
                'pdf_export', // экспорт в PDF
                'expense_reminders', // напоминания о тратах
                'all_free_features',
            ],
            'premium' => [
                'vehicles_limit' => 3,
                'reminders_limit' => null,
                'ai_assistant', // AI помощник
                'trips', // функционал поездки
                'fuel_tracking', // полный учет заправок
                'mileage_tracking', // ежедневный ввод пробега
                'smart_reminders', // умные напоминания (пробег + дата)
                'cloud_storage', // облачное хранилище
                'all_pro_features',
            ],
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
