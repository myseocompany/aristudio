@extends('layout')

@section('content')
<h1>Edit Project</h1>
<form method="POST" action="/projects/{{$project->id}}/update">
{{ csrf_field() }}
  
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="{{$project->name}}">
  </div>
  <div class="form-group">
    <label for="description">Description</label>    
    <textarea class="form-control" name="description" id="description" name="description" cols="30" rows="10">{{$project->description}}</textarea>
  </div>
  <div class="form-group">
    <label for="budget">Budget</label>
    <input type="text" class="form-control" id="budget" name="budget" placeholder="budget" value="{{$project->budget}}">   
  </div>
  <div class="form-group">
    <label for="start_date">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date" 
    value="{{ $project->getDateInput($project->start_date) }}">    
  </div>
    
 <div class="form-group">
    <label for="finish_date">Finish Date</label>
    <input type="date" class="form-control" id="finish_date" name="finish_date" value="{{ $project->getDateInput($project->finish_date) }}">  
  </div>
   
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection