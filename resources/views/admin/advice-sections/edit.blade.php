@extends('admin.layouts.app')

@section('title', 'Редактировать секцию советов')
@section('page-title', 'Редактировать секцию советов')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Редактировать секцию: {{ $adviceSection->slug }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.advice-sections.update', $adviceSection) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $adviceSection->slug) }}" required>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Уникальный идентификатор секции</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Иконка</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', $adviceSection->icon) }}" 
                                       placeholder="calendar-week">
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Название иконки Bootstrap Icons</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Порядок сортировки <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $adviceSection->sort_order) }}" 
                                       min="0" required>
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active', $adviceSection->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Активна
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Переводы названия</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="title_en" class="form-label">Английский <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title_en') is-invalid @enderror" 
                                       id="title_en" name="title_en" 
                                       value="{{ old('title_en', $adviceSection->titleGroup->translations->where('locale', 'en')->first()->title ?? '') }}" required>
                                @error('title_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="title_ru" class="form-label">Русский <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title_ru') is-invalid @enderror" 
                                       id="title_ru" name="title_ru" 
                                       value="{{ old('title_ru', $adviceSection->titleGroup->translations->where('locale', 'ru')->first()->title ?? '') }}" required>
                                @error('title_ru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="title_uk" class="form-label">Украинский <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title_uk') is-invalid @enderror" 
                                       id="title_uk" name="title_uk" 
                                       value="{{ old('title_uk', $adviceSection->titleGroup->translations->where('locale', 'uk')->first()->title ?? '') }}" required>
                                @error('title_uk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.advice-sections.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Назад
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Сохранить
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
