@extends('admin.layouts.app')

@section('title', 'Инструкция #'.$defaultManual->id)
@section('page-title', 'Просмотр инструкции #'.$defaultManual->id)

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Инструкция #{{ $defaultManual->id }}</h6>
        <div class="btn-group">
          <a href="{{ route('admin.default-manuals.edit', $defaultManual) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-pencil"></i> Редактировать</a>
          <form method="POST" action="{{ route('admin.default-manuals.destroy', $defaultManual) }}" onsubmit="return confirm('Удалить?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Удалить</button>
          </form>
        </div>
      </div>
      <div class="card-body">
        <div class="mb-3"><span class="text-muted">Секция:</span> <code>{{ optional($defaultManual->section)->key }}</code></div>
        @if($defaultManual->pdf_url)
          <div class="mb-3"><a class="btn btn-sm btn-outline-primary" href="{{ $defaultManual->pdf_url }}" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Открыть PDF</a></div>
        @endif
        <div class="row g-3">
          @foreach($defaultManual->translations as $tr)
            <div class="col-12 col-md-6 col-lg-4">
              <div class="border rounded p-3 h-100">
                <div class="fw-semibold mb-2">[{{ strtoupper($tr->locale) }}] {{ $tr->title }}</div>
                <div class="text-muted" style="white-space: pre-line;">{{ $tr->content }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


