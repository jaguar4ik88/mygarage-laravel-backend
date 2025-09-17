@extends('admin.layouts.app')

@section('title', 'Тип напоминания: ' . $reminderType->key)
@section('page-title', 'Просмотр типа напоминания')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о типе напоминания</h6>
                <div>
                    <a href="{{ route('admin.reminder-types.edit', $reminderType) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.reminder-types.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $reminderType->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ключ:</strong></td>
                                <td><code>{{ $reminderType->key }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Иконка:</strong></td>
                                <td>
                                    <i class="bi bi-{{ $reminderType->icon }}"></i> {{ $reminderType->icon }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Цвет:</strong></td>
                                <td>
                                    <span class="badge" style="background-color: {{ $reminderType->color }}; color: white;">
                                        {{ $reminderType->color }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Статус:</strong></td>
                                <td>
                                    @if($reminderType->is_active)
                                        <span class="badge bg-success">Активный</span>
                                    @else
                                        <span class="badge bg-secondary">Неактивный</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Порядок сортировки:</strong></td>
                                <td>{{ $reminderType->sort_order }}</td>
                            </tr>
                            <tr>
                                <td><strong>Дата создания:</strong></td>
                                <td>{{ $reminderType->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Последнее обновление:</strong></td>
                                <td>{{ $reminderType->updated_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Переводы</h5>
                        @if($reminderType->translations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Язык</th>
                                            <th>Название</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reminderType->translations as $translation)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ strtoupper($translation->locale) }}</span>
                                                </td>
                                                <td>{{ $translation->title }}</td>
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
@endsection
