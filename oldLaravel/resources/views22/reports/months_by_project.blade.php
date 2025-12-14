@extends('layout')

@section('content')

<h1>Points by Project Month</h1>
<?php // dd($time_span_array); ?>
@include('reports.months_by_project.graph')	
@include('reports.months_by_project.filter')	
	<!--  
	*
	*    Tabla reportes
	*
	-->

<table class="table table-striped table-hover table-responsive">
	<tr>
		<th>Team </th>
		@for($i=0; $i<$time_span; $i++)
		<th>
			<div class="report-month">{{ substr($time_span_array[$i][0], 5, 5) }}</div> 
			<div class="report-month">{{ substr($time_span_array[$i][1], 5, 5) }}</div>
		</th>
		@endfor
	</tr><?php $j = 0; ?>
	@foreach($projects as $item)
	<tr>
		<td>{{$item->name}}</td>
		@for($i=0; $i<$time_span; $i++)
		<td>{{$data[$j][$i]}}</td>
		@endfor
		<?php $j++; ?>
	</tr>	
	@endforeach
	
</table>


@endsection