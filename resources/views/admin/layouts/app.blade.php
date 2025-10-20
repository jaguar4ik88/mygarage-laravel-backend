<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Админка') - MyGarage</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: #495057;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #007bff;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 1rem;
        }
        
        /* Custom pagination styles */
        .pagination .page-link {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            margin-left: -1px;
            line-height: 1.25;
            color: #6c757d;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .pagination .page-link:hover {
            z-index: 2;
            color: #495057;
            background-color: #e9ecef;
            border-color: #dee2e6;
        }
        
        .pagination .page-link:focus {
            z-index: 3;
            color: #495057;
            background-color: #e9ecef;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .pagination .page-item:first-child .page-link {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }
        
        .pagination .page-item:last-child .page-link {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }
        
        .pagination .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        .pagination .page-link svg {
            width: 16px;
            height: 16px;
        }

        /* Tailwind-like Laravel pagination fallback (when default template is used) */
        .pagination-container nav [aria-disabled="true"] > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden; /* prevent icon overflow */
            line-height: 1;   /* keep icon vertically centered */
        }
        .pagination-container nav [aria-disabled="true"] > span svg {
            width: 1em;       /* scale with font-size of container */
            height: 1em;
            max-width: 100%;
            max-height: 100%;
        }
        /* Ensure all pagination icons fit the control area */
        .pagination-container nav svg.w-5.h-5 {
            width: 16px !important;
            height: 16px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">MyGarage Admin</h4>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Дашборд
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                               href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i> Пользователи
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}" 
                               href="{{ route('admin.subscriptions.index') }}">
                                <i class="bi bi-credit-card"></i> Подписки
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.data.vehicles*') ? 'active' : '' }}" 
                               href="{{ route('admin.data.vehicles') }}">
                                <i class="bi bi-car-front"></i> Транспорт
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reminder-types.*') ? 'active' : '' }}" 
                               href="{{ route('admin.reminder-types.index') }}">
                                <i class="bi bi-bell"></i> Типы напоминаний
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.expense-types.*') ? 'active' : '' }}" 
                               href="{{ route('admin.expense-types.index') }}">
                                <i class="bi bi-currency-dollar"></i> Типы трат
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.manual-sections.*') ? 'active' : '' }}" 
                               href="{{ route('admin.manual-sections.index') }}">
                                <i class="bi bi-book"></i> Секции мануалов
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.advice-sections.*') ? 'active' : '' }}" 
                               href="{{ route('admin.advice-sections.index') }}">
                                <i class="bi bi-lightbulb"></i> Секции советов
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.advice-items.*') ? 'active' : '' }}" 
                               href="{{ route('admin.advice-items.index') }}">
                                <i class="bi bi-chat-square-text"></i> Советы
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}" 
                               href="{{ route('admin.faq.categories.index') }}">
                                <i class="bi bi-question-circle"></i> FAQ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.privacy-policy.*') ? 'active' : '' }}" 
                               href="{{ route('admin.privacy-policy.index') }}">
                                <i class="bi bi-shield-check"></i> Политика конфиденциальности
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.data.statistics') ? 'active' : '' }}" 
                               href="{{ route('admin.data.statistics') }}">
                                <i class="bi bi-graph-up"></i> Статистика
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.car-data.*') ? 'active' : '' }}" 
                               href="{{ route('admin.car-data.index') }}">
                                <i class="bi bi-gear-wide-connected"></i> Справочники авто
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.car-recommendations.*') ? 'active' : '' }}" 
                               href="{{ route('admin.car-recommendations.index') }}">
                                <i class="bi bi-wrench"></i> Рекомендации по обслуживанию
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.car-tyres.*') ? 'active' : '' }}" 
                               href="{{ route('admin.car-tyres.index') }}">
                                <i class="bi bi-circle"></i> Рекомендации по шинам
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="text-white">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-start w-100">
                                    <i class="bi bi-box-arrow-right"></i> Выйти
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('page-title', 'Админка')</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">Добро пожаловать, {{ auth()->guard('admin')->user()->name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page content -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
