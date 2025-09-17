@extends('admin.layouts.app')

@section('title', 'Типы трат')
@section('page-title', 'Управление типами трат')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Список типов трат</h6>
                <a href="{{ route('admin.expense-types.create') }}" class="btn btn-primary btn-sm">
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
                                   placeholder="Поиск по slug..." 
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
                            <a href="{{ route('admin.expense-types.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Таблица типов трат -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Slug</th>
                                <th>Переводы</th>
                                <th>Статус</th>
                                <th>Создан</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenseTypes as $type)
                                <tr>
                                    <td>{{ $type->id }}</td>
                                    <td><code>{{ $type->slug }}</code></td>
                                    <td>
                                        @if($type->translationGroup && $type->translationGroup->translations)
                                            @foreach($type->translationGroup->translations as $translation)
                                                <span class="badge bg-light text-dark me-1">
                                                    {{ strtoupper($translation->locale) }}: {{ $translation->title }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Нет переводов</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($type->is_active)
                                            <span class="badge bg-success">Активный</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивный</span>
                                        @endif
                                    </td>
                                    <td>{{ $type->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.expense-types.show', $type) }}" 
                                               class="btn btn-outline-info" 
                                               title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.expense-types.edit', $type) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.expense-types.destroy', $type) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить этот тип трат?')">
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
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Типы трат не найдены</p>
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
