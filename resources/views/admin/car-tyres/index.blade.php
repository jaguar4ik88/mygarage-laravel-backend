@extends('admin.layouts.app')

@section('title', 'Рекомендации по шинам')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Рекомендации по шинам</h1>
                <a href="{{ route('admin.car-tyres.create') }}" class="btn btn-primary">
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
                    <form method="GET" action="{{ route('admin.car-tyres.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Поиск..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="brand" class="form-select">
                                    <option value="">Все марки</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                            {{ $brand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="dimension" class="form-select">
                                    <option value="">Все размеры</option>
                                    @foreach($dimensions as $dimension)
                                        <option value="{{ $dimension }}" {{ request('dimension') == $dimension ? 'selected' : '' }}>
                                            {{ $dimension }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-search"></i> Поиск
                                </button>
                                <a href="{{ route('admin.car-tyres.index') }}" class="btn btn-outline-secondary">
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
                    @if($tyres->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Марка</th>
                                        <th>Модель</th>
                                        <th>Год</th>
                                        <th>Размер шин</th>
                                        <th>Примечания</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tyres as $tyre)
                                        <tr>
                                            <td>{{ $tyre->id }}</td>
                                            <td>{{ $tyre->brand }}</td>
                                            <td>{{ $tyre->model }}</td>
                                            <td>{{ $tyre->year }}</td>
                                            <td>{{ $tyre->dimension }}</td>
                                            <td>{{ Str::limit($tyre->notes, 50) }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.car-tyres.show', $tyre) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.car-tyres.edit', $tyre) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.car-tyres.destroy', $tyre) }}" 
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
                            {{ $tyres->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">Рекомендации по шинам не найдены</p>
                            <a href="{{ route('admin.car-tyres.create') }}" class="btn btn-primary">
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
