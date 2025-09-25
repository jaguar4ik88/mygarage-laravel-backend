@extends('admin.layouts.app')

@section('title', 'Справочники авто')
@section('page-title', 'Справочники авто')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Справочники авто</h5>
    <div class="list-group">
      <a href="{{ route('admin.car-data.makers.index') }}" class="list-group-item list-group-item-action">Производители</a>
      <a href="{{ route('admin.car-data.models.index') }}" class="list-group-item list-group-item-action">Модели</a>
      <a href="{{ route('admin.car-data.engines.index') }}" class="list-group-item list-group-item-action">Двигатели</a>
    </div>
  </div>
</div>

<div class="card mt-3">
  <div class="card-body">
    <h5 class="mb-3">Импорт CSV (Марка,Модель,Двигатель)</h5>
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form method="post" action="{{ route('admin.car-data.import') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-2 align-items-center">
        <div class="col-auto">
          <input type="file" name="file" class="form-control" accept=".csv,text/csv" required>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary">Импортировать</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection


