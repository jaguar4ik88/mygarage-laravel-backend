# API Setup Guide

## Google Places API Configuration

### 1. Получение API ключа
1. Перейдите на [Google Cloud Console](https://console.cloud.google.com/)
2. Создайте новый проект или выберите существующий
3. Включите **Places API** и **Maps JavaScript API**
4. Создайте API ключ в разделе "Credentials"
5. Ограничьте ключ по IP адресам или доменам для безопасности

### 2. Настройка в Laravel
Добавьте в файл `.env`:
```env
GOOGLE_PLACES_API_KEY=your_google_places_api_key_here
GOOGLE_PLACES_BASE_URL=https://maps.googleapis.com/maps/api/place/nearbysearch/json
```

### 3. Overpass API (Fallback)
Overpass API работает без ключа и используется как fallback:
```env
OVERPASS_API_URL=https://overpass-api.de/api/interpreter
```

## Приоритет использования API

1. **Google Places API** - используется если настроен `GOOGLE_PLACES_API_KEY`
2. **Overpass API** - используется как fallback или если Google API недоступен

## Функциональность

### Google Places API
- ✅ Рейтинги и отзывы
- ✅ Часы работы
- ✅ Ценовые категории
- ✅ Фотографии
- ✅ Детальная информация
- ✅ Радиус до 50km

### Overpass API
- ✅ Бесплатный сервис
- ✅ Данные OpenStreetMap
- ✅ Радиус до 50km
- ✅ Телефоны и адреса
- ✅ Типы сервисов
- ❌ Нет рейтингов
- ❌ Нет фотографий

## Логирование

Все запросы к API логируются в Laravel logs:
- `storage/logs/laravel.log`
- Поиск по тегам: `Google Places API`, `Overpass API`
