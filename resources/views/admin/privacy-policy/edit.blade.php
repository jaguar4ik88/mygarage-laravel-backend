@extends('admin.layouts.app')

@section('title', 'Редактировать раздел политики конфиденциальности')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Редактировать раздел политики конфиденциальности</h1>
                <a href="{{ route('admin.privacy-policy.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад к списку
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.privacy-policy.update', $privacyPolicy) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="language" class="form-label">Язык <span class="text-danger">*</span></label>
                                    <select class="form-select @error('language') is-invalid @enderror" 
                                            id="language" name="language" required>
                                        <option value="">Выберите язык</option>
                                        @foreach($languages as $code => $name)
                                            <option value="{{ $code }}" 
                                                    {{ (old('language', $privacyPolicy->language) == $code) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Раздел <span class="text-danger">*</span></label>
                                    <select class="form-select @error('section') is-invalid @enderror" 
                                            id="section" name="section" required>
                                        <option value="">Выберите раздел</option>
                                        @foreach($sections as $code => $name)
                                            <option value="{{ $code }}" 
                                                    {{ (old('section', $privacyPolicy->section) == $code) ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('section')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $privacyPolicy->title) }}" 
                                   placeholder="Введите заголовок раздела" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Содержимое <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="8" 
                                      placeholder="Введите содержимое раздела" required>{{ old('content', $privacyPolicy->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Порядок сортировки</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" name="sort_order" 
                                           value="{{ old('sort_order', $privacyPolicy->sort_order) }}" 
                                           min="0" placeholder="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_active" name="is_active" value="1" 
                                               {{ (old('is_active', $privacyPolicy->is_active)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Активен
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <small class="text-muted">
                                    Создано: {{ $privacyPolicy->created_at->format('d.m.Y H:i') }}<br>
                                    Обновлено: {{ $privacyPolicy->updated_at->format('d.m.Y H:i') }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.privacy-policy.index') }}" class="btn btn-secondary">
                                    Отмена
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
