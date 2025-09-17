@extends('admin.layouts.app')

@section('title', 'Просмотр секции советов')
@section('page-title', 'Просмотр секции советов')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Секция: {{ $adviceSection->slug }}</h5>
                <div>
                    <a href="{{ route('admin.advice-sections.edit', $adviceSection) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.advice-sections.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>ID:</strong> {{ $adviceSection->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>Slug:</strong> <code>{{ $adviceSection->slug }}</code>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Иконка:</strong> 
                        @if($adviceSection->icon)
                            <i class="bi bi-{{ $adviceSection->icon }}"></i> {{ $adviceSection->icon }}
                        @else
                            <span class="text-muted">Не указана</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Порядок сортировки:</strong> {{ $adviceSection->sort_order }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Статус:</strong> 
                        @if($adviceSection->is_active)
                            <span class="badge bg-success">Активна</span>
                        @else
                            <span class="badge bg-danger">Неактивна</span>
                        @endif
                    </div>
                </div>

                <h6 class="mt-4 mb-3">Переводы названия</h6>
                @if($adviceSection->titleGroup && $adviceSection->titleGroup->translations->count() > 0)
                    <div class="row">
                        @foreach($adviceSection->titleGroup->translations as $translation)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-uppercase">{{ $translation->locale }}</h6>
                                        <p class="card-text">{{ $translation->title }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Переводы не найдены
                    </div>
                @endif

                @if($adviceSection->items->count() > 0)
                    <h6 class="mt-4 mb-3">Элементы советов ({{ $adviceSection->items->count() }})</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Иконка</th>
                                    <th>Порядок</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adviceSection->items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @if($item->titleGroup && $item->titleGroup->translations->count() > 0)
                                                {{ $item->titleGroup->translations->first()->title }}
                                            @else
                                                <span class="text-muted">Нет названия</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->icon)
                                                <i class="bi bi-{{ $item->icon }}"></i>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->sort_order }}</td>
                                        <td>
                                            @if($item->is_active)
                                                <span class="badge bg-success">Активен</span>
                                            @else
                                                <span class="badge bg-danger">Неактивен</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.advice-items.show', $item) }}" class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle"></i> В этой секции пока нет элементов советов
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
