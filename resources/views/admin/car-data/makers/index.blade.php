@extends('admin.layouts.app')
@section('title','Производители')
@section('page-title','Производители')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <a href="{{ route('admin.car-data.makers.create') }}" class="btn btn-primary">Добавить</a>
  <a href="{{ route('admin.car-data.index') }}" class="btn btn-outline-secondary">К табам</a>
  <a href="{{ route('admin.car-data.models.index') }}" class="btn btn-outline-secondary">Модели</a>
  <a href="{{ route('admin.car-data.engines.index') }}" class="btn btn-outline-secondary">Двигатели</a>
  <a href="{{ route('admin.car-data.index') }}#import" class="btn btn-outline-secondary">Импорт CSV</a>
  </div>
<div class="card">
  <div class="card-body">
    <table class="table table-striped">
      <thead><tr><th>ID</th><th>Название</th><th class="text-end">Действия</th></tr></thead>
      <tbody>
        @foreach($makers as $m)
        <tr>
          <td>{{ $m->id }}</td>
          <td>{{ $m->name }}</td>
          <td class="text-end">
            <a href="{{ route('admin.car-data.makers.edit', $m) }}" class="btn btn-sm btn-secondary">Редактировать</a>
            <form action="{{ route('admin.car-data.makers.destroy', $m) }}" method="post" class="d-inline" onsubmit="return confirm('Удалить?')">
              @csrf @method('delete')
              <button class="btn btn-sm btn-danger">Удалить</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-container">
      {{ $makers->links('vendor.pagination.default') }}
    </div>
  </div>
 </div>
@endsection


