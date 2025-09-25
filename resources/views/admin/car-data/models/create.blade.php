@extends('admin.layouts.app')
@section('title','Добавить модель')
@section('page-title','Добавить модель')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="post" action="{{ route('admin.car-data.models.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Производитель</label>
        <select name="car_maker_id" class="form-select" required>
          @foreach($makers as $m)
            <option value="{{ $m->id }}">{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <button class="btn btn-primary">Сохранить</button>
      <a href="{{ route('admin.car-data.models.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
  </div>
</div>
@endsection


