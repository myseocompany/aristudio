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
		@for($i=0; $i<$weeks_diff; $i++)
		<th>
			<div>{{$weeks_array[$i]["start"]}}</div> 
			<div>{{$weeks_array[$i]["end"]}}</div>
		</th>
		@endfor
		<th>Subtotal</th>
	</tr><?php $j = 0; ?>
	@foreach($users_graph as $item)
	<?php $sum=0; ?>
	<tr>
		<td>{{$item->name}}</td>
		@for($i=0; $i<$weeks_diff; $i++)
		<td>@if(isset($data[$j][$i])) {{$data[$j][$i]}} @else 0 @endif</td>@if(isset($data[$j][$i])) <?php $sum += $data[$j][$i]; ?> @endif
		@endfor
		<td>{{$sum}}</td>
		<?php $j++; ?>
	</tr>	
	@endforeach
	
</table>


@endsection