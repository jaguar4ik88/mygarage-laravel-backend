@extends('admin.layouts.app')

@section('title', 'FAQ Категория: ' . $category->key)
@section('page-title', 'Просмотр категории FAQ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о категории FAQ</h6>
                <div>
                    <a href="{{ route('admin.faq.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать категорию
                    </a>
                    <a href="{{ route('admin.faq.questions.create') }}?category_id={{ $category->id }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus"></i> Добавить вопрос
                    </a>
                    <a href="{{ route('admin.faq.categories.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $category->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ключ:</strong></td>
                                <td><code>{{ $category->key }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Иконка:</strong></td>
                                <td>
                                    <i class="bi bi-{{ $category->icon }}"></i> {{ $category->icon }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Статус:</strong></td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Активная</span>
                                    @else
                                        <span class="badge bg-secondary">Неактивная</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Порядок сортировки:</strong></td>
                                <td>{{ $category->sort_order }}</td>
                            </tr>
                            <tr>
                                <td><strong>Количество вопросов:</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $category->questions->count() }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Дата создания:</strong></td>
                                <td>{{ $category->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Переводы</h5>
                        @if($category->translations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Язык</th>
                                            <th>Название</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->translations as $translation)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ strtoupper($translation->locale) }}</span>
                                                </td>
                                                <td>{{ $translation->name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Переводы не найдены</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Вопросы категории -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Вопросы категории</h6>
                <a href="{{ route('admin.faq.questions.create') }}?category_id={{ $category->id }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Добавить вопрос
                </a>
            </div>
            
            <div class="card-body">
                @if($category->questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Ключ</th>
                                    <th>Вопрос</th>
                                    <th>Статус</th>
                                    <th>Порядок</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->questions as $question)
                                    <tr>
                                        <td>{{ $question->id }}</td>
                                        <td><code>{{ $question->key }}</code></td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-question-circle" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Вопросы в этой категории не найдены</p>
                        <a href="{{ route('admin.faq.questions.create') }}?category_id={{ $category->id }}" class="btn btn-primary btn-sm mt-2">
                            <i class="bi bi-plus"></i> Добавить первый вопрос
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
