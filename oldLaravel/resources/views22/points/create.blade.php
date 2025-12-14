@extends('layout')

@section('content')
<h1>Create Task</h1>
<form method="POST" action="/tasks">
{{ csrf_field() }}
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
  </div>
  <div class="form-group">
    <label for="email">Points</label>    
    <input type="text" class="form-control" id="points" name="points" placeholder="Points" required="required">
    <input type="text" class="form-control" id="description" name="description" placeholder="description" required="required">
    <input type="text" class="form-control" id="project_id" name="project_id" placeholder="project_id" required="required">
    <input type="text" class="form-control" id="user_id" name="user_id" placeholder="user_id" required="required">
    <input type="text" class="form-control" id="status_id" name="status_id" placeholder="status_id" required="required">


  </div>

  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection