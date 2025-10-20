# ✅ Система подписок готова к тестированию!

## 📊 Что уже работает:

### Backend:
✅ База данных (5 миграций выполнено)
✅ Модели (Subscription, UserSubscription, VehicleDocument)
✅ 3 подписки в БД (FREE, PRO $4.99, PREMIUM в разработке)
✅ 6 API роутов для подписок
✅ Контроллеры (Subscription, VehicleDocument)
✅ Проверка лимитов в Vehicle и Reminder контроллерах
✅ Хранение файлов (документы автомобилей)

### Лимиты:
- **FREE**: 1 авто, 5 напоминаний
- **PRO**: 3 авто, безлимит напоминаний + документы + чеки + PDF

---

## 🚀 Как запустить тест

### Терминал 1: Запустите сервер
```bash
cd /Users/alexg/alexg-service/myGarage/backend
php artisan serve
```

### Терминал 2: Запустите тесты

**Автоматический тест (рекомендую!):**
```bash
cd /Users/alexg/alexg-service/myGarage/backend
./test_subscription_api.sh
```

**Или ручной быстрый тест:**
```bash
# 1. Проверить подписки
curl http://localhost:8000/api/subscriptions | jq

# 2. Создать пользователя
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123"}' | jq

# 3. Логин
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}' | jq -r '.data.token // .token')

echo "Токен: $TOKEN"

# 4. Проверить текущий план (должен быть FREE)
curl http://localhost:8000/api/user/subscription \
  -H "Authorization: Bearer $TOKEN" | jq '.data.plan_type'

# 5. Попробовать добавить 2 авто (второй должен вернуть ошибку лимита)
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"make":"Toyota","model":"Camry","year":2020,"mileage":50000}' | jq

curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"make":"Honda","model":"Accord","year":2021,"mileage":30000}' | jq

# 6. Купить PRO
curl -X POST http://localhost:8000/api/user/subscription/verify \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"platform":"ios","transaction_id":"test_123","subscription_type":"pro"}' | jq

# 7. Проверить что стал PRO
curl http://localhost:8000/api/user/subscription \
  -H "Authorization: Bearer $TOKEN" | jq '.data.plan_type'

# 8. Теперь второй авто должен добавиться
curl -X POST http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"make":"Honda","model":"Accord","year":2021,"mileage":30000}' | jq
```

---

## 📚 Файлы для справки

1. **test_subscription_api.sh** - Автоматический тест-скрипт
2. **TESTING_GUIDE.md** - Подробное руководство по тестированию
3. **quick_test.md** - Быстрые команды для теста
4. **START_TESTING.md** (этот файл) - Стартовая точка

---

## 🔍 Проверка через tinker

```bash
cd /Users/alexg/alexg-service/myGarage/backend
php artisan tinker
```

```php
// Посмотреть подписки
\App\Models\Subscription::all();

// Создать тестового пользователя
$user = \App\Models\User::factory()->create(['plan_type' => 'free']);

// Проверить лимиты
$user->getMaxVehicles();      // 1
$user->getMaxReminders();     // 5
$user->canAddVehicle();       // true/false
$user->isPro();               // false

// Дать PRO на месяц
$user->update([
    'plan_type' => 'pro',
    'subscription_expires_at' => now()->addMonth()
]);

// Проверить снова
$user->getMaxVehicles();      // 3
$user->getMaxReminders();     // null (безлимит)
$user->isPro();               // true
```

---

## ✅ Чеклист тестирования

### Базовое:
- [ ] `GET /api/subscriptions` возвращает 3 подписки
- [ ] Новый пользователь имеет FREE план
- [ ] FREE: лимит 1 авто работает (2-й возвращает 403)
- [ ] FREE: лимит 5 напоминаний работает (6-е возвращает 403)

### PRO функции:
- [ ] Покупка PRO активирует подписку
- [ ] PRO: можно добавить до 3 авто
- [ ] PRO: безлимит напоминаний
- [ ] PRO: можно загружать документы авто
- [ ] FREE: нельзя загружать документы (403)

### Управление:
- [ ] Отмена подписки возвращает на FREE
- [ ] Восстановление покупок работает

---

## 🐛 Если что-то не работает

```bash
# Очистить кеш
php artisan route:clear
php artisan cache:clear
php artisan config:clear

# Пересоздать подписки
php artisan db:seed --class=SubscriptionSeeder

# Полный сброс БД (ОСТОРОЖНО!)
# php artisan migrate:fresh --seed
```

---

## 📋 Следующие шаги

После тестирования backend можно приступать к:
1. 📱 Mobile - Интеграция RevenueCat
2. 🎨 Mobile - UI экранов подписок
3. 📸 Mobile - Реализация PRO функций (документы, чеки, PDF)

---

**Готово к тесту! 🚀**
Запускайте сервер и тестируйте!

