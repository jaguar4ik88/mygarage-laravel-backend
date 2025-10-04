@extends('admin.layouts.app')

@section('title', 'Транспортные средства')
@section('page-title', 'Транспортные средства пользователей')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Список транспортных средств</h6>
            </div>
            
            <div class="card-body">
                <!-- Фильтры и поиск -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Поиск по марке, модели, VIN..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="user_id" class="form-select">
                                <option value="">Все пользователи</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="make" class="form-select">
                                <option value="">Все марки</option>
                                @foreach($makes as $make)
                                    <option value="{{ $make }}" {{ request('make') === $make ? 'selected' : '' }}>
                                        {{ $make }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Поиск
                            </button>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('admin.data.vehicles') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Таблица транспортных средств -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Владелец</th>
                                <th>Марка</th>
                                <th>Модель</th>
                                <th>Год</th>
                                <th>VIN</th>
                                <th>Пробег</th>
                                <th>Добавлено</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->id }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $vehicle->user) }}" class="text-decoration-none">
                                            {{ $vehicle->user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $vehicle->make }}</td>
                                    <td>{{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->year }}</td>
                                    <td>
                                        @if($vehicle->vin)
                                            <code class="small">{{ $vehicle->vin }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($vehicle->mileage) }} км</td>
                                    <td>{{ $vehicle->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.data.vehicles.show', $vehicle) }}" 
                                           class="btn btn-outline-info btn-sm" 
                                           title="Просмотр">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-car-front" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Транспортные средства не найдены</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($vehicles->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $vehicles->appends(request()->query())->links("vendor.pagination.default") }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
