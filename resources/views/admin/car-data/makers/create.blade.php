@extends('admin.layouts.app')
@section('title','Добавить производителя')
@section('page-title','Добавить производителя')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="post" action="{{ route('admin.car-data.makers.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Название</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <button class="btn btn-primary">Сохранить</button>
      <a href="{{ route('admin.car-data.makers.index') }}" class="btn btn-secondary">Отмена</a>
    </form>
  </div>
</div>
@endsection


