@extends('admin.layouts.app')

@section('title', 'Советы')
@section('page-title', 'Советы')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Советы</h2>
    <a href="{{ route('admin.advice-items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Добавить совет
    </a>
</div>

@if($items->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Секция</th>
                    <th>Название</th>
                    <th>Иконка</th>
                    <th>Порядок</th>
                    <th>Активен</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            @if($item->section && $item->section->titleGroup && $item->section->titleGroup->translations->count() > 0)
                                <span class="badge bg-info">{{ $item->section->titleGroup->translations->first()->title }}</span>
                            @else
                                <span class="text-muted">Без секции</span>
                            @endif
                        </td>
                        <td>
                            @if($item->titleGroup && $item->titleGroup->translations->count() > 0)
                                @foreach($item->titleGroup->translations as $translation)
                                    <span class="badge bg-secondary me-1">{{ strtoupper($translation->locale) }}: {{ Str::limit($translation->title, 30) }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Нет переводов</span>
                            @endif
                        </td>
                        <td>
                            @if($item->icon)
                                <i class="bi bi-{{ $item->icon }}"></i> {{ $item->icon }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->sort_order }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">Да</span>
                            @else
                                <span class="badge bg-danger">Нет</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.advice-items.show', $item) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.advice-items.edit', $item) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.advice-items.destroy', $item) }}" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Вы уверены, что хотите удалить этот совет?')">
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
@else
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Советы не найдены. 
        <a href="{{ route('admin.advice-items.create') }}">Создать первый совет</a>
    </div>
@endif
@endsection
