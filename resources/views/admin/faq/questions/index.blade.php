@extends('admin.layouts.app')

@section('title', 'FAQ Вопросы')
@section('page-title', 'Управление вопросами FAQ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Список вопросов FAQ</h6>
                <a href="{{ route('admin.faq.questions.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Добавить вопрос
                </a>
            </div>
            
            <div class="card-body">
                <!-- Фильтры и поиск -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Поиск по ключу..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category_id" class="form-select">
                                <option value="">Все категории</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->translations->where('locale', 'ru')->first()->name ?? $category->key }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
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
                        <div class="col-md-2 text-end">
                            <a href="{{ route('admin.faq.questions.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Таблица вопросов -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Ключ</th>
                                <th>Категория</th>
                                <th>Вопрос</th>
                                <th>Статус</th>
                                <th>Порядок</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($questions as $question)
                                <tr>
                                    <td>{{ $question->id }}</td>
                                    <td><code>{{ $question->key }}</code></td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $question->category->translations->where('locale', 'ru')->first()->name ?? $question->category->key }}
                                        </span>
                                    </td>
                                    <td>
                                        @foreach($question->translations as $translation)
                                            <div class="mb-1">
                                                <span class="badge bg-light text-dark me-1">
                                                    {{ strtoupper($translation->locale) }}
                                                </span>
                                                {{ Str::limit($translation->question, 50) }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($question->is_active)
                                            <span class="badge bg-success">Активный</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивный</span>
                                        @endif
                                    </td>
                                    <td>{{ $question->sort_order }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.faq.questions.show', $question) }}" 
                                               class="btn btn-outline-info" 
                                               title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.faq.questions.edit', $question) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.faq.questions.destroy', $question) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить этот вопрос?')">
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
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-question-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Вопросы FAQ не найдены</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($questions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $questions->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
