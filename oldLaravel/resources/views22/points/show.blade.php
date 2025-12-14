@extends('layout')

@section('content')
<h1>Show Task</h1>
<form method="POST" action="/tasks/{{ $model->id}}/edit">
  {{ csrf_field() }}
  <div class="form-group">
    <label for="name"><strong>Name</strong></label>
    
    <div class="help-block">{{ $model->name}}</div>
  </div>
  <div class="form-group">
    <label for="description"><strong>Description</strong></label>    
    <div class="help-block">{{ $model->email }}</div>
  </div>
  
  
  <button type="submit" class="btn btn-basic">Edit</button>
</form>
@endsection