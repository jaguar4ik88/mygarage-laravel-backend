<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Services\AppleReceiptValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Get all available subscriptions
     */
    public function index(): JsonResponse
    {
        $subscriptions = Subscription::active()->get();

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }

    /**
     * Get current user's subscription
     */
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $subscription = [
            'plan_type' => $user->plan_type,
            'subscription_expires_at' => $user->subscription_expires_at,
            'is_active' => $user->hasActiveSubscription(),
            'max_vehicles' => $user->getMaxVehicles(),
            'max_reminders' => $user->getMaxReminders(),
            'features' => $user->getPlanFeatures(),
            'can_add_vehicle' => $user->canAddVehicle(),
            'can_add_reminder' => $user->canAddReminder(),
            'is_pro' => $user->isPro(),
            'is_premium' => $user->isPremium(),
        ];

        // Получаем текущую активную подписку из истории
        $currentSubscription = $user->currentSubscription();
        if ($currentSubscription) {
            $subscription['subscription_details'] = $currentSubscription;
        }

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ]);
    }

    /**
     * Verify purchase from iOS/Android store
     */
    public function verify(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:ios,android',
            'transaction_id' => 'required|string',
            'original_transaction_id' => 'nullable|string',
            'receipt_data' => 'nullable|string',
            'subscription_type' => 'required|in:free,pro,premium',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $data = $validator->validated();

        // Получаем подписку по типу
        $subscription = Subscription::where('name', $data['subscription_type'])->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found',
            ], 404);
        }

        // Initialize expiration date (will be set from receipt validation or default)
        $expiresAt = null;

        // Validate receipt for iOS (required by Apple App Store Review)
        // 
        // Apple App Store Review Guidelines 2.1 require:
        // - Server-side receipt validation for production apps
        // - Production server must first check production App Store
        // - If status 21007 (Sandbox receipt used in production), retry in sandbox
        // 
        // This allows production-signed apps to receive receipts from sandbox during review
        // See: https://developer.apple.com/documentation/appstorereceipts/verifyreceipt
        if ($data['platform'] === 'ios') {
            // In production, receipt_data is mandatory for iOS
            if (empty($data['receipt_data']) && config('app.env') === 'production') {
                Log::error('iOS subscription verification without receipt data in production', [
                    'user_id' => $user->id,
                    'subscription_type' => $data['subscription_type'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Receipt data is required for iOS subscriptions in production',
                    'error' => 'Missing receipt data',
                ], 400);
            }

            // Log receipt data status
            Log::info('Receipt data status', [
                'user_id' => $user->id,
                'has_receipt_data' => !empty($data['receipt_data']),
                'receipt_data_length' => $data['receipt_data'] ? strlen($data['receipt_data']) : 0,
                'is_production' => config('app.env') === 'production',
            ]);

            // Validate receipt if provided
            if (!empty($data['receipt_data'])) {
                try {
                    $validator = new AppleReceiptValidator();
                    
                    // Determine product ID based on subscription type
                    $productId = match($data['subscription_type']) {
                        'pro' => 'pro_garage_monthly_subscription',
                        'premium' => 'premium_garage_monthly_subscription',
                        default => null,
                    };

                    if (!$productId) {
                        Log::warning('Unknown subscription type for receipt validation', [
                            'subscription_type' => $data['subscription_type'],
                        ]);
                    } else {
                        // Validate receipt according to Apple guidelines:
                        // 1. First tries production App Store
                        // 2. If status 21007 (Sandbox receipt used in production), validates against sandbox
                        Log::info('Starting Apple receipt validation', [
                            'user_id' => $user->id,
                            'subscription_type' => $data['subscription_type'],
                            'product_id' => $productId,
                        ]);

                        $validationResult = $validator->validate($data['receipt_data']);

                        if (!$validationResult) {
                            Log::error('Apple receipt validation: Validation request failed (null result)', [
                                'user_id' => $user->id,
                                'subscription_type' => $data['subscription_type'],
                            ]);

                            if (config('app.env') === 'production') {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Receipt validation failed',
                                    'error' => 'Failed to validate receipt with App Store',
                                ], 400);
                            } else {
                                Log::warning('Apple receipt validation failed in development mode, proceeding anyway');
                            }
                        } elseif (!$validator->isValid($validationResult)) {
                            $status = $validationResult['status'] ?? 'unknown';
                            $environment = $validationResult['environment'] ?? 'unknown';
                            
                            Log::error('Apple receipt validation: Receipt is invalid', [
                                'user_id' => $user->id,
                                'subscription_type' => $data['subscription_type'],
                                'validation_status' => $status,
                                'environment' => $environment,
                                'is_production' => config('app.env') === 'production',
                            ]);

                            // В production режиме строго требуем валидацию
                            if (config('app.env') === 'production') {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Receipt validation failed',
                                    'error' => 'Invalid receipt',
                                    'status' => $status,
                                    'environment' => $environment,
                                ], 400);
                            } else {
                                // В development режиме логируем предупреждение, но продолжаем
                                Log::warning('Apple receipt validation failed in development mode, proceeding anyway', [
                                    'user_id' => $user->id,
                                    'validation_status' => $status,
                                    'environment' => $environment,
                                ]);
                                // Продолжаем без данных из receipt
                            }
                        } else {
                            // Валидация успешна (status 0)
                            $environment = $validationResult['environment'] ?? 'unknown';
                            $status = $validationResult['status'] ?? 'unknown';
                            
                            Log::info('Apple receipt validation: Receipt is valid', [
                                'user_id' => $user->id,
                                'subscription_type' => $data['subscription_type'],
                                'environment' => $environment,
                                'status' => $status,
                                'is_sandbox' => $environment === 'Sandbox',
                            ]);
                        }

                        // Extract subscription info from receipt
                        $subscriptionInfo = $validator->extractSubscriptionInfo($validationResult, $productId);

                        if ($subscriptionInfo) {
                            // Use transaction IDs from receipt if available
                            if (!empty($subscriptionInfo['transaction_id'])) {
                                $data['transaction_id'] = $subscriptionInfo['transaction_id'];
                            }
                            if (!empty($subscriptionInfo['original_transaction_id'])) {
                                $data['original_transaction_id'] = $subscriptionInfo['original_transaction_id'];
                            }

                            // Calculate expiration date from receipt
                            if (!empty($subscriptionInfo['expires_date_ms'])) {
                                $expiresAt = \Carbon\Carbon::createFromTimestampMs($subscriptionInfo['expires_date_ms']);
                            } else {
                                $expiresAt = $subscription->duration_days > 0 
                                    ? now()->addDays($subscription->duration_days) 
                                    : null;
                            }

                            Log::info('Apple receipt validated successfully', [
                                'user_id' => $user->id,
                                'product_id' => $productId,
                                'transaction_id' => $data['transaction_id'],
                                'expires_at' => $expiresAt?->toIso8601String(),
                            ]);
                        } else {
                            Log::warning('Could not extract subscription info from receipt', [
                                'user_id' => $user->id,
                                'product_id' => $productId,
                            ]);
                            // Fallback to default expiration
                            $expiresAt = $subscription->duration_days > 0 
                                ? now()->addDays($subscription->duration_days) 
                                : null;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Apple receipt validation exception', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    // In production, we should fail on validation errors
                    if (config('app.env') === 'production') {
                        return response()->json([
                            'success' => false,
                            'message' => 'Receipt validation error',
                            'error' => 'Failed to validate receipt',
                        ], 400);
                    }
                }
            }
        }

        // Set expiration date (from receipt validation or default)
        $expiresAt = $expiresAt ?? ($subscription->duration_days > 0 
            ? now()->addDays($subscription->duration_days) 
            : null);

        Log::info('Creating subscription for user', [
            'user_id' => $user->id,
            'subscription_type' => $data['subscription_type'],
            'expires_at' => $expiresAt?->toIso8601String(),
            'transaction_id' => $data['transaction_id'],
            'has_receipt_data' => !empty($data['receipt_data']),
        ]);

        // Деактивируем все предыдущие подписки пользователя
        UserSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'cancelled_at' => now()]);

        // Создаем новую подписку
        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'starts_at' => now(),
            'expires_at' => $expiresAt,
            'is_active' => true,
            'platform' => $data['platform'],
            'transaction_id' => $data['transaction_id'],
            'original_transaction_id' => $data['original_transaction_id'] ?? null,
            'receipt_data' => $data['receipt_data'] ?? null,
        ]);

        Log::info('UserSubscription created', [
            'user_subscription_id' => $userSubscription->id,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'is_active' => $userSubscription->is_active,
        ]);

        // Обновляем поля в таблице users
        $oldPlanType = $user->plan_type;
        
        $user->update([
            'plan_type' => $subscription->name,
            'subscription_expires_at' => $userSubscription->expires_at,
            'platform' => $data['platform'],
            'transaction_id' => $data['transaction_id'],
        ]);

        // Обновляем объект пользователя для проверки
        $user->refresh();

        Log::info('User subscription status updated', [
            'user_id' => $user->id,
            'old_plan_type' => $oldPlanType,
            'new_plan_type' => $subscription->name,
            'actual_plan_type_in_db' => $user->plan_type,
            'subscription_expires_at' => $userSubscription->expires_at?->toIso8601String(),
            'subscription_id' => $subscription->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription activated successfully',
            'data' => [
                'subscription' => $userSubscription->load('subscription'),
                'user' => $user->fresh(),
            ],
        ]);
    }

    /**
     * Cancel user's subscription
     */
    public function cancel(Request $request): JsonResponse
    {
        $user = $request->user();

        // Находим активную подписку
        $activeSubscription = $user->currentSubscription();

        if (!$activeSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 404);
        }

        // Отменяем подписку
        $activeSubscription->cancel();

        // Возвращаем пользователя на FREE план
        $user->update([
            'plan_type' => 'free',
            'subscription_expires_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully',
        ]);
    }

    /**
     * Restore purchases (for iOS/Android)
     */
    public function restore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|in:ios,android',
            'original_transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $data = $validator->validated();

        // Ищем подписку по original_transaction_id
        $subscription = UserSubscription::where('original_transaction_id', $data['original_transaction_id'])
            ->where('user_id', $user->id)
            ->with('subscription')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No subscription found to restore',
            ], 404);
        }

        // Если подписка еще активна или можно продлить
        if ($subscription->expires_at && $subscription->expires_at->isFuture()) {
            // Активируем подписку
            $subscription->update(['is_active' => true, 'cancelled_at' => null]);

            // Обновляем пользователя
            $user->update([
                'plan_type' => $subscription->subscription->name,
                'subscription_expires_at' => $subscription->expires_at,
                'platform' => $data['platform'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription restored successfully',
                'data' => [
                    'subscription' => $subscription,
                    'user' => $user->fresh(),
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Subscription has expired and cannot be restored',
        ], 400);
    }

    /**
     * Get subscription features
     */
    public function features(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'plan_type' => $user->plan_type,
                'features' => $user->getPlanFeatures(),
                'limits' => [
                    'max_vehicles' => $user->getMaxVehicles(),
                    'max_reminders' => $user->getMaxReminders(),
                    'current_vehicles' => $user->vehicles()->count(),
                    'current_reminders' => $user->reminders()->count(),
                ],
                'access' => [
                    'can_add_vehicle' => $user->canAddVehicle(),
                    'can_add_reminder' => $user->canAddReminder(),
                    'photo_documents' => $user->isPro(),
                    'receipt_photos' => $user->isPro(),
                    'pdf_export' => $user->isPro(),
                    'expense_reminders' => $user->isPro(),
                ],
            ],
        ]);
    }
}
