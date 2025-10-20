<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - myGarage</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
        }
        .code-container {
            background-color: #f9fafb;
            border: 2px dashed #dc2626;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #dc2626;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .message {
            color: #666;
            text-align: center;
            margin: 20px 0;
        }
        .warning {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 12px 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #991b1b;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🚗 myGarage</div>
            <h2 style="margin: 10px 0; color: #1f2937;">Сброс пароля</h2>
        </div>

        <p>Здравствуйте!</p>
        
        <p>Вы запросили сброс пароля для вашего аккаунта в myGarage.</p>

        <div class="code-container">
            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">Ваш код для сброса пароля:</p>
            <div class="code">{{ $code }}</div>
        </div>

        <p class="message">
            Введите этот код в приложении для установки нового пароля.
        </p>

        <div class="warning">
            <p><strong>⚠️ Важно:</strong> Код действителен в течение 60 минут. Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.</p>
        </div>

        <div class="footer">
            <p>С уважением,<br>Команда myGarage</p>
            <p style="margin-top: 10px;">Это автоматическое письмо, пожалуйста, не отвечайте на него.</p>
        </div>
    </div>
</body>
</html>

