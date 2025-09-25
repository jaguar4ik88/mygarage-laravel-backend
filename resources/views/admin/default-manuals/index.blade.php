@extends('admin.layouts.app')

@section('title', 'Дефолтные инструкции')
@section('page-title', 'Управление дефолтными инструкциями')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Список дефолтных инструкций</h6>
        <a href="{{ route('admin.default-manuals.create') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-plus"></i> Добавить инструкцию
        </a>
      </div>

      <div class="card-body">
        <form method="GET" class="mb-4">
          <div class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label">Секция</label>
              <select name="section_id" class="form-select">
                <option value="">Все секции</option>
                @foreach($sections as $section)
                  <option value="{{ $section->id }}" {{ request('section_id')==$section->id?'selected':'' }}>
                    {{ $section->key }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Поиск по названию</label>
              <input type="text" name="search" value="{{ request('search') }}" class="form-control" />
            </div>
            <div class="col-md-4 text-end">
              <button class="btn btn-outline-primary me-2"><i class="bi bi-search"></i> Поиск</button>
              <a href="{{ route('admin.default-manuals.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Сбросить</a>
            </div>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Секция</th>
                <th>Заголовки</th>
                <th style="width: 120px;">Действия</th>
              </tr>
            </thead>
            <tbody>
              @forelse($manuals as $manual)
                <tr>
                  <td>{{ $manual->id }}</td>
                  <td><code>{{ optional($manual->section)->key }}</code></td>
                  <td>
                    @foreach($manual->translations as $tr)
                      <span class="badge bg-light text-dark me-1">[{{ strtoupper($tr->locale) }}] {{ $tr->title }}</span>
                    @endforeach
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <a href="{{ route('admin.default-manuals.show', $manual) }}" class="btn btn-outline-info" title="Просмотр"><i class="bi bi-eye"></i></a>
                      <a href="{{ route('admin.default-manuals.edit', $manual) }}" class="btn btn-outline-warning" title="Редактировать"><i class="bi bi-pencil"></i></a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">Инструкции не найдены</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($manuals->hasPages())
          <div class="d-flex justify-content-center mt-4">
            {{ $manuals->withQueryString()->links('vendor.pagination.default') }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection


