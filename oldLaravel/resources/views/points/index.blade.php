@extends('layout')

@section('content')
<h1>Tasks</h1>
  <div><a href="/tasks/create">+Create</a></div>
	<div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Points</th>
                  <th>Project</th>
                  <th>User</th>
                  <th>Edit</th>
                </tr>
              </thead>
              <tbody class="dd">
                @foreach($model as $item)
                <tr class="dd-list">
                  <td class="dd-item">{{ $item->id }}</td>
                  <td class="dd-item"><a href="/tasks/{{ $item->id }}">{{ $item->name }}</a></td>
                  <td class="dd-item">{{ $item->points }}</td>
                  <td class="dd-item">{{ $item->project_id }}</td>
                  <td class="dd-item">{{ $item->user_id }}</td>
                  <td class="dd-item"><a href="/tasks/{{$item->id }}/edit">Edit</a></td>
                </tr>
 				@endforeach
              </tbody>
            </table>
          </div>
@endsection