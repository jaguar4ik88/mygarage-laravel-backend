@extends('admin.layouts.app')

@section('title', 'Пользователи')
@section('page-title', 'Управление пользователями')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Список пользователей</h6>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus"></i> Добавить пользователя
                </a>
            </div>
            
            <div class="card-body">
                <!-- Фильтры и поиск -->
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Поиск по имени или email..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">Все роли</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Админы</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Пользователи</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Поиск
                            </button>
                        </div>
                        <div class="col-md-3 text-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Таблица пользователей -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Валюта</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge bg-danger">Админ</span>
                                        @else
                                            <span class="badge bg-primary">Пользователь</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->currency }}</td>
                                    <td>{{ $user->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-outline-info" 
                                               title="Просмотр">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-outline-warning" 
                                               title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($user->id !== auth()->guard('admin')->id())
                                                <form method="POST" 
                                                      action="{{ route('admin.users.destroy', $user) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger" 
                                                            title="Удалить">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-2 mb-0">Пользователи не найдены</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($users->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->appends(request()->query())->links('vendor.pagination.default') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
