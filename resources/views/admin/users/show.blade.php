@extends('admin.layouts.app')

@section('title', 'Пользователь: ' . $user->name)
@section('page-title', 'Просмотр пользователя')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о пользователе</h6>
                <div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Основная информация</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $user->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Имя:</strong></td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>Роль:</strong></td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="badge bg-danger">Администратор</span>
                                    @else
                                        <span class="badge bg-primary">Пользователь</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Валюта:</strong></td>
                                <td>{{ $user->currency }}</td>
                            </tr>
                            <tr>
                                <td><strong>Дата регистрации:</strong></td>
                                <td>{{ $user->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Последнее обновление:</strong></td>
                                <td>{{ $user->updated_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Статистика</h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ $user->vehicles->count() }}</h3>
                                        <p class="mb-0">Транспортных средств</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ $user->reminders->count() }}</h3>
                                        <p class="mb-0">Напоминаний</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mt-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-info">{{ $user->serviceStations->count() }}</h3>
                                        <p class="mb-0">СТО в избранном</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="m-0 font-weight-bold text-primary mb-0">Подписка</h6>
                <div class="small text-muted">Состояние в приложении — по данным сервера (как после verify API).</div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Тариф (plan_type):</strong></td>
                                <td>
                                    @if(($user->plan_type ?? 'free') === 'free')
                                        <span class="badge bg-secondary">free</span>
                                    @elseif($user->plan_type === 'pro')
                                        <span class="badge bg-info">pro</span>
                                    @elseif($user->plan_type === 'premium')
                                        <span class="badge bg-warning text-dark">premium</span>
                                    @else
                                        <span class="badge bg-light text-dark">{{ $user->plan_type }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Срок действия (users.subscription_expires_at):</strong></td>
                                <td>
                                    {{ $user->subscription_expires_at ? $user->subscription_expires_at->timezone(config('app.timezone'))->format('d.m.Y H:i:s T') : '—' }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Статус по серверной логике:</strong></td>
                                <td>
                                    @if(!$user->hasActiveSubscription())
                                        <span class="badge bg-secondary">нет активной подписки (платный план недействителен или истёк)</span>
                                    @else
                                        <span class="badge bg-success">активно</span>
                                        @if($user->getPlanType() === 'free')
                                            <span class="text-muted">(тариф Free всегда считается доступным)</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>SaaS-уровень:</strong></td>
                                <td>
                                    @if($user->isPremium())
                                        <span class="badge bg-warning text-dark">Premium</span>
                                    @elseif($user->isPro())
                                        <span class="badge bg-info">Pro</span>
                                    @else
                                        <span class="badge bg-secondary">Free</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Платформа (последняя верификация):</strong></td>
                                <td>{{ $user->platform ?: '—' }}</td>
                            </tr>
                            <tr>
                                <td><strong>transaction_id:</strong></td>
                                <td><code>{{ $user->transaction_id ?: '—' }}</code></td>
                            </tr>
                        </table>
                        <div class="alert alert-warning mb-3 py-2 small">
                            <strong>Отмена для теста</strong> — меняются только записи на сервере (планы в API),
                            платёжка Apple/Google и RevenueCat это не затрагивают.
                        </div>
                        <form method="POST" action="{{ route('admin.users.cancel-subscription', $user) }}" class="d-inline"
                              onsubmit="return confirm('Сбросить подписку пользователя на Free на сервере? Отменит активные строки в user_subscriptions и очистит срок действия.');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-x-circle"></i> Отменить подписку (тест)
                            </button>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <h6 class="text-muted mb-2">Записи user_subscriptions (последние)</h6>
                        @forelse($user->subscriptions->take(20) as $row)
                            @if($loop->first)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Тариф</th>
                                            <th>С</th>
                                            <th>По</th>
                                            <th>Акт.</th>
                                            <th>Платформа</th>
                                            <th>Отменена</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            @endif
                                        <tr>
                                            <td>{{ optional($row->subscription)->display_name ?? optional($row->subscription)->name ?? '—' }}</td>
                                            <td>{{ $row->starts_at ? $row->starts_at->timezone(config('app.timezone'))->format('d.m.Y H:i') : '—' }}</td>
                                            <td>{{ $row->expires_at ? $row->expires_at->timezone(config('app.timezone'))->format('d.m.Y H:i') : '—' }}</td>
                                            <td>
                                                @if($row->is_active && !$row->cancelled_at)
                                                    <span class="badge bg-success">да</span>
                                                @else
                                                    <span class="badge bg-secondary">нет</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->platform ?: '—' }}</td>
                                            <td>{{ $row->cancelled_at ? $row->cancelled_at->timezone(config('app.timezone'))->format('d.m.Y H:i') : '—' }}</td>
                                        </tr>
                                        @if($row->transaction_id || $row->original_transaction_id)
                                            <tr class="small text-muted">
                                                <td colspan="6">
                                                    tx: <code>{{ $row->transaction_id ?: '—' }}</code>
                                                    · original: <code>{{ $row->original_transaction_id ?: '—' }}</code>
                                                </td>
                                            </tr>
                                        @endif
                            @if($loop->last)
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        @empty
                            <p class="text-muted mb-0">История покупок пуста.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Транспортные средства пользователя -->
@if($user->vehicles->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Транспортные средства</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Марка</th>
                                    <th>Модель</th>
                                    <th>Год</th>
                                    <th>Пробег</th>
                                    <th>Дата добавления</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->make }}</td>
                                        <td>{{ $vehicle->model }}</td>
                                        <td>{{ $vehicle->year }}</td>
                                        <td>{{ number_format($vehicle->mileage) }} км</td>
                                        <td>{{ $vehicle->created_at ? $vehicle->created_at->format('d.m.Y') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Напоминания пользователя -->
@if($user->reminders->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Напоминания</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Тип</th>
                                    <th>Описание</th>
                                    <th>Дата</th>
                                    <th>Пробег</th>
                                    <th>Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->reminders as $reminder)
                                    <tr>
                                        <td>{{ $reminder->type ?? 'Не указан' }}</td>
                                        <td>{{ $reminder->description }}</td>
                                        <td>{{ $reminder->reminder_date ? \Carbon\Carbon::parse($reminder->reminder_date)->format('d.m.Y') : '-' }}</td>
                                        <td>{{ $reminder->mileage ? number_format($reminder->mileage) . ' км' : '-' }}</td>
                                        <td>
                                            @if($reminder->is_completed)
                                                <span class="badge bg-success">Выполнено</span>
                                            @else
                                                <span class="badge bg-warning">Ожидает</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
