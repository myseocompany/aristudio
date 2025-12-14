@extends('layout')

@section('content')
<h1>{{ $model->name}}</h1>
<form method="POST" action="/projects/{{ $model->id}}/edit">
  {{ csrf_field() }}
  
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="description"><strong>Description</strong></label>    
        <div class="help-block">{!! nl2br($model->description) !!}</div>
      </div>
      
    </div>
 
    <div class="col-md-4">
    <div class="form-group">
        <label for="budget"><strong>Budget</strong></label>
        <div class="help-block">{{ $model->budget }}</div>
         
      </div>
      <div class="form-group">
    <label for="status"><strong>Status</strong></label>
    <div class="help-block">{{$model->getProjectStatusOptionsById() }}</div>    
  </div>
   <div class="form-group">
    <label for="status"><strong>Sales</strong></label>
    <div class="help-block">{{ $model->sales }}</div>    
  </div>

    <div class="form-group">
          <label for="start_date"><strong>Start Date</strong></label>
          <div class="help-block">{{ $model->start_date }}</div>
            
      <label for="finish_date"><strong>Finish Date</strong></label>
        <div class="help-block">{{ $model->finish_date }}</div>    
      </div>
      
      <div class="form-group">
        <label for="status"><strong>Type</strong></label>
        <div class="help-block">{{$model->getProjectTypeOptionsById() }}</div>    
      </div> 
      <div class="form-group">
        <label for="status"><strong>Lead Target</strong></label>
        <div class="help-block">{{ $model->lead_target }}</div>    
      </div> 
      <div class="form-group">
        <label for="status"><strong>Point Target</strong></label>
        <div class="help-block">{{ $model->monthly_points_goal }}</div>    
      </div> 
  
    </div>
  </div>
  <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Edit</button>
</form>




@include("projects.widget_files")
@include("projects.widget_logins")

<!--  m_id,c,r,u,d,l) m=6-> logins -->
@if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,1,0,0,0,0) == 1)
<form action="/project_logins" method="POST">
  {{ csrf_field() }}
  <h2>Create Login</h2>
  <div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="name"><strong>Name</strong></label>
      <input class="form-control" type="text" name="name">
    </div>

    <div class="form-group">
      <label for="url"><strong>URL</strong></label>
      <input class="form-control" type="text" name="url">
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label for="user"><strong>User</strong></label>
      <input class="form-control" type="text" name="user">
    </div>

    <div class="form-group">
      <label for="password"><strong>Password</strong></label>
      <input class="form-control" type="text" name="password">
    </div>
    <input type="hidden" name="project_id" id="project_id" value="{{$model->id}}">
  </div>

</div>
<input type="submit" name="" value="Submit" class="btn btn-sm btn-primary my-2 my-sm-0">

</form>
@endif







<div>
  <div>
    <h2>Users</h2>
  
  
  <form action="/projects/{{$model->id}}/addUser" method="POST">
     {{ csrf_field() }}
    <select name="user_id" id="user_id">
      <option value="">Select user...</option>
      @foreach ($pending_users as $item)
        {{-- expr --}}
      <option value="{{$item->id}}">{{$item->name}}</option>
      @endforeach
    </select>
  <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Submit</button>
  </form>
  </div>

  <div id="user-table" class="table-wrapper-scroll-y my-custom-scrollbar">
     <div class="table-responsive">
            <table class="table table-striped">
              <thead class="">
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th></th>
              </tr>
              </thead>
              @foreach ($model->users as $item)
              
              <tr>
                <td>{{$item->name}}</td>
                <td>@if(isset($item->role_id)){{$item->role->name}}@endif</td>
                <td><a href="/projects/{{$model->id}}/deleteUser/{{$item->id}}">Delete</a></td>
                
              </tr>
              
              @endforeach
            </table>
   
          <script>
    function copyPassword(id_elemento) {
       
  var aux = document.createElement("input");
  aux.setAttribute("value", document.getElementById(id_elemento).innerHTML);
  document.body.appendChild(aux);
  aux.select();
  document.execCommand("copy");
  document.body.removeChild(aux);
}
</script>
    
  </div>
</div>
   
@endsection