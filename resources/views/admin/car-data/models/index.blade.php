@extends('admin.layouts.app')
@section('title','Модели')
@section('page-title','Модели')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <a href="{{ route('admin.car-data.models.create') }}" class="btn btn-primary">Добавить</a>
  <a href="{{ route('admin.car-data.makers.index') }}" class="btn btn-outline-secondary">Производители</a>
  <a href="{{ route('admin.car-data.engines.index') }}" class="btn btn-outline-secondary">Двигатели</a>
  <a href="{{ route('admin.car-data.index') }}#import" class="btn btn-outline-secondary">Импорт CSV</a>
  </div>
<div class="card">
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-auto">
        <select name="maker_id" class="form-select" onchange="this.form.submit()">
          <option value="">Все производители</option>
          @foreach($makers as $m)
            <option value="{{ $m->id }}" @selected($makerId==$m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-secondary">Фильтр</button>
      </div>
    </form>
    <table class="table table-striped">
      <thead><tr><th>ID</th><th>Производитель</th><th>Название</th><th class="text-end">Действия</th></tr></thead>
      <tbody>
        @foreach($models as $m)
        <tr>
          <td>{{ $m->id }}</td>
          <td>{{ optional($m->maker)->name ?? ('#'.$m->car_maker_id) }}</td>
          <td>{{ $m->name }}</td>
          <td class="text-end">
            <a href="{{ route('admin.car-data.models.edit', $m) }}" class="btn btn-sm btn-secondary">Редактировать</a>
            <form action="{{ route('admin.car-data.models.destroy', $m) }}" method="post" class="d-inline" onsubmit="return confirm('Удалить?')">
              @csrf @method('delete')
              <button class="btn btn-sm btn-danger">Удалить</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-container">
      {{ $models->appends(['maker_id'=>$makerId])->links('vendor.pagination.default') }}
    </div>
  </div>
 </div>
@endsection


