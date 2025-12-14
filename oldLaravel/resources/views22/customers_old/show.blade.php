@extends('layout')

@section('content')
<h1>Show User</h1>
<form method="POST" action="/users/{{ $user->id}}/edit">
  {{ csrf_field() }}
  <div class="form-group">
    <label for="name"><strong>Name</strong></label>
    
    <div class="help-block">{{ $user->name}}</div>
  </div>
  <div class="form-group">
    <label for="description"><strong>Email</strong></label>    
    <div class="help-block">{{ $user->email }}</div>
  </div>
  
  
  <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Edit</button>
</form>
@endsection