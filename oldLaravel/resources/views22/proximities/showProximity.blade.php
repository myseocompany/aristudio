@extends('layout')

@section('content')
	<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Usuario</th>
      <th scope="col">Distancia</th>
      <th scope="col">Fecha</th>
    </tr>
  </thead>
  <tbody>
  	@foreach($model as $item)
    <tr>
      <th scope="row">{{$item->id}}</th>
      <td>@if(isset($item->user)){{$item->user->name}}@else {{$item->user_id}} @endif</td>
      <td>{{$item->distance}}</td>
      <td>{{$item->created_at}}</td>
    </tr>
	@endforeach    
  </tbody>
</table>

@endsection