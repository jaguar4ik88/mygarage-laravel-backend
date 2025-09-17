@extends('admin.layouts.app')

@section('title', 'Просмотр совета')
@section('page-title', 'Просмотр совета')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Совет #{{ $adviceItem->id }}</h5>
                <div>
                    <a href="{{ route('admin.advice-items.edit', $adviceItem) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.advice-items.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>ID:</strong> {{ $adviceItem->id }}
                    </div>
                    <div class="col-md-6">
                        <strong>Секция:</strong> 
                        @if($adviceItem->section && $adviceItem->section->titleGroup && $adviceItem->section->titleGroup->translations->count() > 0)
                            <span class="badge bg-info">{{ $adviceItem->section->titleGroup->translations->first()->title }}</span>
                        @else
                            <span class="text-muted">Не указана</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Иконка:</strong> 
                        @if($adviceItem->icon)
                            <i class="bi bi-{{ $adviceItem->icon }}"></i> {{ $adviceItem->icon }}
                        @else
                            <span class="text-muted">Не указана</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Порядок сортировки:</strong> {{ $adviceItem->sort_order }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Статус:</strong> 
                        @if($adviceItem->is_active)
                            <span class="badge bg-success">Активен</span>
                        @else
                            <span class="badge bg-danger">Неактивен</span>
                        @endif
                    </div>
                </div>

                <h6 class="mt-4 mb-3">Переводы названия</h6>
                @if($adviceItem->titleGroup && $adviceItem->titleGroup->translations->count() > 0)
                    <div class="row">
                        @foreach($adviceItem->titleGroup->translations as $translation)
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
                        <i class="bi bi-exclamation-triangle"></i> Переводы названия не найдены
                    </div>
                @endif

                <h6 class="mt-4 mb-3">Переводы содержания</h6>
                @if($adviceItem->contentGroup && $adviceItem->contentGroup->translations->count() > 0)
                    <div class="row">
                        @foreach($adviceItem->contentGroup->translations as $translation)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-uppercase">{{ $translation->locale }}</h6>
                                        <div class="card-text" style="white-space: pre-line;">{{ $translation->content }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Переводы содержания не найдены
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
