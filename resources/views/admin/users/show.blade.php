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
                                        <td>{{ $vehicle->created_at->format('d.m.Y') }}</td>
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
