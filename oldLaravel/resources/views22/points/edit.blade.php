@extends('layout')

@section('content')
<h1>Edit Users</h1>
<form method="POST" action="/users/{{$user->id}}/update">
{{ csrf_field() }}
  
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="{{$user->name}}">
  </div>
  <div class="form-group">
    <label for="description">Email</label>    
   
    <input type="text" class="form-control" id="email" name="email" placeholder="email" required="required" value="{{$user->email}}">
  </div>
  <div class="form-group">
    <label for="budget">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="passwords" value="{{$user->password}}">   
  </div>
 
  
   
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection