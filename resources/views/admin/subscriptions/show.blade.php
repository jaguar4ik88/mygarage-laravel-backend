@extends('admin.layouts.app')

@section('title', $subscription->display_name)
@section('page-title', 'Подписка: ' . $subscription->display_name)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о подписке</h6>
                <div>
                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">ID</th>
                        <td>{{ $subscription->id }}</td>
                    </tr>
                    <tr>
                        <th>Название (системное)</th>
                        <td><code>{{ $subscription->name }}</code></td>
                    </tr>
                    <tr>
                        <th>Отображаемое название</th>
                        <td><strong>{{ $subscription->display_name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Цена</th>
                        <td>
                            @if($subscription->price > 0)
                                <strong class="text-success">${{ number_format($subscription->price / 100, 2) }}</strong> ({{ $subscription->price }} центов)
                            @else
                                <span class="badge bg-secondary">Бесплатно</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Длительность</th>
                        <td>
                            @if($subscription->duration_days > 0)
                                {{ $subscription->duration_days }} дней
                            @else
                                <span class="badge bg-info">Бессрочно</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Макс. автомобилей</th>
                        <td>{{ $subscription->max_vehicles }}</td>
                    </tr>
                    <tr>
                        <th>Макс. напоминаний</th>
                        <td>
                            @if($subscription->max_reminders)
                                {{ $subscription->max_reminders }}
                            @else
                                <span class="badge bg-success">Безлимит</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Статус</th>
                        <td>
                            @if($subscription->is_active)
                                <span class="badge bg-success">Активна</span>
                            @else
                                <span class="badge bg-danger">Неактивна</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Создана</th>
                        <td>{{ $subscription->created_at->format('d.m.Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Обновлена</th>
                        <td>{{ $subscription->updated_at->format('d.m.Y H:i') }}</td>
                    </tr>
                </table>

                <h5 class="mt-4">Функции подписки:</h5>
                @if($subscription->features && count($subscription->features) > 0)
                    <ul>
                        @foreach($subscription->features as $feature)
                            <li><code>{{ $feature }}</code></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Функции не указаны</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Статистика</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h2 class="text-primary">{{ $subscription->user_subscriptions_count ?? 0 }}</h2>
                    <p class="text-muted">Всего подписок</p>
                </div>
                <hr>
                <div class="text-center">
                    <h2 class="text-success">{{ $activeSubscriptions ?? 0 }}</h2>
                    <p class="text-muted">Активных подписок</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
