@extends('admin.layouts.app')

@section('title', 'Редактировать совет')
@section('page-title', 'Редактировать совет')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Редактировать совет #{{ $adviceItem->id }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.advice-items.update', $adviceItem) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="advice_section_id" class="form-label">Секция <span class="text-danger">*</span></label>
                                <select class="form-select @error('advice_section_id') is-invalid @enderror" 
                                        id="advice_section_id" name="advice_section_id" required>
                                    <option value="">Выберите секцию</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" 
                                                {{ old('advice_section_id', $adviceItem->advice_section_id) == $section->id ? 'selected' : '' }}>
                                            {{ $section->titleGroup->translations->first()->title ?? $section->slug }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('advice_section_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Иконка</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                       id="icon" name="icon" value="{{ old('icon', $adviceItem->icon) }}" 
                                       placeholder="tint">
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
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $adviceItem->sort_order) }}" 
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
                                           {{ old('is_active', $adviceItem->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Активен
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
                                       value="{{ old('title_en', $adviceItem->titleGroup->translations->where('locale', 'en')->first()->title ?? '') }}" required>
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
                                       value="{{ old('title_ru', $adviceItem->titleGroup->translations->where('locale', 'ru')->first()->title ?? '') }}" required>
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
                                       value="{{ old('title_uk', $adviceItem->titleGroup->translations->where('locale', 'uk')->first()->title ?? '') }}" required>
                                @error('title_uk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3">Переводы содержания</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="content_en" class="form-label">Английский <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content_en') is-invalid @enderror" 
                                          id="content_en" name="content_en" rows="6" required>{{ old('content_en', $adviceItem->contentGroup->translations->where('locale', 'en')->first()->content ?? '') }}</textarea>
                                @error('content_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Используйте • для списков, \n для переносов строк</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="content_ru" class="form-label">Русский <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content_ru') is-invalid @enderror" 
                                          id="content_ru" name="content_ru" rows="6" required>{{ old('content_ru', $adviceItem->contentGroup->translations->where('locale', 'ru')->first()->content ?? '') }}</textarea>
                                @error('content_ru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="content_uk" class="form-label">Украинский <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('content_uk') is-invalid @enderror" 
                                          id="content_uk" name="content_uk" rows="6" required>{{ old('content_uk', $adviceItem->contentGroup->translations->where('locale', 'uk')->first()->content ?? '') }}</textarea>
                                @error('content_uk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.advice-items.index') }}" class="btn btn-secondary">
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
