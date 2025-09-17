@extends('admin.layouts.app')

@section('title', 'Транспортное средство: ' . $vehicle->make . ' ' . $vehicle->model)
@section('page-title', 'Просмотр транспортного средства')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о транспортном средстве</h6>
                <a href="{{ route('admin.data.vehicles') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Назад к списку
                </a>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Основная информация</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $vehicle->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Владелец:</strong></td>
                                <td>
                                    <a href="{{ route('admin.users.show', $vehicle->user) }}" class="text-decoration-none">
                                        {{ $vehicle->user->name }} ({{ $vehicle->user->email }})
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Марка:</strong></td>
                                <td>{{ $vehicle->make }}</td>
                            </tr>
                            <tr>
                                <td><strong>Модель:</strong></td>
                                <td>{{ $vehicle->model }}</td>
                            </tr>
                            <tr>
                                <td><strong>Год:</strong></td>
                                <td>{{ $vehicle->year }}</td>
                            </tr>
                            <tr>
                                <td><strong>VIN:</strong></td>
                                <td>
                                    @if($vehicle->vin)
                                        <code>{{ $vehicle->vin }}</code>
                                    @else
                                        <span class="text-muted">Не указан</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Тип двигателя:</strong></td>
                                <td>{{ $vehicle->engine_type }}</td>
                            </tr>
                            <tr>
                                <td><strong>Пробег:</strong></td>
                                <td>{{ number_format($vehicle->mileage) }} км</td>
                            </tr>
                            <tr>
                                <td><strong>Дата добавления:</strong></td>
                                <td>{{ $vehicle->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Последнее обновление:</strong></td>
                                <td>{{ $vehicle->updated_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Статистика</h5>
                        <div class="row">
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary">{{ $vehicle->reminders->count() }}</h3>
                                        <p class="mb-0">Напоминаний</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-success">{{ $vehicle->serviceHistory->count() }}</h3>
                                        <p class="mb-0">Записей сервиса</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($vehicle->image_url)
                            <div class="mt-3">
                                <h6>Изображение</h6>
                                <img src="{{ $vehicle->image_url }}" 
                                     alt="{{ $vehicle->make }} {{ $vehicle->model }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Напоминания -->
@if($vehicle->reminders->count() > 0)
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
                                @foreach($vehicle->reminders as $reminder)
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

<!-- История сервиса -->
@if($vehicle->serviceHistory->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">История сервиса</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Тип</th>
                                    <th>Название</th>
                                    <th>Описание</th>
                                    <th>Стоимость</th>
                                    <th>Дата</th>
                                    <th>Пробег</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->serviceHistory as $service)
                                    <tr>
                                        <td>{{ $service->type ?? 'Не указан' }}</td>
                                        <td>{{ $service->title }}</td>
                                        <td>{{ $service->description ?? '-' }}</td>
                                        <td>{{ number_format($service->cost) }} {{ $vehicle->user->currency ?? 'UAH' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($service->service_date)->format('d.m.Y') }}</td>
                                        <td>{{ number_format($service->mileage) }} км</td>
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
