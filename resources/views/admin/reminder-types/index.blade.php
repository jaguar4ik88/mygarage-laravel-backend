@extends('admin.layouts.app')

@section('title', 'Типы напоминаний')
@section('page-title', 'Управление типами напоминаний')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Список типов напоминаний</h6>
                <a href="{{ route('admin.reminder-types.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Добавить тип
                </a>
            </div>
            
            <div class="card-body">
                <!-- Фильтры и поиск -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Поиск по ключу..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Все статусы</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Неактивные</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Поиск
                            </button>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('admin.reminder-types.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Таблица типов напоминаний -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ключ</th>
                                <th>Название</th>
                                <th>Иконка</th>
                                <th>Цвет</th>
                                <th>Статус</th>
                                <th>Порядок</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reminderTypes as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td><code>{{ $type->key }}</code></td>
                                    <td>
                                        @foreach($type->translations as $translation)
                                            <span class="badge bg-light text-dark me-1">
                                                {{ strtoupper($translation->locale) }}: {{ $translation->title }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <i class="bi bi-{{ $type->icon }}"></i> {{ $type->icon }}
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $type->color }}; color: white;">
                                            {{ $type->color }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($type->is_active)
                                            <span class="badge bg-success">Активный</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивный</span>
                                        @endif
                                    </td>
                                    <td>{{ $type->sort_order }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.reminder-types.show', $type) }}" 
                                               class="btn btn-outline-info" 
                                               title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.reminder-types.edit', $type) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.reminder-types.destroy', $type) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить этот тип напоминания?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Удалить">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Типы напоминаний не найдены</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($reminderTypes->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $reminderTypes->appends(request()->query())->links('vendor.pagination.default') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
