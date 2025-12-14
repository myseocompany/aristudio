@extends('layout')

@section('content')
<h1>Metadatas</h1>
  <div><a href="/metadatas/create">+Create</a></div>
	<div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Value</th>
                  <th>Edit</th>
                </tr>
              </thead>
              <tbody>
                @foreach($model as $item)
                <tr>
                  <td>{{ $item->id }}</td>
                  <td><a href="/metadatas/{{ $item->id }}">{{ $item->name }}</a></td>
                  <td>{{$item->value }}</td>
                  <td><a href="/metadatas/{{$item->id }}/edit">Edit</a></td>
                </tr>
 				@endforeach
              </tbody>
            </table>
          </div>
@endsection