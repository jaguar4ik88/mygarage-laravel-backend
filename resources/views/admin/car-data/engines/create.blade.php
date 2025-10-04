@extends('admin.layouts.app')
@section('title','Добавить двигатель')
@section('page-title','Добавить двигатель')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="post" action="{{ route('admin.car-data.engines.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Производитель (ID)</label>
        <input type="number" name="car_maker_id" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Модель (ID)</label>
        <input type="number" name="car_model_id" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Метка</label>
        <input type="text" name="label" class="form-control" required>
      </div>
      <button class="btn btn-primary">Сохранить</button>
      <a href="{{ route('admin.car-data.engines.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
  </div>
</div>
@endsection


