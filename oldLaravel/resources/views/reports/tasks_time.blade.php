@extends('layout')

@section('content')

<h1>Acciones por Usuarios</h1>

@include('reports.tasks_time.graph')	
@include('reports.tasks_time.filter')	
	<!--  
	*
	*    Tabla reportes
	*
	-->

<table class="table table-striped table-hover table-responsive">
	<tr>
		<th>Estados</th>
		@for($i=0; $i<count($users); $i++)
				
		<th>
			{{$users[$i]->name}}
		</th>
		@endfor
	</tr>
	@for($j=0; $j<count($task_statuses); $j++)
		
		<tr>
		<td>{{$task_statuses[$j]->name}}</td>
		@for($i=0; $i<count($users); $i++)
	
		<td>{{$data[$i][$j]}}</td>
		@endfor
	</tr>	
	@endfor
	
</table>


@endsection