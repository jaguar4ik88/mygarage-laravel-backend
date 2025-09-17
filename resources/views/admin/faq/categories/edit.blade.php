@extends('admin.layouts.app')

@section('title', 'Редактировать категорию FAQ')
@section('page-title', 'Редактировать категорию FAQ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Редактирование категории FAQ: {{ $category->key }}</h6>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.faq.categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="key" class="form-label">Ключ <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('key') is-invalid @enderror" 
                                       id="key" 
                                       name="key" 
                                       value="{{ old('key', $category->key) }}" 
                                       placeholder="general_questions" 
                                       required>
                                <div class="form-text">Уникальный ключ для идентификации (только латинские буквы, цифры и подчеркивания)</div>
                                @error('key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Иконка <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" 
                                       name="icon" 
                                       value="{{ old('icon', $category->icon) }}" 
                                       placeholder="question-circle" 
                                       required>
                                <div class="form-text">Название иконки Bootstrap Icons (без префикса bi-)</div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Порядок сортировки</label>
                                <input type="number" 
                                       class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', $category->sort_order) }}" 
                                       min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   class="form-check-input @error('is_active') is-invalid @enderror" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1" 
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Активная
                            </label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Переводы -->
                    <h5 class="mt-4 mb-3">Переводы</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_en_name" class="form-label">Название (EN) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('translations.0.name') is-invalid @enderror" 
                                       id="translations_en_name" 
                                       name="translations[0][name]" 
                                       value="{{ old('translations.0.name', $category->translations->where('locale', 'en')->first()->name ?? '') }}" 
                                       required>
                                <input type="hidden" name="translations[0][locale]" value="en">
                                @error('translations.0.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_ru_name" class="form-label">Название (RU) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('translations.1.name') is-invalid @enderror" 
                                       id="translations_ru_name" 
                                       name="translations[1][name]" 
                                       value="{{ old('translations.1.name', $category->translations->where('locale', 'ru')->first()->name ?? '') }}" 
                                       required>
                                <input type="hidden" name="translations[1][locale]" value="ru">
                                @error('translations.1.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_uk_name" class="form-label">Название (UK) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('translations.2.name') is-invalid @enderror" 
                                       id="translations_uk_name" 
                                       name="translations[2][name]" 
                                       value="{{ old('translations.2.name', $category->translations->where('locale', 'uk')->first()->name ?? '') }}" 
                                       required>
                                <input type="hidden" name="translations[2][locale]" value="uk">
                                @error('translations.2.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.faq.categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Назад к списку
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check"></i> Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
