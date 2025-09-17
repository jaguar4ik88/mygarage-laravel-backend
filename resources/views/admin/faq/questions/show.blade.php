@extends('admin.layouts.app')

@section('title', 'FAQ Вопрос: ' . $question->key)
@section('page-title', 'Просмотр вопроса FAQ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о вопросе FAQ</h6>
                <div>
                    <a href="{{ route('admin.faq.questions.edit', $question) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.faq.questions.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $question->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ключ:</strong></td>
                                <td><code>{{ $question->key }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Категория:</strong></td>
                                <td>
                                    <a href="{{ route('admin.faq.categories.show', $question->category) }}" class="text-decoration-none">
                                        <span class="badge bg-primary">
                                            {{ $question->category->translations->where('locale', 'ru')->first()->name ?? $question->category->key }}
                                        </span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Статус:</strong></td>
                                <td>
                                    @if($question->is_active)
                                        <span class="badge bg-success">Активный</span>
                                    @else
                                        <span class="badge bg-secondary">Неактивный</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Порядок сортировки:</strong></td>
                                <td>{{ $question->sort_order }}</td>
                            </tr>
                            <tr>
                                <td><strong>Дата создания:</strong></td>
                                <td>{{ $question->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Последнее обновление:</strong></td>
                                <td>{{ $question->updated_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Переводы</h5>
                        @if($question->translations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Язык</th>
                                            <th>Вопрос</th>
                                            <th>Ответ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($question->translations as $translation)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ strtoupper($translation->locale) }}</span>
                                                </td>
                                                <td>{{ Str::limit($translation->question, 30) }}</td>
                                                <td>{{ Str::limit($translation->answer, 30) }}</td>
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

<!-- Детальные переводы -->
@if($question->translations->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Детальные переводы</h6>
                </div>
                <div class="card-body">
                    @foreach($question->translations as $translation)
                        <div class="mb-4 p-3 border rounded">
                            <h6>
                                <span class="badge bg-primary me-2">{{ strtoupper($translation->locale) }}</span>
                                Вопрос и ответ
                            </h6>
                            <div class="mb-2">
                                <strong>Вопрос:</strong>
                                <p class="mt-1">{{ $translation->question }}</p>
                            </div>
                            <div>
                                <strong>Ответ:</strong>
                                <div class="mt-1 p-3 bg-light rounded">
                                    {!! nl2br(e($translation->answer)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
