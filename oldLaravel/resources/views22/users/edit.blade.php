@extends('layout')

@section('content')
<h1>Edit Users</h1>
<form method="POST" action="/users/{{$model->id}}/update" enctype="multipart/form-data">
{{ csrf_field() }}
  
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="{{$model->name}}">
  </div>
  <div class="form-group">
    <label for="document">Document</label>
    <input type="text" class="form-control" id="document" name="document" placeholder="Document" required="required" value="{{$model->document}}">
  </div>
  <div class="form-group">
    <label for="description">Email</label>    
   
    <input type="text" class="form-control" id="email" name="email" placeholder="email" required="required" value="{{$model->email}}">
  </div>
   <div class="form-group">
    <label for="description">Phone</label>    
   
    <input type="text" class="form-control" id="phone" name="phone" placeholder="phone" required="required" value="{{$model->phone}}">
  </div>
  <div class="form-group">
    <label for="description">Address</label>    
   
    <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="{{$model->address}}">
  </div>
  <div class="form-group">
    <label for="description">Position</label>    
   
    <input type="text" class="form-control" id="position" name="position" placeholder="position" required="required" value="{{$model->position}}">
  </div>
  <div class="form-group">
    <label for="budget">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="passwords" value="{{$model->password}}">   
  </div>
    <div class="form-group">
    <label for="budget">Role</label>
    <select class="form-control" name="role_id" id="role_id">
      @foreach($role as $item)
      <option value="{{$item->id}}" @if($item->id==$model->role_id)selected="selected" @endif>{{$item->name}}</option>
      @endforeach
    </select>
  </div>
  <div class="form-group">
    <label for="budget">Status</label>
    <select class="form-control" name="status_id" id="status_id">
      @foreach($user_statuses as $item)
      <option value="{{$item->id}}" @if($item->id==$model->status_id)selected="selected" @endif>{{$item->name}}</option>
      @endforeach
    </select>
  </div>


  <div class="form-group">
    <label for="color">Color</label>
    <input type="text" class="form-control" id="color" name="color" placeholder="color" value="{{$model->color}}">   
  </div>

  <div class="form-group">
    <label for="image_url">Foto</label>    
    <input type="file" id="image_url" name="image_url" placeholder="Foto">
  </div>

  <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endsection