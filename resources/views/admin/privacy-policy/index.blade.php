@extends('admin.layouts.app')

@section('title', 'Политика конфиденциальности')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Политика конфиденциальности</h1>
                <a href="{{ route('admin.privacy-policy.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Добавить раздел
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @foreach($policies as $language => $languagePolicies)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            @switch($language)
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
                            <span class="badge bg-secondary ms-2">{{ count($languagePolicies) }} разделов</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Раздел</th>
                                        <th>Заголовок</th>
                                        <th>Порядок</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($languagePolicies as $policy)
                                        <tr>
                                            <td>
                                                <code>{{ $policy->section }}</code>
                                            </td>
                                            <td>{{ Str::limit($policy->title, 50) }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $policy->sort_order }}</span>
                                            </td>
                                            <td>
                                                @if($policy->is_active)
                                                    <span class="badge bg-success">Активен</span>
                                                @else
                                                    <span class="badge bg-secondary">Неактивен</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.privacy-policy.show', $policy) }}" 
                                                       class="btn btn-outline-info" title="Просмотр">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.privacy-policy.edit', $policy) }}" 
                                                       class="btn btn-outline-primary" title="Редактировать">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.privacy-policy.destroy', $policy) }}" 
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Вы уверены, что хотите удалить этот раздел?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Удалить">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($policies->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Разделы политики конфиденциальности не найдены</h4>
                    <p class="text-muted">Создайте первый раздел, чтобы начать работу</p>
                    <a href="{{ route('admin.privacy-policy.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Добавить раздел
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
