<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Console\Command;

/**
 * Восстанавливает записи user_subscriptions для пользователей с plan_type pro/premium,
 * у которых нет ни одной связанной строки (типично после ручного UPDATE users или очистки таблицы).
 */
class BackfillUserSubscriptionsFromUsers extends Command
{
    protected $signature = 'subscriptions:backfill-from-users {--dry-run : Только вывести план, без записи в БД}';

    protected $description = 'Создаёт строки user_subscriptions по данным users (pro/premium без истории подписок)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $users = User::query()
            ->whereIn('plan_type', ['pro', 'premium'])
            ->whereDoesntHave('subscriptions')
            ->orderBy('id')
            ->get();

        if ($users->isEmpty()) {
            $this->info('Нечего делать: нет пользователей pro/premium без записей в user_subscriptions.');
            return self::SUCCESS;
        }

        $this->warn('Найдено пользователей без user_subscriptions: '.$users->count());

        foreach ($users as $user) {
            $plan = Subscription::query()->where('name', $user->plan_type)->first();
            if (! $plan) {
                $this->error("User #{$user->id}: в таблице subscriptions нет name=\"{$user->plan_type}\" — пропуск.");

                continue;
            }

            $expiresAt = $user->subscription_expires_at;
            $isActive =
                $expiresAt === null
                || $expiresAt->isFuture();

            $payload = [
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'starts_at' => now(),
                'expires_at' => $expiresAt,
                'is_active' => $isActive,
                'platform' => $user->platform ?? 'ios',
                'transaction_id' => $user->transaction_id ?: 'backfill:'.$user->id,
                'original_transaction_id' => $user->transaction_id,
                'receipt_data' => null,
                'cancelled_at' => $isActive ? null : now(),
            ];

            $this->line(sprintf(
                'User #%d (%s) plan=%s expires=%s active=%s',
                $user->id,
                $user->email ?? '—',
                $user->plan_type,
                $expiresAt ? $expiresAt->toIso8601String() : 'null',
                $isActive ? 'yes' : 'no'
            ));

            if ($dryRun) {
                continue;
            }

            UserSubscription::query()->create($payload);
            $this->info('  → создана запись user_subscriptions.');
        }

        if ($dryRun) {
            $this->warn('Режим --dry-run: в БД ничего не записано. Запустите без флага для записи.');
        }

        return self::SUCCESS;
    }
}
