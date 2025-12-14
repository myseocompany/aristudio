@extends('layout')

@section('content')
<h1>Show Metadata</h1>
<form method="POST" action="/metadatas/{{ $model->id}}/edit">
  {{ csrf_field() }}
  <div class="form-group">
    <label for="name"><strong>Name</strong></label>
    
    <div class="help-block">{{ $model->name}}</div>
  </div>
  <div class="form-group">
    <label for="description"><strong>Value</strong></label>    
    <div class="help-block">{{ $model->value }}</div>
  </div>
  
  <div>
    <a href="/metadatas/{{ $model->id}}/edit">Edit</a>
  </div>
  <!-- <button type="submit" class="btn btn-basic">Edit</button>
  -->
</form>
@endsection