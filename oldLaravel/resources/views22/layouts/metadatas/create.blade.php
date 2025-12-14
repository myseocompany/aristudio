@extends('layout')

@section('content')
<h1>Create Metadatas</h1>
<form method="POST" action="/metadatas">
{{ csrf_field() }}
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
  </div>
  <div class="form-group">
    <label for="points">Value</label>    
    <input type="text" class="form-control" id="value" name="value" placeholder="value" required="required">
  </div>

  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection