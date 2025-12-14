@extends('layout')

@section('content')
	<h1>Projects</h1>
	<div><a href="/projects/create">+Create</a></div>
	<div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Proyect</th>
                  <th>Start Date</th>
                  <th>Finish Date</th>
                  <th>Budgets</th>
                  <th>Pieces per week</th>
                  <!-- <th>Finished designs</th> -->
                  <th>Edit</th>
                </tr>
              </thead>
              <tbody>

                
                @foreach($projects as $project)
                
                <tr>
                  <td>{{ $project->id }}</td>
                  <td><a class="proyect-{{$project->id}}" href="/projects/{{ $project->id }}">{{ $project->name }}</a></td>
                  <td>{{ $project->start_date }}</td>
                  <td>{{ $project->finish_date }}</td>
                  <td>{{ $project->budget }}</td>
                  <td>{{ $project->pieces_week }}</td>
                  <!-- <td>{{ $project->finish_desing }}</td> -->
                  <td><a href="/projects/{{ $project->id }}/edit">Edit</a></td>
                </tr>
 				@endforeach
              </tbody>
            </table>
          </div>
@endsection