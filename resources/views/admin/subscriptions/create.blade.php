@extends('admin.layouts.app')

@section('title', 'Создать подписку')
@section('page-title', 'Создание новой подписки')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Новая подписка</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Название (системное) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required
                               placeholder="free, pro, premium">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Используется в коде. Только латиница, строчные буквы.</small>
                    </div>

                    <div class="mb-3">
                        <label for="display_name" class="form-label">Отображаемое название <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                               id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Цена (в центах) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', 0) }}" required min="0">
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">499 = $4.99</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="duration_days" class="form-label">Длительность (дней) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                                   id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" required min="0">
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">0 = бессрочно</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_vehicles" class="form-label">Макс. автомобилей <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_vehicles') is-invalid @enderror" 
                                   id="max_vehicles" name="max_vehicles" value="{{ old('max_vehicles', 1) }}" required min="1">
                            @error('max_vehicles')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="max_reminders" class="form-label">Макс. напоминаний</label>
                            <input type="number" class="form-control @error('max_reminders') is-invalid @enderror" 
                                   id="max_reminders" name="max_reminders" value="{{ old('max_reminders') }}" min="1">
                            @error('max_reminders')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Оставьте пустым для безлимита</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="features_text" class="form-label">Функции (каждая с новой строки)</label>
                        <textarea class="form-control @error('features_text') is-invalid @enderror" 
                                  id="features_text" name="features_text" rows="6">{{ old('features_text') }}</textarea>
                        @error('features_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Например: photo_documents, pdf_export, unlimited_reminders</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Активна
                        </label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Назад
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Создать
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Подсказка</h6>
            </div>
            <div class="card-body">
                <h6>Примеры функций:</h6>
                <ul class="small">
                    <li><code>photo_documents</code> - фото документов</li>
                    <li><code>receipt_photos</code> - фото чеков</li>
                    <li><code>pdf_export</code> - экспорт в PDF</li>
                    <li><code>unlimited_reminders</code> - безлимит напоминаний</li>
                    <li><code>expense_reminders</code> - напоминания о тратах</li>
                    <li><code>ai_assistant</code> - AI помощник</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

