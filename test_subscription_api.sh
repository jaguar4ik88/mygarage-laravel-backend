#!/bin/bash

# Цвета для вывода
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

API_URL="http://localhost:8000/api"
EMAIL="test@example.com"
PASSWORD="password123"
TOKEN=""

echo -e "${YELLOW}=== Тестирование API системы подписок ===${NC}\n"

# 1. Регистрация пользователя (если нужно)
echo -e "${YELLOW}1. Регистрация тестового пользователя...${NC}"
REGISTER_RESPONSE=$(curl -s -X POST "$API_URL/register" \
  -H "Content-Type: application/json" \
  -d "{
    \"name\": \"Test User\",
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\",
    \"password_confirmation\": \"$PASSWORD\"
  }")

echo "$REGISTER_RESPONSE" | jq '.'

# 2. Логин
echo -e "\n${YELLOW}2. Логин...${NC}"
LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\"
  }")

TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token // .token // empty')

if [ -z "$TOKEN" ]; then
  echo -e "${RED}Ошибка: не удалось получить токен${NC}"
  echo "$LOGIN_RESPONSE" | jq '.'
  exit 1
fi

echo -e "${GREEN}✓ Токен получен: ${TOKEN:0:20}...${NC}"

# 3. Получить список всех подписок (публичный endpoint)
echo -e "\n${YELLOW}3. Получение списка всех доступных подписок...${NC}"
SUBSCRIPTIONS=$(curl -s -X GET "$API_URL/subscriptions")
echo "$SUBSCRIPTIONS" | jq '.'

# 4. Получить текущую подписку пользователя
echo -e "\n${YELLOW}4. Получение текущей подписки пользователя...${NC}"
CURRENT_SUB=$(curl -s -X GET "$API_URL/user/subscription" \
  -H "Authorization: Bearer $TOKEN")
echo "$CURRENT_SUB" | jq '.'

PLAN_TYPE=$(echo "$CURRENT_SUB" | jq -r '.data.plan_type')
echo -e "${GREEN}✓ Текущий план: $PLAN_TYPE${NC}"

# 5. Получить доступные функции
echo -e "\n${YELLOW}5. Получение доступных функций...${NC}"
FEATURES=$(curl -s -X GET "$API_URL/user/subscription/features" \
  -H "Authorization: Bearer $TOKEN")
echo "$FEATURES" | jq '.'

MAX_VEHICLES=$(echo "$FEATURES" | jq -r '.data.limits.max_vehicles')
MAX_REMINDERS=$(echo "$FEATURES" | jq -r '.data.limits.max_reminders')
echo -e "${GREEN}✓ Лимиты: $MAX_VEHICLES авто, $MAX_REMINDERS напоминаний${NC}"

# 6. Попытка добавить автомобиль
echo -e "\n${YELLOW}6. Добавление первого автомобиля...${NC}"
VEHICLE1=$(curl -s -X POST "$API_URL/vehicles" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Toyota",
    "model": "Camry",
    "year": 2020,
    "mileage": 50000,
    "engine_type": "2.5L"
  }')
echo "$VEHICLE1" | jq '.'

if echo "$VEHICLE1" | jq -e '.success == true' > /dev/null; then
  echo -e "${GREEN}✓ Первый автомобиль добавлен${NC}"
else
  echo -e "${RED}✗ Ошибка при добавлении автомобиля${NC}"
fi

# 7. Попытка добавить второй автомобиль (должна быть ошибка для FREE плана)
echo -e "\n${YELLOW}7. Попытка добавить второй автомобиль (должна быть ошибка для FREE)...${NC}"
VEHICLE2=$(curl -s -X POST "$API_URL/vehicles" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Honda",
    "model": "Accord",
    "year": 2021,
    "mileage": 30000
  }')
echo "$VEHICLE2" | jq '.'

if echo "$VEHICLE2" | jq -e '.limit_reached == true' > /dev/null; then
  echo -e "${GREEN}✓ Лимит правильно сработал!${NC}"
else
  echo -e "${RED}✗ Лимит не сработал (или уже PRO план)${NC}"
fi

# 8. Добавить несколько напоминаний
echo -e "\n${YELLOW}8. Добавление напоминаний (максимум 5 для FREE)...${NC}"
for i in {1..6}; do
  REMINDER=$(curl -s -X POST "$API_URL/reminders" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d "{
      \"type\": \"maintenance\",
      \"title\": \"Напоминание $i\",
      \"description\": \"Тестовое напоминание\",
      \"next_service_date\": \"2025-12-01\",
      \"is_active\": true
    }")
  
  if echo "$REMINDER" | jq -e '.success == true' > /dev/null; then
    echo -e "${GREEN}✓ Напоминание $i добавлено${NC}"
  elif echo "$REMINDER" | jq -e '.limit_reached == true' > /dev/null; then
    echo -e "${YELLOW}⚠ Достигнут лимит напоминаний (напоминание $i)${NC}"
    break
  else
    echo -e "${RED}✗ Ошибка при добавлении напоминания $i${NC}"
  fi
done

# 9. Симуляция покупки PRO подписки
echo -e "\n${YELLOW}9. Симуляция покупки PRO подписки...${NC}"
PRO_PURCHASE=$(curl -s -X POST "$API_URL/user/subscription/verify" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "platform": "ios",
    "transaction_id": "test_transaction_12345",
    "original_transaction_id": "test_original_12345",
    "subscription_type": "pro"
  }')
echo "$PRO_PURCHASE" | jq '.'

if echo "$PRO_PURCHASE" | jq -e '.success == true' > /dev/null; then
  echo -e "${GREEN}✓ PRO подписка активирована!${NC}"
fi

# 10. Проверка обновленной подписки
echo -e "\n${YELLOW}10. Проверка обновленной подписки...${NC}"
UPDATED_SUB=$(curl -s -X GET "$API_URL/user/subscription" \
  -H "Authorization: Bearer $TOKEN")
echo "$UPDATED_SUB" | jq '.'

NEW_PLAN=$(echo "$UPDATED_SUB" | jq -r '.data.plan_type')
IS_PRO=$(echo "$UPDATED_SUB" | jq -r '.data.is_pro')
echo -e "${GREEN}✓ Новый план: $NEW_PLAN, PRO активен: $IS_PRO${NC}"

# 11. Попытка добавить второй автомобиль с PRO
echo -e "\n${YELLOW}11. Попытка добавить второй автомобиль с PRO планом...${NC}"
VEHICLE2_PRO=$(curl -s -X POST "$API_URL/vehicles" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "make": "Honda",
    "model": "Accord",
    "year": 2021,
    "mileage": 30000
  }')
echo "$VEHICLE2_PRO" | jq '.'

if echo "$VEHICLE2_PRO" | jq -e '.success == true' > /dev/null; then
  echo -e "${GREEN}✓ Второй автомобиль добавлен с PRO планом!${NC}"
fi

# 12. Попытка загрузить документ (требует PRO)
echo -e "\n${YELLOW}12. Получение списка автомобилей для тестирования документов...${NC}"
VEHICLES=$(curl -s -X GET "$API_URL/vehicles?user_id=1" \
  -H "Authorization: Bearer $TOKEN")

VEHICLE_ID=$(echo "$VEHICLES" | jq -r '.data[0].id // empty')

if [ -n "$VEHICLE_ID" ]; then
  echo -e "${GREEN}✓ Найден автомобиль ID: $VEHICLE_ID${NC}"
  
  echo -e "\n${YELLOW}13. Получение списка документов автомобиля...${NC}"
  DOCS=$(curl -s -X GET "$API_URL/vehicles/$VEHICLE_ID/documents" \
    -H "Authorization: Bearer $TOKEN")
  echo "$DOCS" | jq '.'
fi

# 14. Отмена подписки
echo -e "\n${YELLOW}14. Отмена подписки...${NC}"
CANCEL=$(curl -s -X POST "$API_URL/user/subscription/cancel" \
  -H "Authorization: Bearer $TOKEN")
echo "$CANCEL" | jq '.'

if echo "$CANCEL" | jq -e '.success == true' > /dev/null; then
  echo -e "${GREEN}✓ Подписка отменена${NC}"
fi

# 15. Проверка возврата к FREE плану
echo -e "\n${YELLOW}15. Проверка возврата к FREE плану...${NC}"
FINAL_SUB=$(curl -s -X GET "$API_URL/user/subscription" \
  -H "Authorization: Bearer $TOKEN")
echo "$FINAL_SUB" | jq '.'

FINAL_PLAN=$(echo "$FINAL_SUB" | jq -r '.data.plan_type')
echo -e "${GREEN}✓ Финальный план: $FINAL_PLAN${NC}"

echo -e "\n${GREEN}=== Тестирование завершено ===${NC}"

