@extends('layout')

@section('content')
<h1>Users</h1>
  @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,1,0,0,0,0) == 1)
  <div><a href="/users/create">Create + <i class="fa fa-plus-square"></i></a></div>
  @endif
  <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>photo</th>
                  <th>User</th>
                  <th>Document</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th>Status</th>
                  @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,0,0,1,0,0) == 1)  
                  <th>Edit</th>
                  @endif
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                
                
                <tr>
                  <td>{{ $user->id }}</td>
                  <td>@if(isset($user->image_url))<img src="/laravel/storage/app/public/files/users/{{ $user->image_url }}" height="45"> @endif</td>


                   <td>
                    @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,0,0,1,0,0) == 1)  
                    <a href="/users/{{ $user->id }}">{{ $user->name }}<br>{{ $user->phone }} <br>{{ $user->position }}</a>
                  
                    @else
                     <a >{{ $user->name }}<br>{{ $user->phone }}<br>{{ $user->position }}</a>
                    @endif 

                  </td>

                  <td>{{ $user->document }}</td>
                  <td>{{ $user->email }} <br><br> {{ $user->address }}</td>
             
                   
                  <td>@if(isset($user->role_id)&&($user->role_id!=""))
                    {{ $user->role->name }} @endif</td>
                  <td>@if(isset($user->status_id)&&($user->status_id!=""))
                    {{ $user->status->name }} @endif</td>
                   @if (  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,0,0,1,0,0) == 1)  
                  <td><a href="/users/{{$user->id }}/edit" class="btn btn-sm btn-primary my-2 my-sm-0">Edit</a></td>
                   @endif 
                </tr>
        @endforeach
              </tbody>
            </table>
          </div>
@endsection