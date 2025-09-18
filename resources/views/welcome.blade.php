<!DOCTYPE html>
<html lang="uk">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyGarage - Керування автомобілем стало простішим</title>
    <meta name="description" content="MyGarage - сучасний застосунок для керування автомобілем. Відстежуйте витрати, нагадування про ТО, історію сервісу та багато іншого.">
    
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
            color: var(--text-color);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
            background: #202129;
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
        .feature-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
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
                        Керування автомобілем стало <span class="text-warning">простішим</span>
                    </h1>
                    <p class="lead mb-4">
                        MyGarage — це сучасний застосунок для повного контролю над вашим автомобілем. 
                        Відстежуйте витрати, отримуйте нагадування про ТО, ведіть історію сервісу тощо.
                    </p>
                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="#download" class="btn btn-warning btn-lg btn-download">
                            <i class="bi bi-download"></i>
                            Завантажити застосунок
                        </a>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-play-circle"></i>
                            Дізнатися більше
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-4 text-sm">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span>4.8/5 рейтинг</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-people-fill"></i>
                            <span>10,000+ користувачів</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="app-preview">
                        <img src="{{ asset('images/main.png') }}" 
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
                <h2 class="section-title">Чому обирають MyGarage?</h2>
                <p class="section-subtitle">
                    Усі необхідні інструменти для керування автомобілем в одному застосунку
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--success-color), #6ed6f8);">
                            <i class="bi bi-calculator"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Облік витрат</h4>
                        <p style="color: var(--text-secondary);">
                            Ведіть детальний облік усіх витрат на авто: пальне, ремонт, 
                            страхування, податки. Отримуйте аналітику та статистику витрат.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--accent-color), var(--primary-color));">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Розумні нагадування</h4>
                        <p style="color: var(--text-secondary);">
                            Налаштуйте нагадування про ТО, заміну масла, техогляд та інші важливі 
                            процедури. Ніколи не пропускайте важливе обслуговування.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--warning-color), var(--accent-color));">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Історія сервісу</h4>
                        <p style="color: var(--text-secondary);">
                            Ведіть повну історію всіх робіт і ремонтів. Зберігайте чеки, 
                            фото та нотатки про виконані роботи.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--info-color), var(--accent-color));">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Керування автопарком</h4>
                        <p style="color: var(--text-secondary);">
                            Додавайте кілька автомобілів, відстежуйте пробіг, 
                            ведіть окрему статистику для кожного авто.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--danger-color), var(--primary-color));">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Пошук СТО</h4>
                        <p style="color: var(--text-secondary);">
                            Знаходьте найближчі станції техобслуговування, зберігайте 
                            обрані СТО, читайте відгуки та рейтинги.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, var(--success-color), var(--info-color));">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4 class="fw-bold mb-3" style="color: var(--text-color);">Аналітика та звіти</h4>
                        <p style="color: var(--text-secondary);">
                            Отримуйте детальні звіти про витрати, графіки споживання пального, 
                            прогнози майбутніх витрат та рекомендації щодо економії.
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
                        <p style="color: var(--text-secondary);">Активних користувачів</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">50,000+</span>
                        <p style="color: var(--text-secondary);">Записів про витрати</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">15,000+</span>
                        <p style="color: var(--text-secondary);">Створено нагадувань</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <span class="stat-number">4.8/5</span>
                        <p style="color: var(--text-secondary);">Середній рейтинг</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Screenshots -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Подивіться, як це працює</h2>
                <p class="section-subtitle">
                    Інтуїтивний інтерфейс і всі необхідні функції в одному місці
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="app-preview mb-3">
                            <img src="{{ asset('images/screenshots/main.png') }}" 
                                 alt="Dashboard" class="img-fluid rounded-3" loading="lazy">
                        </div>
                        <h5 class="fw-bold" style="color: var(--text-color);">Головна панель</h5>
                        <p style="color: var(--text-secondary);">Огляд усіх ваших автомобілів і важливої інформації</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="app-preview mb-3">
                            <img src="{{ asset('images/screenshots/istor.png') }}" 
                                 alt="Expenses" class="img-fluid rounded-3" loading="lazy">
                        </div>
                        <h5 class="fw-bold" style="color: var(--text-color);">Облік витрат</h5>
                        <p style="color: var(--text-secondary);">Детальна статистика та аналіз ваших витрат</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="app-preview mb-3">
                            <img src="{{ asset('images/screenshots/sto.png') }}" 
                                 alt="STO" class="img-fluid rounded-3" loading="lazy">
                        </div>
                        <h5 class="fw-bold" style="color: var(--text-color);">Твої СТО</h5>
                        <p style="color: var(--text-secondary);">Зберігай улюблені СТО</p>
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
                        Готові почати керувати автомобілем по-новому?
                    </h2>
                    <p class="lead mb-4">
                        Завантажте MyGarage просто зараз і отримайте повний контроль над 
                        витратами та обслуговуванням вашого автомобіля.
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
                        <img src="{{ asset('images/image.png') }}" 
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
                        Сучасний застосунок для керування автомобілем. 
                        Зробіть догляд за авто простим і ефективним.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-twitter fs-4"></i></a>
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" style="color: var(--text-secondary);"><i class="bi bi-linkedin fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="border-color: var(--border-color);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p style="color: var(--text-secondary);" class="mb-0">&copy; 2024 MyGarage. Усі права захищені.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p style="color: var(--text-secondary);" class="mb-0">Зроблено з ❤️ для автолюбителів</p>
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
