@php
    $namedLocales = [
        'pl' => 'Польский',
        'pt' => 'Португальский',
        'ro' => 'Румынский',
    ];
    $translationRows = $translations ?? collect();
    $column = $column ?? $prefix;
    $multiline = $multiline ?? false;
@endphp
<div class="row">
    @foreach ($namedLocales as $code => $label)
        @php
            $existing = optional($translationRows->firstWhere('locale', $code))->{$column} ?? '';
            $inputId = $prefix.'_'.$code;
        @endphp
        <div class="col-md-4">
            <div class="mb-3">
                <label for="{{ $inputId }}" class="form-label">{{ $label }}</label>
                @if ($multiline)
                    <textarea class="form-control @error($inputId) is-invalid @enderror"
                              id="{{ $inputId }}"
                              name="{{ $inputId }}"
                              rows="6">{{ old($inputId, $existing) }}</textarea>
                @else
                    <input type="text"
                           class="form-control @error($inputId) is-invalid @enderror"
                           id="{{ $inputId }}"
                           name="{{ $inputId }}"
                           value="{{ old($inputId, $existing) }}">
                @endif
                @error($inputId)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    @endforeach
</div>
