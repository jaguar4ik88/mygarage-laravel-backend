@extends('admin.layouts.app')

@section('title', 'Просмотр раздела политики конфиденциальности')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Просмотр раздела политики конфиденциальности</h1>
                <div>
                    <a href="{{ route('admin.privacy-policy.edit', $privacyPolicy) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.privacy-policy.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                @switch($privacyPolicy->language)
                                    @case('ru')
                                        🇷🇺 Русский
                                        @break
                                    @case('uk')
                                        🇺🇦 Українська
                                        @break
                                    @case('en')
                                        🇬🇧 English
                                        @break
                                    @case('pl')
                                        🇵🇱 Polski
                                        @break
                                    @case('pt')
                                        🇵🇹 Português
                                        @break
                                    @case('ro')
                                        🇷🇴 Română
                                        @break
                                @endswitch
                                - {{ $privacyPolicy->title }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-muted">Раздел:</h6>
                                <code class="fs-6">{{ $privacyPolicy->section }}</code>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted">Заголовок:</h6>
                                <h4>{{ $privacyPolicy->title }}</h4>
                            </div>

                            <div class="mb-3">
                                <h6 class="text-muted">Содержимое:</h6>
                                <div class="border rounded p-3 bg-light">
                                    {!! nl2br(e($privacyPolicy->content)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Информация о разделе</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Язык:</strong></td>
                                    <td>
                                        @switch($privacyPolicy->language)
                                            @case('ru')
                                                🇷🇺 Русский
                                                @break
                                            @case('uk')
                                                🇺🇦 Українська
                                                @break
                                            @case('en')
                                                🇬🇧 English
                                                @break
                                            @case('pl')
                                                🇵🇱 Polski
                                                @break
                                            @case('pt')
                                                🇵🇹 Português
                                                @break
                                            @case('ro')
                                                🇷🇴 Română
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Раздел:</strong></td>
                                    <td><code>{{ $privacyPolicy->section }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Порядок:</strong></td>
                                    <td><span class="badge bg-info">{{ $privacyPolicy->sort_order }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Статус:</strong></td>
                                    <td>
                                        @if($privacyPolicy->is_active)
                                            <span class="badge bg-success">Активен</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивен</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Создано:</strong></td>
                                    <td>{{ $privacyPolicy->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Обновлено:</strong></td>
                                    <td>{{ $privacyPolicy->updated_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">Действия</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.privacy-policy.edit', $privacyPolicy) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>
                                
                                <form action="{{ route('admin.privacy-policy.destroy', $privacyPolicy) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Вы уверены, что хотите удалить этот раздел?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Удалить
                                    </button>
                                </form>

                                <a href="{{ route('admin.privacy-policy.index') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> К списку
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
