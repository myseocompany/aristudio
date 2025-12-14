@extends('layout')

@section('content')

<div style="margin-top: 20px;"><h1>Editar acción {{$model->name}}</h1></div>
<div class="card-block">
  <form method="POST" action="/action_type/{{$model->id}}/update">
  {{ csrf_field() }}
  <div class="form-inline">
    <div class="form-group">
      <label for="name" style="margin-right: 10px;">Nombre:</label>
      <input type="text" class="form-control" id="name" name="name" style="margin-right: 10px;" value="{{$model->name}}" />
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
  </div>
  </form>
  </div>

@endsection