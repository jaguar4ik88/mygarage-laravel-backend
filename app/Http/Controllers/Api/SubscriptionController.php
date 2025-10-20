<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        // В реальном приложении здесь должна быть верификация чека с Apple/Google
        // Для простоты пропускаем верификацию и сразу активируем подписку

        // Деактивируем все предыдущие подписки пользователя
        UserSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'cancelled_at' => now()]);

        // Создаем новую подписку
        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'starts_at' => now(),
            'expires_at' => $subscription->duration_days > 0 ? now()->addDays($subscription->duration_days) : null,
            'is_active' => true,
            'platform' => $data['platform'],
            'transaction_id' => $data['transaction_id'],
            'original_transaction_id' => $data['original_transaction_id'] ?? null,
            'receipt_data' => $data['receipt_data'] ?? null,
        ]);

        // Обновляем поля в таблице users
        $user->update([
            'plan_type' => $subscription->name,
            'subscription_expires_at' => $userSubscription->expires_at,
            'platform' => $data['platform'],
            'transaction_id' => $data['transaction_id'],
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
