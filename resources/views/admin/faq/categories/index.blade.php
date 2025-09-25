@extends('admin.layouts.app')

@section('title', 'FAQ Категории')
@section('page-title', 'Управление FAQ категориями')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Список FAQ категорий</h6>
                <a href="{{ route('admin.faq.categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Добавить категорию
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
                                   placeholder="Поиск по названию..." 
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
                            <a href="{{ route('admin.faq.categories.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Таблица категорий -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Статус</th>
                                <th>Порядок</th>
                                <th>Вопросов</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>
                                        @foreach($category->translations as $translation)
                                            <span class="badge bg-light text-dark me-1">
                                                {{ strtoupper($translation->locale) }}: {{ $translation->title }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Активная</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивная</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->questions->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.faq.categories.show', $category) }}" 
                                               class="btn btn-outline-info" 
                                               title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.faq.categories.edit', $category) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.faq.categories.destroy', $category) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию?')">
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
                                        <p class="mt-2 mb-0">Категории не найдены</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($categories->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $categories->appends(request()->query())->links("vendor.pagination.default") }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
