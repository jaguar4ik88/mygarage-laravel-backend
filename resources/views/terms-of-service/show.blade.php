<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Service - {{ strtoupper($locale) }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Figtree, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 0; padding: 24px; background: #f8fafc; color: #111827; }
        .container { max-width: 860px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 24px 28px; }
        h1 { font-size: 28px; margin: 0 0 16px; }
        .locale { color: #6b7280; font-size: 14px; margin-bottom: 24px; }
        .section { padding: 16px 0; border-top: 1px solid #e5e7eb; }
        .section:first-of-type { border-top: 0; }
        .section code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; color: #374151; }
        .section h2 { margin: 6px 0 8px; font-size: 18px; }
        .section pre { white-space: pre-wrap; word-wrap: break-word; font-family: inherit; margin: 0; }
    </style>
    </head>
<body>
    <div class="container">
        <h1>Terms of Service</h1>
        <div class="locale">Locale: <strong>{{ strtoupper($locale) }}</strong></div>

        @foreach($sections as $section)
            <div class="section">
                <h2>{{ $section['title'] }}</h2>
                <pre>{{ $section['content'] }}</pre>
            </div>
        @endforeach
    </div>
</body>
</html>

