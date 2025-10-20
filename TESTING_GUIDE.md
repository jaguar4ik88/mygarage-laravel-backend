# 🧪 Руководство по тестированию системы подписок

## Быстрый запуск

### 1. Убедитесь, что сервер запущен

```bash
cd /Users/alexg/alexg-service/myGarage/backend
php artisan serve
# Сервер должен быть доступен на http://localhost:8000
```

### 2. Запустите тест-скрипт

```bash
cd /Users/alexg/alexg-service/myGarage/backend
./test_subscription_api.sh
```

Скрипт автоматически проверит:
- ✅ Регистрацию и логин
- ✅ Получение списка подписок
- ✅ Текущую подписку пользователя (FREE по умолчанию)
- ✅ Лимиты (1 авто, 5 напоминаний для FREE)
- ✅ Попытку превысить лимиты
- ✅ Покупку PRO подписки
- ✅ Увеличенные лимиты PRO (3 авто, безлимит напоминаний)
- ✅ Отмену подписки

---

## Ручное тестирование через curl

### 1. Регистрация пользователя

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### 2. Логин и получение токена

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Сохраните TOKEN из ответа!**

### 3. Проверка доступных подписок

```bash
# Публичный endpoint - токен не нужен
curl -X GET http://localhost:8000/api/subscriptions
```

**Ожидаемый результат:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "free",
      "display_name": "Free",
      "price": 0,
      "max_vehicles": 1,
      "max_reminders": 5,
      "features": [...]
    },
    {
      "id": 2,
      "name": "pro",
      "display_name": "PRO",
      "price": 499,
      "max_vehicles": 3,
      "max_reminders": null,
      "features": [...]
    }
  ]
}
```

### 4. Получение текущей подписки

```bash
curl -X GET http://localhost:8000/api/user/subscription \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 5. Проверка лимитов и доступа

```bash
curl -X GET http://localhost:8000/api/user/subscription/features \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Ожидаемый результат для FREE:**
```json
{
  "success": true,
  "data": {
    "plan_type": "free",
    "limits": {
      "max_vehicles": 1,
      "max_reminders": 5,
      "current_vehicles": 0,
      "current_reminders": 0
    },
    "access": {
      "can_add_vehicle": true,
      "can_add_reminder": true,
      "photo_documents": false,
      "receipt_photos": false,
      "pdf_export": false
    }
  }
}
```

### 6. Тестирование лимита автомобилей

#### 6.1 Добавить первый автомобиль (должно сработать)

```bash
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Toyota",
    "model": "Camry",
    "year": 2020,
    "mileage": 50000
  }'
```

#### 6.2 Попытка добавить второй (должна быть ошибка 403)

```bash
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Honda",
    "model": "Accord",
    "year": 2021,
    "mileage": 30000
  }'
```

**Ожидаемый результат:**
```json
{
  "success": false,
  "message": "You have reached the maximum number of vehicles (1) for your free plan",
  "upgrade_required": true,
  "limit_reached": true,
  "max_vehicles": 1,
  "current_plan": "free"
}
```

### 7. Тестирование лимита напоминаний

```bash
# Добавляем 5 напоминаний - должны пройти
for i in {1..5}; do
  curl -X POST http://localhost:8000/api/reminders \
    -H "Authorization: Bearer YOUR_TOKEN" \
    -H "Content-Type: application/json" \
    -d "{
      \"type\": \"maintenance\",
      \"title\": \"Reminder $i\",
      \"description\": \"Test\",
      \"next_service_date\": \"2025-12-01\",
      \"is_active\": true
    }"
done

# 6-е должно вернуть ошибку
curl -X POST http://localhost:8000/api/reminders \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "maintenance",
    "title": "Reminder 6",
    "description": "Test",
    "next_service_date": "2025-12-01",
    "is_active": true
  }'
```

### 8. Покупка PRO подписки

```bash
curl -X POST http://localhost:8000/api/user/subscription/verify \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "platform": "ios",
    "transaction_id": "test_transaction_123",
    "original_transaction_id": "test_original_123",
    "subscription_type": "pro"
  }'
```

**Ожидаемый результат:**
```json
{
  "success": true,
  "message": "Subscription activated successfully",
  "data": {
    "subscription": {...},
    "user": {
      "plan_type": "pro",
      "subscription_expires_at": "2025-11-19 12:00:00"
    }
  }
}
```

### 9. Проверка PRO функций

#### 9.1 Теперь можно добавить до 3 автомобилей

```bash
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Honda",
    "model": "Accord",
    "year": 2021,
    "mileage": 30000
  }'
```

#### 9.2 Загрузка документа автомобиля (только для PRO!)

Сначала создайте тестовый файл:
```bash
echo "Test document" > /tmp/test_doc.pdf
```

Затем загрузите:
```bash
curl -X POST http://localhost:8000/api/vehicles/1/documents \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "type=insurance" \
  -F "name=Страховка ОСАГО" \
  -F "file=@/tmp/test_doc.pdf" \
  -F "expiry_date=2026-12-31" \
  -F "notes=Тестовая страховка"
```

#### 9.3 Получение списка документов

```bash
curl -X GET http://localhost:8000/api/vehicles/1/documents \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 10. Отмена подписки

```bash
curl -X POST http://localhost:8000/api/user/subscription/cancel \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 11. Восстановление покупки

```bash
curl -X POST http://localhost:8000/api/user/subscription/restore \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "platform": "ios",
    "original_transaction_id": "test_original_123"
  }'
```

---

## Тестирование в базе данных

### Проверка данных напрямую

```bash
cd /Users/alexg/alexg-service/myGarage/backend
php artisan tinker
```

```php
// Проверить подписки
\App\Models\Subscription::all();

// Проверить пользователя
$user = \App\Models\User::where('email', 'test@example.com')->first();
$user->plan_type;
$user->getMaxVehicles();
$user->getMaxReminders();
$user->isPro();

// Проверить историю подписок пользователя
$user->subscriptions;

// Проверить активную подписку
$user->currentSubscription();

// Проверить автомобили
$user->vehicles()->count();

// Проверить напоминания
$user->reminders()->count();

// Проверить можно ли добавить авто/напоминания
$user->canAddVehicle();
$user->canAddReminder();
```

---

## Проверка файлов

Документы сохраняются в:
```bash
ls -la /Users/alexg/alexg-service/myGarage/backend/storage/app/documents/
```

Структура:
```
storage/app/documents/
  └── {user_id}/
      └── {vehicle_id}/
          └── {uuid}.pdf
```

---

## Ожидаемое поведение

### FREE план (по умолчанию)
- ✅ 1 автомобиль
- ✅ 5 напоминаний
- ❌ Нет доступа к документам
- ❌ Нет доступа к чекам
- ❌ Нет PDF экспорта

### PRO план ($4.99/мес)
- ✅ До 3 автомобилей
- ✅ Безлимит напоминаний
- ✅ Фото документов
- ✅ Фото чеков
- ✅ PDF экспорт

### PREMIUM план (в разработке)
- 🚧 Показывается в списке, но `is_active: false`
- ❌ Нельзя купить

---

## Troubleshooting

### Ошибка: "No subscriptions found"
```bash
cd backend
php artisan db:seed --class=SubscriptionSeeder
```

### Ошибка: "Table not found"
```bash
cd backend
php artisan migrate
```

### Очистить тестовые данные
```bash
cd backend
php artisan migrate:fresh --seed
```

### Проверить роуты
```bash
cd backend
php artisan route:list | grep subscription
```

---

## Полезные команды

```bash
# Посмотреть все подписки
php artisan tinker
>>> \App\Models\Subscription::all();

# Сбросить план пользователя на FREE
>>> $user = \App\Models\User::find(1);
>>> $user->update(['plan_type' => 'free', 'subscription_expires_at' => null]);

# Дать пользователю PRO на месяц
>>> $user->update(['plan_type' => 'pro', 'subscription_expires_at' => now()->addMonth()]);

# Проверить текущие лимиты
>>> $user->getMaxVehicles();
>>> $user->getMaxReminders();
>>> $user->canAddVehicle();
```

---

## Что тестировать

### ✅ Обязательно проверить:
1. FREE план работает по умолчанию
2. Лимиты FREE плана соблюдаются (1 авто, 5 напоминаний)
3. Покупка PRO активируется корректно
4. Лимиты PRO плана корректны (3 авто, безлимит напоминаний)
5. PRO функции доступны только с PRO планом
6. Отмена подписки возвращает на FREE
7. FREE пользователь не может загружать документы (403)
8. PRO пользователь может загружать документы

### 📋 Дополнительно:
- Восстановление покупок
- Загрузка файлов разных типов (jpg, png, pdf)
- Удаление документов (файл тоже должен удалиться)
- Проверка размера файлов (макс 10MB)

---

## Автоматические тесты (TODO)

Можно создать PHPUnit тесты для автоматизации:
```bash
php artisan make:test SubscriptionTest
php artisan make:test VehicleDocumentTest
```

Запуск тестов:
```bash
php artisan test
```

