# 🚀 Быстрый тест системы подписок

## Шаг 1: Запустите сервер

```bash
cd /Users/alexg/alexg-service/myGarage/backend
php artisan serve
```

Оставьте этот терминал открытым!

---

## Шаг 2: Откройте новый терминал и запустите тесты

### Вариант A: Автоматический тест-скрипт (рекомендуется)

```bash
cd /Users/alexg/alexg-service/myGarage/backend
./test_subscription_api.sh
```

### Вариант B: Ручные тесты через curl

#### 1. Получить список подписок (публичный endpoint):
```bash
curl http://localhost:8000/api/subscriptions | jq
```

**Ожидаемый результат:**
```json
{
  "success": true,
  "data": [
    {
      "name": "free",
      "display_name": "Free",
      "price": 0,
      "max_vehicles": 1,
      "max_reminders": 5
    },
    {
      "name": "pro",
      "display_name": "PRO",
      "price": 499,
      "max_vehicles": 3,
      "max_reminders": null
    }
  ]
}
```

#### 2. Создать тестового пользователя:
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }' | jq
```

#### 3. Залогиниться и получить токен:
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }' | jq -r '.data.token // .token')

echo "Token: $TOKEN"
```

#### 4. Проверить текущую подписку:
```bash
curl http://localhost:8000/api/user/subscription \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Ожидается:** `plan_type: "free"`

#### 5. Попробовать добавить 2 автомобиля (второй должен вернуть ошибку):
```bash
# Первый - должен пройти
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Toyota",
    "model": "Camry",
    "year": 2020,
    "mileage": 50000
  }' | jq

# Второй - должна быть ошибка лимита
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Honda",
    "model": "Accord",
    "year": 2021,
    "mileage": 30000
  }' | jq
```

**Ожидается для второго:**
```json
{
  "success": false,
  "message": "You have reached the maximum number of vehicles (1) for your free plan",
  "upgrade_required": true,
  "limit_reached": true
}
```

#### 6. Купить PRO подписку:
```bash
curl -X POST http://localhost:8000/api/user/subscription/verify \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "platform": "ios",
    "transaction_id": "test_123",
    "subscription_type": "pro"
  }' | jq
```

#### 7. Проверить что план изменился на PRO:
```bash
curl http://localhost:8000/api/user/subscription \
  -H "Authorization: Bearer $TOKEN" | jq '.data.plan_type'
```

**Ожидается:** `"pro"`

#### 8. Теперь попробовать добавить второй автомобиль (должно пройти):
```bash
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Honda",
    "model": "Accord",
    "year": 2021,
    "mileage": 30000
  }' | jq
```

**Ожидается:** `"success": true`

---

## Вариант C: Через tinker (для проверки данных в БД)

```bash
cd /Users/alexg/alexg-service/myGarage/backend
php artisan tinker
```

```php
// Проверить подписки
\App\Models\Subscription::all();

// Создать пользователя
$user = \App\Models\User::factory()->create([
    'email' => 'test2@example.com',
    'plan_type' => 'free'
]);

// Проверить лимиты
$user->getMaxVehicles(); // должно быть 1
$user->getMaxReminders(); // должно быть 5
$user->canAddVehicle(); // true если 0 авто
$user->isPro(); // false

// Дать PRO
$user->update(['plan_type' => 'pro', 'subscription_expires_at' => now()->addMonth()]);

// Проверить снова
$user->getMaxVehicles(); // должно быть 3
$user->getMaxReminders(); // должно быть null (безлимит)
$user->isPro(); // true
```

---

## ✅ Чеклист тестирования

- [ ] Сервер запущен на http://localhost:8000
- [ ] `GET /api/subscriptions` возвращает 3 подписки
- [ ] Регистрация создает пользователя с FREE планом
- [ ] FREE план: лимит 1 автомобиль работает
- [ ] FREE план: лимит 5 напоминаний работает
- [ ] Покупка PRO активирует подписку
- [ ] PRO план: можно добавить до 3 автомобилей
- [ ] PRO план: безлимит напоминаний
- [ ] PRO план: доступ к документам (403 для FREE)
- [ ] Отмена подписки возвращает на FREE

---

## Troubleshooting

### Если роуты не работают:
```bash
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

### Если нет подписок в БД:
```bash
php artisan db:seed --class=SubscriptionSeeder
```

### Если ошибки миграций:
```bash
php artisan migrate
```

### Полный сброс (для чистого теста):
```bash
php artisan migrate:fresh --seed
# Это удалит ВСЕ данные и создаст заново!
```

