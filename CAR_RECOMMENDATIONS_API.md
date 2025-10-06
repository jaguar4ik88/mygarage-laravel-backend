# API для рекомендаций по обслуживанию автомобилей

## Обзор

API предоставляет доступ к рекомендациям по обслуживанию автомобилей и информации о шинах. Включает в себя две основные таблицы:

1. **car_recommendations** - рекомендации по обслуживанию
2. **car_tyres** - рекомендации по шинам

## Рекомендации по обслуживанию (Car Recommendations)

### Публичные endpoints (GET)

#### Получить все рекомендации
```
GET /api/car-recommendations
```

**Параметры запроса:**
- `maker` (string) - фильтр по марке автомобиля
- `model` (string) - фильтр по модели автомобиля  
- `year` (integer) - фильтр по году выпуска
- `mileage` (integer) - фильтр по пробегу (показывает рекомендации для данного пробега и меньше)
- `item` (string) - фильтр по типу обслуживания

#### Получить рекомендации для конкретного автомобиля
```
GET /api/car-recommendations/for-car?maker=Toyota&model=Corolla&year=2020&mileage=15000
```

#### Получить список марок
```
GET /api/car-recommendations/makers
```

#### Получить модели для марки
```
GET /api/car-recommendations/models?maker=Toyota
```

#### Получить типы обслуживания
```
GET /api/car-recommendations/items
```

#### Получить конкретную рекомендацию
```
GET /api/car-recommendations/{id}
```

### Административные endpoints (требуют авторизации)

#### Создать рекомендацию
```
POST /api/car-recommendations
```

**Тело запроса:**
```json
{
    "maker": "Toyota",
    "model": "Corolla", 
    "year": 2020,
    "mileage_interval": 10000,
    "item": "Масло двигателя",
    "recommendation": "Менять масло каждые 10 000 км, использовать 5W-30",
    "manual_section_id": 1
}
```

#### Обновить рекомендацию
```
PUT /api/car-recommendations/{id}
```

#### Удалить рекомендацию
```
DELETE /api/car-recommendations/{id}
```

## Рекомендации по шинам (Car Tyres)

### Публичные endpoints (GET)

#### Получить все рекомендации по шинам
```
GET /api/car-tyres
```

**Параметры запроса:**
- `brand` (string) - фильтр по марке автомобиля
- `model` (string) - фильтр по модели автомобиля
- `year` (integer) - фильтр по году выпуска
- `dimension` (string) - фильтр по размеру шин

#### Получить рекомендации для конкретного автомобиля
```
GET /api/car-tyres/for-car?brand=Toyota&model=Corolla&year=2020
```

#### Получить список марок автомобилей
```
GET /api/car-tyres/brands
```

#### Получить модели для марки
```
GET /api/car-tyres/models?brand=Toyota
```

#### Получить размеры шин
```
GET /api/car-tyres/dimensions
```

#### Получить размеры для конкретного автомобиля
```
GET /api/car-tyres/dimensions-for-car?brand=Toyota&model=Corolla&year=2020
```

#### Получить конкретную рекомендацию
```
GET /api/car-tyres/{id}
```

### Административные endpoints (требуют авторизации)

#### Создать рекомендацию по шинам
```
POST /api/car-tyres
```

**Тело запроса:**
```json
{
    "brand": "Toyota",
    "model": "Corolla",
    "year": 2020,
    "dimension": "205/55 R16",
    "notes": "Можно ставить RunFlat шины"
}
```

#### Обновить рекомендацию
```
PUT /api/car-tyres/{id}
```

#### Удалить рекомендацию
```
DELETE /api/car-tyres/{id}
```

## Структура данных

### CarRecommendation
```json
{
    "id": 1,
    "maker": "Toyota",
    "model": "Corolla",
    "year": 2020,
    "mileage_interval": 10000,
    "item": "Масло двигателя",
    "recommendation": "Менять масло каждые 10 000 км, использовать 5W-30",
    "manual_section_id": 1,
    "created_at": "2025-10-04T14:12:39.000000Z",
    "updated_at": "2025-10-04T14:12:39.000000Z"
}
```

### CarTyre
```json
{
    "id": 1,
    "brand": "Toyota",
    "model": "Corolla", 
    "year": 2020,
    "dimension": "205/55 R16",
    "notes": "Можно ставить RunFlat шины",
    "created_at": "2025-10-04T14:12:42.000000Z",
    "updated_at": "2025-10-04T14:12:42.000000Z"
}
```

## Особенности

1. **Связь с manual_sections**: Рекомендации могут быть связаны с разделами руководства для лучшей организации
2. **Scope методы**: Модели содержат удобные scope методы для фильтрации данных
3. **Индексы**: База данных оптимизирована индексами для быстрого поиска
4. **Авторизация**: Публичные endpoints доступны всем, административные требуют токен авторизации
5. **Валидация**: Все входные данные валидируются перед сохранением

## Примеры использования

### Поиск рекомендаций для Toyota Corolla 2020 года с пробегом 25000 км
```
GET /api/car-recommendations/for-car?maker=Toyota&model=Corolla&year=2020&mileage=25000
```

### Получение всех размеров шин для Honda Civic
```
GET /api/car-tyres/dimensions-for-car?brand=Honda&model=Civic
```

### Поиск рекомендаций по замене масла
```
GET /api/car-recommendations?item=Масло%20двигателя
```
