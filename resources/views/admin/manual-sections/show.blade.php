@extends('admin.layouts.app')

@section('title', 'Секция мануала: ' . $manualSection->key)
@section('page-title', 'Просмотр секции мануала')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Информация о секции мануала</h6>
                <div>
                    <a href="{{ route('admin.manual-sections.edit', $manualSection) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Редактировать
                    </a>
                    <a href="{{ route('admin.manual-sections.index') }}" class="btn btn-secondary btn-sm">
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
                                <td>{{ $manualSection->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ключ:</strong></td>
                                <td><code>{{ $manualSection->key }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Иконка:</strong></td>
                                <td>
                                    <i class="bi bi-{{ $manualSection->icon }}"></i> {{ $manualSection->icon }}
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Статус:</strong></td>
                                <td>
                                    @if($manualSection->is_active)
                                        <span class="badge bg-success">Активная</span>
                                    @else
                                        <span class="badge bg-secondary">Неактивная</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Порядок сортировки:</strong></td>
                                <td>{{ $manualSection->sort_order }}</td>
                            </tr>
                            <tr>
                                <td><strong>Дата создания:</strong></td>
                                <td>{{ $manualSection->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Последнее обновление:</strong></td>
                                <td>{{ $manualSection->updated_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Переводы</h5>
                        @if($manualSection->translations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Язык</th>
                                            <th>Название</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($manualSection->translations as $translation)
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
