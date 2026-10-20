@php
    $extraLocales = [
        3 => ['pl', 'Польский (PL)'],
        4 => ['pt', 'Португальский (PT)'],
        5 => ['ro', 'Румынский (RO)'],
    ];
    $translationRows = $translations ?? collect();
    $fields = $fields ?? ['title'];
    $fieldLabels = [
        'title' => 'название',
        'name' => 'название',
        'question' => 'вопрос',
        'answer' => 'ответ',
    ];
@endphp
@foreach ($extraLocales as $index => [$code, $label])
    <div class="row">
        @foreach ($fields as $field)
            @php
                $existing = optional($translationRows->firstWhere('locale', $code))->{$field} ?? '';
                $inputName = 'translations['.$index.']['.$field.']';
                $oldKey = 'translations.'.$index.'.'.$field;
            @endphp
            <div class="col-md-{{ count($fields) > 1 ? '6' : '4' }}">
                <div class="mb-3">
                    <label class="form-label" for="translations_{{ $code }}_{{ $field }}">
                        {{ $label }}@if(count($fields) > 1) — {{ $fieldLabels[$field] ?? $field }}@endif
                    </label>
                    @if ($field === 'answer' || $field === 'content')
                        <textarea class="form-control"
                                  id="translations_{{ $code }}_{{ $field }}"
                                  name="{{ $inputName }}"
                                  rows="4">{{ old($oldKey, $existing) }}</textarea>
                    @else
                        <input type="text"
                               class="form-control"
                               id="translations_{{ $code }}_{{ $field }}"
                               name="{{ $inputName }}"
                               value="{{ old($oldKey, $existing) }}">
                    @endif
                    @if ($loop->first)
                        <input type="hidden" name="translations[{{ $index }}][locale]" value="{{ $code }}">
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endforeach
