@extends('layout')

@section('content')
<h1>{{ $project->name}}</h1>
<form method="POST" action="/projects/{{ $project->id}}/edit">
  {{ csrf_field() }}
  
  <div class="form-group">
    <label for="description">Description</label>    
    <div class="help-block">{{ $project->description }}</div>
  </div>
  <div class="form-group">
    <label for="budget">Budget</label>
    <div class="help-block">{{ $project->budget }}</div>
     
  </div>
  <div class="form-group">
    <label for="start_date">Start Date</label>
    <div class="help-block">{{ $project->start_date }}</div>
      
  </div>
 <div class="form-group">
    <label for="finish_date">Finish Date</label>
    <div class="help-block">{{ $project->finish_date }}</div>    
  </div> 
  <button type="submit" class="btn btn-basic">Edit</button>
</form>
<!-- /// -->
<h2>Users</h2>
  <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr>
                  <td>{{ $user->id }}</td>
                  <td>{{ $user->name }}</td>
                </tr>
        @endforeach
              </tbody>
            </table>
          </div>

<!-- // -->

<!-- //form user -->
<div>
    
    <h2>Create User</h2>
  <form method="POST" action="/user">
{{ csrf_field() }}

  <div class="form-group">
    <label for="user_id">User</label>
    <select name="user_id" id="user_id" class="form-control">
    @foreach ($users as $user)
        <option value="{{$user->id}}">{{$user->name}}</option>
    @endforeach
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
  </div>


<!-- end form user -->

<div>
  <h2>Task</h2>

  <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Points</th>
                  <th>Due Date</th>
                  <th>User</th>
                   <th>Status</th>
                  <th>Edit</th>
                </tr>
              </thead>
              <tbody>
                @foreach($project->tasks as $item)
                <tr>
                  <td>{{ $item->id }}</td>
                  <td><a href="/tasks/{{ $item->id }}">{{ $item->name }}</a></td>
                  <td>{{ $item->points }}</td>
                  <td>{{ $item->due_date }}</td>
                  <td>{{ $item->user->name }}</td>
                   <td></td>
                  <td><a href="/tasks/{{$item->id }}/edit">Edit</a></td>
                </tr>
        @endforeach
              </tbody>
            </table>
          </div>

              <hr>
              <!-- diffForHumans() -->
  <div>
    
    <h2>Create Task</h2>
  <form method="POST" action="/tasks">
{{ csrf_field() }}
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
  </div>

  <div class="form-group">
    <label for="name">Due Date</label>
    <input type="date" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d');?>">
  </div>

  <div class="form-group">
    <label for="points">Points</label>    
    <input type="text" class="form-control" id="points" name="points" placeholder="Points" >
    <label for="points">Description</label>

    <textarea class="form-control" name="description" id="description" cols="30" rows="10"></textarea>

    <input type="hidden" name="project_id" id="project_id" class="form-control" value="{{$project->id}}">
    <input type="hidden" name="from" id="from" class="form-control" value="project">
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
     <select name="status_id" id="status_id" class="form-control" >
    @foreach($taskStatus as $key=>$value)
        <option value="{{$key}}">{{$value}}</option>
    @endforeach
    ?>
    </select>
  </div>
  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
  </div>
</div>
@endsection