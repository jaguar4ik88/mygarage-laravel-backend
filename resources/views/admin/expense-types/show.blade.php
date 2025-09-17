@extends('admin.layouts.app')

@section('title', 'Просмотр типа трат')
@section('page-title', 'Просмотр типа трат')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Тип трат: {{ $expenseType->slug }}</h6>
                <div>
                    <a href="{{ route('admin.expense-types.edit', $expenseType) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.expense-types.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Основная информация</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td>{{ $expenseType->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug:</strong></td>
                                <td><code>{{ $expenseType->slug }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Статус:</strong></td>
                                <td>
                                    @if($expenseType->is_active)
                                        <span class="badge bg-success">Активный</span>
                                    @else
                                        <span class="badge bg-secondary">Неактивный</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Создан:</strong></td>
                                <td>{{ $expenseType->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Обновлен:</strong></td>
                                <td>{{ $expenseType->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6>Переводы</h6>
                        @if($expenseType->translationGroup && $expenseType->translationGroup->translations)
                            <div class="list-group">
                                @foreach($expenseType->translationGroup->translations as $translation)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ strtoupper($translation->locale) }}:</strong>
                                            {{ $translation->title }}
                                        </div>
                                        <small class="text-muted">{{ $translation->created_at->format('d.m.Y') }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                Переводы не найдены
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Статистика использования</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $expenseType->expenses->count() }}</h5>
                                    <p class="card-text">Всего записей</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $expenseType->expenses->sum('cost') }} ₴</h5>
                                    <p class="card-text">Общая сумма</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $expenseType->expenses->avg('cost') ? number_format($expenseType->expenses->avg('cost'), 2) : 0 }} ₴</h5>
                                    <p class="card-text">Средняя сумма</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
