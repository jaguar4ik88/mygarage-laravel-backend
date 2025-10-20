@extends('admin.layouts.app')

@section('title', 'Подписки')
@section('page-title', 'Управление подписками')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Список подписок</h6>
                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Добавить подписку
                </a>
            </div>
            
            <div class="card-body">
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

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Отображаемое имя</th>
                                <th>Цена</th>
                                <th>Длительность</th>
                                <th>Макс. авто</th>
                                <th>Макс. напоминаний</th>
                                <th>Активных подписок</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $subscription->id }}</td>
                                    <td><code>{{ $subscription->name }}</code></td>
                                    <td><strong>{{ $subscription->display_name }}</strong></td>
                                    <td>
                                        @if($subscription->price > 0)
                                            <strong class="text-success">${{ number_format($subscription->price / 100, 2) }}</strong>
                                        @else
                                            <span class="badge bg-secondary">Бесплатно</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->duration_days > 0)
                                            {{ $subscription->duration_days }} дн.
                                        @else
                                            <span class="badge bg-info">Бессрочно</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $subscription->max_vehicles }}</td>
                                    <td class="text-center">
                                        @if($subscription->max_reminders)
                                            {{ $subscription->max_reminders }}
                                        @else
                                            <span class="badge bg-success">∞</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $subscription->user_subscriptions_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        @if($subscription->is_active)
                                            <span class="badge bg-success">Активна</span>
                                        @else
                                            <span class="badge bg-danger">Неактивна</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.subscriptions.show', $subscription) }}" 
                                               class="btn btn-sm btn-info" title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" 
                                               class="btn btn-sm btn-warning" title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить эту подписку?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Удалить">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        Подписки не найдены
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

