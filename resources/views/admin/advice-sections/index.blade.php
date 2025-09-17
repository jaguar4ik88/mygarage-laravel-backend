@extends('admin.layouts.app')

@section('title', 'Секции советов')
@section('page-title', 'Секции советов')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Секции советов</h2>
    <a href="{{ route('admin.advice-sections.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Добавить секцию
    </a>
</div>

@if($sections->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Slug</th>
                    <th>Название</th>
                    <th>Иконка</th>
                    <th>Порядок</th>
                    <th>Активна</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                    <tr>
                        <td>{{ $section->id }}</td>
                        <td><code>{{ $section->slug }}</code></td>
                        <td>
                            @if($section->titleGroup && $section->titleGroup->translations->count() > 0)
                                @foreach($section->titleGroup->translations as $translation)
                                    <span class="badge bg-secondary me-1">{{ strtoupper($translation->locale) }}: {{ $translation->title }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Нет переводов</span>
                            @endif
                        </td>
                        <td>
                            @if($section->icon)
                                <i class="bi bi-{{ $section->icon }}"></i> {{ $section->icon }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $section->sort_order }}</td>
                        <td>
                            @if($section->is_active)
                                <span class="badge bg-success">Да</span>
                            @else
                                <span class="badge bg-danger">Нет</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.advice-sections.show', $section) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.advice-sections.edit', $section) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.advice-sections.destroy', $section) }}" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Вы уверены, что хотите удалить эту секцию?')">
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
        <i class="bi bi-info-circle"></i> Секции советов не найдены. 
        <a href="{{ route('admin.advice-sections.create') }}">Создать первую секцию</a>
    </div>
@endif
@endsection
