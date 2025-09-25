@extends('admin.layouts.app')
@section('title','Редактировать модель')
@section('page-title','Редактировать модель')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="post" action="{{ route('admin.car-data.models.update', $model) }}">
      @csrf @method('put')
      <div class="mb-3">
        <label class="form-label">Производитель</label>
        <select name="car_maker_id" class="form-select" required>
          @foreach($makers as $m)
            <option value="{{ $m->id }}" @selected($model->car_maker_id==$m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="name" class="form-control" value="{{ $model->name }}" required>
      </div>
      <button class="btn btn-primary">Сохранить</button>
      <a href="{{ route('admin.car-data.models.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
  </div>
</div>
@endsection


