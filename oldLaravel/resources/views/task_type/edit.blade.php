@extends('layout')

@section('content')
<h1>Edig Task Types</h1>

<div class="container">
  <form action="/task_types/{{$model->id}}/update" method="POST">
    {{ csrf_field() }}
    <select id="parent_id" name="parent_id">
      <option>
        Seleccione una opcion
      </option>
      @foreach($options as $item)
      <option value="{{$item->id}}" @if($model->parent_id==$item->id) selected="selected" @endif>{{$item->name}}</option>
      @endforeach
    </select>

    <input type="" name="name" value="{{$model->name}}">
    <input type="submit" name="">
    
  </form>
</div>

@endsection