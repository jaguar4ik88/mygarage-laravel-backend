#!/bin/bash

echo "🔍 Проверка настроек файловой системы для myGarage..."

# Проверяем существование папки storage
if [ ! -d "storage/app/public" ]; then
    echo "❌ Папка storage/app/public не существует"
    echo "📁 Создаем папку..."
    mkdir -p storage/app/public
fi

# Проверяем права доступа
echo "🔐 Проверяем права доступа..."
chmod -R 755 storage/
chown -R www-data:www-data storage/ 2>/dev/null || echo "⚠️  Не удалось изменить владельца (возможно, не запущено от root)"

# Проверяем симлинк
if [ ! -L "public/storage" ]; then
    echo "🔗 Симлинк public/storage не существует"
    echo "🔗 Создаем симлинк..."
    php artisan storage:link
else
    echo "✅ Симлинк public/storage существует"
fi

# Проверяем папку для чеков
if [ ! -d "storage/app/public/receipts" ]; then
    echo "📁 Создаем папку для чеков..."
    mkdir -p storage/app/public/receipts
    chmod 755 storage/app/public/receipts
fi

echo "✅ Проверка завершена!"
echo ""
echo "📋 Следующие шаги:"
echo "1. Убедитесь, что веб-сервер имеет доступ к папке storage/"
echo "2. Проверьте, что симлинк public/storage работает"
echo "3. Протестируйте загрузку файлов через API"
echo ""
echo "🔗 URL для тестирования:"
echo "   https://yourdomain.com/storage/receipts/[user_id]/[filename]"
echo "   https://yourdomain.com/api/expenses/[expense_id]/receipt"
