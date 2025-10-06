@extends('admin.layouts.app')

@section('title', 'Рекомендации по обслуживанию')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Рекомендации по обслуживанию</h1>
                <a href="{{ route('admin.car-recommendations.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Добавить рекомендацию
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Фильтры -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.car-recommendations.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Поиск..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="maker" class="form-select">
                                    <option value="">Все марки</option>
                                    @foreach($makers as $maker)
                                        <option value="{{ $maker }}" {{ request('maker') == $maker ? 'selected' : '' }}>
                                            {{ $maker }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="manual_section_id" class="form-select">
                                    <option value="">Все типы</option>
                                    @foreach($manualSections as $section)
                                        <option value="{{ $section->id }}" {{ request('manual_section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i> Поиск
                                </button>
                                <a href="{{ route('admin.car-recommendations.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Сбросить
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Таблица -->
            <div class="card">
                <div class="card-body">
                    @if($recommendations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Марка</th>
                                        <th>Модель</th>
                                        <th>Год</th>
                                        <th>Пробег (км)</th>
                                        <th>Секция</th>
                                        <th>Рекомендация</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recommendations as $recommendation)
                                        <tr>
                                            <td>{{ $recommendation->id }}</td>
                                            <td>{{ $recommendation->maker }}</td>
                                            <td>{{ $recommendation->model }}</td>
                                            <td>{{ $recommendation->year }}</td>
                                            <td>{{ number_format($recommendation->mileage_interval) }}</td>
                                            <td>{{ $recommendation->manualSection?->title ?? '-' }}</td>
                                            <td>{{ Str::limit($recommendation->recommendation, 50) }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.car-recommendations.show', $recommendation) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.car-recommendations.edit', $recommendation) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.car-recommendations.destroy', $recommendation) }}" 
                                                          class="d-inline" onsubmit="return confirm('Вы уверены?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
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

                        <!-- Пагинация -->
                        <div class="pagination-container">
                            {{ $recommendations->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">Рекомендации не найдены</p>
                            <a href="{{ route('admin.car-recommendations.create') }}" class="btn btn-primary">
                                Добавить первую рекомендацию
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
