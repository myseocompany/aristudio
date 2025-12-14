@extends('layout')

@section('content')
<h1>Create Project</h1>
<form method="POST" action="/projects">
{{ csrf_field() }}
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
  </div>
  <div class="form-group">
    <label for="description">Description</label>    
    <textarea class="form-control" name="description" id="description" name="description" cols="30" rows="10"></textarea>
  </div>
  <div class="form-group">
    <label for="budget">Budget</label>
    <input type="text" class="form-control" id="budget" name="budget" placeholder="budget">    
  </div>
  <div class="form-group">
    <label for="start_date">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date" placeholder="YYYY-MM-DD" value="<?php echo date_create('now')->format('Y-m-d'); ?>">    
  </div>
 <div class="form-group">
    <label for="finish_date">Finish Date</label>
    <input type="date" class="form-control" id="finish_date" name="finish_date" placeholder="YYYY-MM-DD" value="<?php echo date_create('now')->format('Y-m-d'); ?>">    
  </div> 
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection