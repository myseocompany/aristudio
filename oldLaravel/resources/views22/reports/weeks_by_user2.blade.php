@extends('layout')

@section('content')

<h1>Points by Users Week</h1>

@include('reports.weeks_by_user.graph')	
@include('reports.weeks_by_user.filter')	
	<!--  
	*
	*    Tabla reportes
	*
	-->

<table class="table table-striped table-hover table-responsive">
	<tr>
		<th>Team </th>
		@for($i=0; $i<$weeks; $i++)
		<th>
			<div>{{$weeks_array[$i][0]}}</div> 
			<div>{{$weeks_array[$i][1]}}</div>
		</th>
		@endfor
		<th>Subtotal</th>
	</tr><?php $j = 0; ?>
	@foreach($users as $item)
	<?php $sum=0; ?>
	<tr>
		<td>{{$item->name}}</td>
		@for($i=0; $i<$weeks; $i++)
		<td>{{$data[$j][$i]}}</td><?php $sum += $data[$j][$i]; ?>
		@endfor
		<td>{{$sum}}</td>
		<?php $j++; ?>
	</tr>	
	@endforeach
	
</table>


@endsection