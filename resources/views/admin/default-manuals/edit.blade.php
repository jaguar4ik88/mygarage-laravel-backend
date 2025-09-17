@extends('admin.layouts.app')

@section('title', 'Редактирование инструкции')
@section('page-title', 'Редактирование инструкции #'.$defaultManual->id)

@section('content')
<div class="row">
  <div class="col-12 col-lg-10">
    <div class="card shadow">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Форма редактирования</h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.default-manuals.update', $defaultManual) }}">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label class="form-label">Секция</label>
            <select name="manual_section_id" class="form-select" required>
              @foreach($sections as $section)
                <option value="{{ $section->id }}" {{ $defaultManual->manual_section_id==$section->id?'selected':'' }}>{{ $section->key }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">PDF путь (опционально)</label>
            <input type="text" name="pdf_path" class="form-control" value="{{ old('pdf_path', $defaultManual->pdf_path) }}" />
          </div>

          <div class="border rounded p-3">
            <h6 class="mb-3">Переводы</h6>
            @php($byLocale = $defaultManual->translations->keyBy('locale'))
            @foreach(['uk','en','ru'] as $locale)
              @php($tr = $byLocale->get($locale))
              <div class="mb-3">
                <div class="text-muted mb-1">{{ strtoupper($locale) }}</div>
                <input type="text" name="translations[{{$locale}}][locale]" value="{{$locale}}" hidden />
                <input type="text" name="translations[{{$locale}}][title]" placeholder="Заголовок" class="form-control mb-2" value="{{ old("translations.$locale.title", optional($tr)->title) }}" />
                <textarea name="translations[{{$locale}}][content]" placeholder="Контент" class="form-control" rows="4">{{ old("translations.$locale.content", optional($tr)->content) }}</textarea>
              </div>
            @endforeach
          </div>

          <div class="mt-3 d-flex gap-2">
            <a href="{{ route('admin.default-manuals.index') }}" class="btn btn-outline-secondary">Отмена</a>
            <button class="btn btn-primary">Сохранить</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection


