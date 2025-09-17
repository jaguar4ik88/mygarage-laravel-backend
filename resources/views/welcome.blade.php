<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyGarage - Управление автомобилем стало проще</title>
    <meta name="description" content="MyGarage - современное приложение для управления автомобилем. Отслеживайте расходы, напоминания о ТО, историю сервиса и многое другое.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #b83939;
            --secondary-color: #9898a0;
            --accent-color: #f87272;
            --success-color: #8bfa8b;
            --warning-color: #b83939;
            --danger-color: #b83939;
            --info-color: #6ed6f8;
            --dark-color: #1b1b1f;
            --surface-color: #202128;
            --card-color: #202128;
            --text-color: #FFFFFF;
            --text-secondary: #9898a0;
            --text-muted: #686464;
            --border-color: #37373e;
            --light-color: #f8fafc;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            background-color: var(--dark-color);
            color: var(--text-color);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: var(--text-color);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="300" fill="url(%23a)"/><circle cx="800" cy="800" r="200" fill="url(%23a)"/></svg>');
            opacity: 0.1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .feature-card {
            background: var(--card-color);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.4);
            border-color: var(--accent-color);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stats-section {
            background: var(--surface-color);
            padding: 80px 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 2rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--accent-color);
            display: block;
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--surface-color) 100%);
            color: var(--text-color);
            padding: 80px 0;
        }
        
        .app-preview {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .btn-download {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
        }
        
        .btn-app-store {
            background: var(--primary-color);
            color: var(--text-color);
        }
        
        .btn-google-play {
            background: var(--accent-color);
            color: var(--text-color);
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-color);
        }
        
        .section-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
        }
            </style>
    </head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4">
                        Управление автомобилем стало <span class="text-warning">проще</span>
                    </h1>
                    <p class="lead mb-4">
                        MyGarage - это современное приложение для полного контроля над вашим автомобилем. 
                        Отслеживайте расходы, получайте напоминания о ТО, ведите историю сервиса и многое другое.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="#download" class="btn btn-warning btn-lg btn-download">
                            <i class="bi bi-download"></i>
                            Скачать приложение
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-play-circle"></i>
                            Узнать больше
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-4 text-sm">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span>4.8/5 рейтинг</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill"></i>
                            <span>10,000+ пользователей</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="app-preview">
                        <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                             alt="MyGarage App Preview" 
                             class="img-fluid rounded-3">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Почему выбирают MyGarage?</h2>
                <p class="section-subtitle">
                    Все необходимые инструменты для управления автомобилем в одном приложении
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--success-color), #6ed6f8);">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Учет расходов</h4>
                        <p style="color: var(--text-secondary);">
                            Ведите детальный учет всех расходов на автомобиль: топливо, ремонт, 
                            страховка, налоги. Получайте аналитику и статистику по тратам.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--accent-color), var(--primary-color));">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Умные напоминания</h4>
                        <p style="color: var(--text-secondary);">
                            Настройте напоминания о ТО, замене масла, техосмотре и других важных 
                            процедурах. Никогда не пропустите важное обслуживание.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--warning-color), var(--accent-color));">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">История сервиса</h4>
                        <p style="color: var(--text-secondary);">
                            Ведите полную историю всех работ и ремонтов. Сохраняйте чеки, 
                            фотографии и заметки о проведенных работах.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--info-color), var(--accent-color));">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Управление автопарком</h4>
                        <p style="color: var(--text-secondary);">
                            Добавляйте несколько автомобилей, отслеживайте пробег, 
                            ведите отдельную статистику для каждого авто.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--danger-color), var(--primary-color));">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Поиск СТО</h4>
                        <p style="color: var(--text-secondary);">
                            Найдите ближайшие станции техобслуживания, сохраняйте 
                            избранные СТО, читайте отзывы и рейтинги.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--success-color), var(--info-color));">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Аналитика и отчеты</h4>
                        <p style="color: var(--text-secondary);">
                            Получайте детальные отчеты о расходах, графики потребления топлива, 
                            прогнозы на будущие траты и рекомендации по экономии.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">10,000+</span>
                        <p style="color: var(--text-secondary);">Активных пользователей</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">50,000+</span>
                        <p style="color: var(--text-secondary);">Записей о расходах</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">15,000+</span>
                        <p style="color: var(--text-secondary);">Напоминаний создано</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">4.8/5</span>
                        <p style="color: var(--text-secondary);">Средний рейтинг</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Screenshots -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Посмотрите, как это работает</h2>
                <p class="section-subtitle">
                    Интуитивный интерфейс и все необходимые функции в одном месте
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="app-preview mb-3">
                            <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" 
                                 alt="Dashboard" class="img-fluid rounded-3">
                        </div>
                        <h5 class="fw-bold" style="color: var(--text-color);">Главная панель</h5>
                        <p style="color: var(--text-secondary);">Обзор всех ваших автомобилей и важной информации</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="app-preview mb-3">
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" 
                                 alt="Expenses" class="img-fluid rounded-3">
                        </div>
                        <h5 class="fw-bold" style="color: var(--text-color);">Учет расходов</h5>
                        <p style="color: var(--text-secondary);">Детальная статистика и анализ ваших трат</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="app-preview mb-3">
                            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80" 
                                 alt="Analytics" class="img-fluid rounded-3">
                        </div>
                        <h5 class="fw-bold" style="color: var(--text-color);">Аналитика</h5>
                        <p style="color: var(--text-secondary);">Графики и отчеты для принятия решений</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="download" class="cta-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-5 fw-bold mb-4">
                        Готовы начать управлять автомобилем по-новому?
                    </h2>
                    <p class="lead mb-4">
                        Скачайте MyGarage прямо сейчас и получите полный контроль над 
                        расходами и обслуживанием вашего автомобиля.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#" class="btn btn-light btn-lg btn-download btn-app-store">
                            <i class="bi bi-apple"></i>
                            <div class="d-flex flex-column align-items-start">
                                <small>Download on the</small>
                                <strong>App Store</strong>
                            </div>
                        </a>
                        <a href="#" class="btn btn-light btn-lg btn-download btn-google-play">
                            <i class="bi bi-google-play"></i>
                            <div class="d-flex flex-column align-items-start">
                                <small>GET IT ON</small>
                                <strong>Google Play</strong>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="app-preview">
                        <img src="https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                             alt="MyGarage Mobile App" 
                             class="img-fluid rounded-3">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: var(--dark-color); color: var(--text-color);" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3" style="color: var(--accent-color);">MyGarage</h5>
                    <p style="color: var(--text-secondary);">
                        Современное приложение для управления автомобилем. 
                        Сделайте уход за автомобилем простым и эффективным.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-twitter fs-4"></i></a>
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-linkedin fs-4"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--text-color);">Приложение</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Функции</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Цены</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Скачать</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Обновления</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--text-color);">Поддержка</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Помощь</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">FAQ</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Контакты</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Сообщество</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--text-color);">Компания</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">О нас</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Карьера</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Пресса</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Блог</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3" style="color: var(--text-color);">Правовая информация</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Условия использования</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Политика конфиденциальности</a></li>
                        <li><a href="#" style="color: var(--text-secondary);" class="text-decoration-none">Cookies</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: var(--border-color);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p style="color: var(--text-secondary);" class="mb-0">&copy; 2024 MyGarage. Все права защищены.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p style="color: var(--text-secondary);" class="mb-0">Сделано с ❤️ для автолюбителей</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Smooth scrolling -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    </body>
</html>
