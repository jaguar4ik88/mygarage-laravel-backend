@extends('admin.layouts.app')

@section('title', 'Редактировать вопрос FAQ')
@section('page-title', 'Редактировать вопрос FAQ')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Редактирование вопроса FAQ: {{ $question->key }}</h6>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.faq.questions.update', $question) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="faq_category_id" class="form-label">Категория <span class="text-danger">*</span></label>
                                <select class="form-select @error('faq_category_id') is-invalid @enderror" 
                                        id="faq_category_id" 
                                        name="faq_category_id" 
                                        required>
                                    <option value="">Выберите категорию</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('faq_category_id', $question->faq_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->translations->where('locale', 'ru')->first()->name ?? $category->key }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faq_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="key" class="form-label">Ключ <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('key') is-invalid @enderror" 
                                       id="key" 
                                       name="key" 
                                       value="{{ old('key', $question->key) }}" 
                                       placeholder="how_to_add_vehicle" 
                                       required>
                                <div class="form-text">Уникальный ключ для идентификации (только латинские буквы, цифры и подчеркивания)</div>
                                @error('key')
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
                                       value="{{ old('sort_order', $question->sort_order) }}" 
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
                                   {{ old('is_active', $question->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Активный
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
                                <label for="translations_en_question" class="form-label">Вопрос (EN) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('translations.0.question') is-invalid @enderror" 
                                       id="translations_en_question" 
                                       name="translations[0][question]" 
                                       value="{{ old('translations.0.question', $question->translations->where('locale', 'en')->first()->question ?? '') }}" 
                                       required>
                                <input type="hidden" name="translations[0][locale]" value="en">
                                @error('translations.0.question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_ru_question" class="form-label">Вопрос (RU) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('translations.1.question') is-invalid @enderror" 
                                       id="translations_ru_question" 
                                       name="translations[1][question]" 
                                       value="{{ old('translations.1.question', $question->translations->where('locale', 'ru')->first()->question ?? '') }}" 
                                       required>
                                <input type="hidden" name="translations[1][locale]" value="ru">
                                @error('translations.1.question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_uk_question" class="form-label">Вопрос (UK) <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('translations.2.question') is-invalid @enderror" 
                                       id="translations_uk_question" 
                                       name="translations[2][question]" 
                                       value="{{ old('translations.2.question', $question->translations->where('locale', 'uk')->first()->question ?? '') }}" 
                                       required>
                                <input type="hidden" name="translations[2][locale]" value="uk">
                                @error('translations.2.question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_en_answer" class="form-label">Ответ (EN) <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('translations.0.answer') is-invalid @enderror" 
                                          id="translations_en_answer" 
                                          name="translations[0][answer]" 
                                          rows="4" 
                                          required>{{ old('translations.0.answer', $question->translations->where('locale', 'en')->first()->answer ?? '') }}</textarea>
                                @error('translations.0.answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_ru_answer" class="form-label">Ответ (RU) <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('translations.1.answer') is-invalid @enderror" 
                                          id="translations_ru_answer" 
                                          name="translations[1][answer]" 
                                          rows="4" 
                                          required>{{ old('translations.1.answer', $question->translations->where('locale', 'ru')->first()->answer ?? '') }}</textarea>
                                @error('translations.1.answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="translations_uk_answer" class="form-label">Ответ (UK) <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('translations.2.answer') is-invalid @enderror" 
                                          id="translations_uk_answer" 
                                          name="translations[2][answer]" 
                                          rows="4" 
                                          required>{{ old('translations.2.answer', $question->translations->where('locale', 'uk')->first()->answer ?? '') }}</textarea>
                                @error('translations.2.answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.faq.questions.index') }}" class="btn btn-secondary">
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
