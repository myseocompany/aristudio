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
    <label for="name">Due Date:</label>
    <input type="date" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d');?>">
  </div>

  <div class="form-group">
    <label for="points">Points</label>    
    <input type="text" class="form-control" id="points" name="points" placeholder="Points" >
    <label for="points">Description</label>
     <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>

  </div>


  <div class="form-group">
    <label for="project_id">Project</label>
    <select name="project_id" id="project_id" class="form-control">
    @foreach ($projects as $project)
        <option value="{{$project->id}}">{{$project->name}}</option>
    @endforeach
    </select>
  </div>

  <div class="form-group">
    <label for="user_id">User</label>
    <select name="user_id" id="user_id" class="form-control">
    @foreach ($users as $user)
        <option value="{{$user->id}}">{{$user->name}}</option>
    @endforeach
    </select>
  </div>

  <div class="form-group">
   <label for="status_id">Status</label>
     <select name="status_id" id="task_status_id" class="form-control">
    @foreach ($task_status as $status)
        <option value="{{$status->id}}">{{$status->name}}</option>
    @endforeach
    </select>
  </div>
  
  
  <button type="submit" class="btn btn-sum btn-primary my-2 my-sm-0">Submit</button>
</form>
@endsection