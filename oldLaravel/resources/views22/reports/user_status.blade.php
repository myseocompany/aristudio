@extends('layout')

@section('content')

<h1>Estados por usuarios </h1>

@include('reports.user_status.graph')	
@include('reports.user_status.filter')	
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
	@for($j=0; $j<count($customer_statuses); $j++)
		
		<tr>
		<td>{{$customer_statuses[$j]->name}}</td>
		@for($i=0; $i<count($users); $i++)
	
		<td>{{$data[$i][$j]}}</td>
		@endfor
	</tr>	
	@endfor
	
</table>


@endsection