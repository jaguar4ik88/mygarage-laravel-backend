@extends('admin.layouts.app')
@section('title','Двигатели')
@section('page-title','Двигатели')
@section('content')
<div class="d-flex justify-content-between mb-3">
  <a href="{{ route('admin.car-data.engines.create') }}" class="btn btn-primary">Добавить</a>
  <a href="{{ route('admin.car-data.makers.index') }}" class="btn btn-outline-secondary">Производители</a>
  <a href="{{ route('admin.car-data.models.index') }}" class="btn btn-outline-secondary">Модели</a>
  <a href="{{ route('admin.car-data.index') }}#import" class="btn btn-outline-secondary">Импорт CSV</a>
  </div>
<div class="card">
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-auto">
        <label class="form-label">Производитель</label>
        <select name="maker_id" class="form-select" onchange="this.form.submit()">
          <option value="">Все</option>
          @foreach($makers as $m)
            <option value="{{ $m->id }}" @selected($makerId==$m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
    </form>
    <table class="table table-striped">
      <thead><tr><th>ID</th><th>Производитель</th><th>Модель</th><th>Метка</th><th class="text-end">Действия</th></tr></thead>
      <tbody>
        @foreach($engines as $e)
        <tr>
          <td>{{ $e->id }}</td>
          <td>{{ optional($e->maker)->name ?? ('#'.$e->car_maker_id) }}</td>
          <td>{{ optional($e->model)->name ?? ('#'.$e->car_model_id) }}</td>
          <td>{{ $e->label }}</td>
          <td class="text-end">
            <a href="{{ route('admin.car-data.engines.edit', $e) }}" class="btn btn-sm btn-secondary">Редактировать</a>
            <form action="{{ route('admin.car-data.engines.destroy', $e) }}" method="post" class="d-inline" onsubmit="return confirm('Удалить?')">
              @csrf @method('delete')
              <button class="btn btn-sm btn-danger">Удалить</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-container">
      {{ $engines->appends(['maker_id'=>$makerId,'model_id'=>$modelId])->links('vendor.pagination.default') }}
    </div>
  </div>
 </div>
@endsection


