@extends('layout')

@section('content')
<h1>{{ $user->name}}</h1>
<form method="POST" action="/users/{{ $user->id}}/edit">
  {{ csrf_field() }}

<div class="form-row">
  <div class="form-group col-md-8">
    <div class="form-group">
      <label for="document"><strong>Document</strong></label>
      <div class="help-block">{{ $user->document}}</div>
    </div>
    <div class="form-group">
      <label for="description"><strong>Email</strong></label>    
      <div class="help-block">{{ $user->email }}</div>
    </div>
    <div class="form-group">
      <label for="description"><strong>Phone</strong></label>    
      <div class="help-block">{{ $user->phone }}</div>
    </div>
    <div class="form-group">
      <label for="description"><strong>Address</strong></label>    
      <div class="help-block">{{ $user->address }}</div>
    </div>
    <div class="form-group">
      <label for="description"><strong>Position</strong></label>    
      <div class="help-block">{{ $user->position }}</div>
    </div>

    <div class="row">
      <div class="form-group col-md-4">
        <label for="budget"><strong>Role</strong></label>
        <div class="help-block">{{ $user->role->name }}</div>
      </div>

      <div class="form-group col-md-4">
        <label for="budget"><strong>Status</strong></label>
        <div class="help-block">{{ $user->status->name }}</div>
      </div>

      <div class="form-group col-md-4">
        <button type="submit" class="btn btn-outline-success my-2 my-sm-0">Edit</button>
      </div>
    </div>
  
  </div>
  <div class="form-group col-md-4">
        @if(isset($user->image_url))<img src="/laravel/storage/app/public/files/users/{{ $user->image_url }}" height="200px"> @endif
  </div>
</div>
</form>


<form action="/projects/{{$user->id}}/addProject" method="POST">
  {{ csrf_field() }}
  <h2>Projects</h2>
  
  <div class="row">
      <div class="form-group col-md-6">
        <select name="project_id" id="project_id" class="form-control">
          <option value="">Select project...</option>
            @foreach ($projects as $item)
              {{-- expr --}}
            <option value="{{$item->id}}">{{$item->name}}</option>
            @endforeach
        </select>
      </div>
      <div class="form-group col-md-6">
        <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Submit</button>
      </div>
  </div>
</form>


  <div id="user-table" class="table-wrapper-scroll-y my-custom-scrollbar">
     <div class="table-responsive">
      <table class="table table-striped">
        <thead class="">
        <tr>
          <th>Name</th>
          <th></th>
        </tr>
        </thead>
        @foreach ($project_users as $item)
        <tr>
          <td>{{$item->name}}</td>
          <td><a href="/projects/{{$item->id}}/deleteProject/{{$user->id}}">Delete</a></td>
        </tr>
        @endforeach
      </table>
  </div>
</div>
@endsection