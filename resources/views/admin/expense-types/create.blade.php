@extends('admin.layouts.app')

@section('title', 'Создать тип трат')
@section('page-title', 'Создание нового типа трат')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Создание типа трат</h6>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.expense-types.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" 
                                       name="slug" 
                                       value="{{ old('slug') }}" 
                                       required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Уникальный идентификатор типа (например: maintenance, repair, fuel)</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Активный
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="mb-3">Переводы</h6>
                        
                        <div id="translations-container">
                            <div class="translation-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Язык</label>
                                        <select class="form-select" name="translations[0][locale]" required>
                                            <option value="">Выберите язык</option>
                                            <option value="uk" {{ old('translations.0.locale') === 'uk' ? 'selected' : '' }}>Украинский</option>
                                            <option value="ru" {{ old('translations.0.locale') === 'ru' ? 'selected' : '' }}>Русский</option>
                                            <option value="en" {{ old('translations.0.locale') === 'en' ? 'selected' : '' }}>Английский</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Название</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="translations[0][title]" 
                                               value="{{ old('translations.0.title') }}" 
                                               required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-translation">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-translation">
                            <i class="bi bi-plus"></i> Добавить перевод
                        </button>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.expense-types.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Назад
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Создать
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let translationIndex = 1;
    
    document.getElementById('add-translation').addEventListener('click', function() {
        const container = document.getElementById('translations-container');
        const newItem = document.createElement('div');
        newItem.className = 'translation-item mb-3 p-3 border rounded';
        newItem.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Язык</label>
                    <select class="form-select" name="translations[${translationIndex}][locale]" required>
                        <option value="">Выберите язык</option>
                        <option value="uk">Украинский</option>
                        <option value="ru">Русский</option>
                        <option value="en">Английский</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Название</label>
                    <input type="text" class="form-control" name="translations[${translationIndex}][title]" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-translation">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        translationIndex++;
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-translation') || e.target.closest('.remove-translation')) {
            e.target.closest('.translation-item').remove();
        }
    });
});
</script>
@endsection
