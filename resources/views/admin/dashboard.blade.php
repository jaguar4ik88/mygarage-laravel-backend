@extends('admin.layouts.app')

@section('title', 'Дашборд')
@section('page-title', 'Дашборд')

@section('content')
<div class="row">
    <!-- Статистика -->
    <div class="col-12">
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Пользователи
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['users'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Транспорт
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['vehicles'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-car-front text-success" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    СТО
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['service_stations'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-gear text-info" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Записи расходов
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expenses_records'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Дополнительная статистика -->
    <div class="col-12">
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Типы напоминаний
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['reminder_types'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-bell text-secondary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-dark shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                    Секции мануалов
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['manual_sections'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-book text-dark" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    FAQ категории
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['faq_categories'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-question-circle text-danger" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    FAQ вопросы
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['faq_questions'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-chat-dots text-primary" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Последние пользователи и транспорт -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Последние пользователи</h6>
            </div>
            <div class="card-body">
                @if($recentUsers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Имя</th>
                                    <th>Email</th>
                                    <th>Дата регистрации</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Нет пользователей</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Последние транспортные средства</h6>
            </div>
            <div class="card-body">
                @if($recentVehicles->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Модель</th>
                                    <th>Владелец</th>
                                    <th>Дата добавления</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentVehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                        <td>{{ $vehicle->user->name ?? 'Неизвестно' }}</td>
                                        <td>
                                            @if($vehicle->added_at)
                                                {{ \Carbon\Carbon::parse($vehicle->added_at)->format('d.m.Y') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Нет транспортных средств</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-secondary {
    border-left: 0.25rem solid #858796 !important;
}
.border-left-dark {
    border-left: 0.25rem solid #5a5c69 !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
</style>
@endsection
