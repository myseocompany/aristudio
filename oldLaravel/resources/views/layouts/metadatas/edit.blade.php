@extends('layout')

@section('content')
<h1>Edit Metadata</h1>
<form method="POST" action="/metadatas/{{$model->id}}/update">
{{ csrf_field() }}
  
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="{{$model->name}}">
  </div>
  <div class="form-group">
    <label for="description">Value</label>    
   
    <input type="text" class="form-control" id="value" name="value" placeholder="value" required="required" value="{{$model->value}}">
  </div>
  
  
   
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection