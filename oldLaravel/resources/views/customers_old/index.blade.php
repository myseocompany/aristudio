@extends('layout')

@section('content')
<h1>Users</h1>
  <div><a href="/users/create">Create + <i class="fa fa-plus-square"></i></a></div>
  <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Empresa</th>
                  <th>Fecha de creación</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($model as $item)
                <tr>
                  <td>{{ $item->id }}</td>
                  <td><a href="/customers/{{ $item->id }}">{{ $item->name }}</a></td>
                  <td>{{ $item->enterprise_id }}</td>
                  <td>{{ $item->created_at }}</td>
                  <td><a href="/customers/{{$item->id }}/edit" class="btn btn-sm btn-primary my-2 my-sm-0">Editar</a></td>
                  <td><a href="/customers/{{$item->id }}/destroy" class="btn btn-sm btn-primary my-2 my-sm-0">Eliminar</a></td>
                </tr>
        @endforeach
              </tbody>
            </table>
          </div>
@endsection