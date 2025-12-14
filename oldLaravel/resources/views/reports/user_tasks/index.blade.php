@extends('layout')

@section('content')

<h1>Users by status</h1>
	
	<!--  
	*
	*    Tabla reportes
	*
	-->
@include('reports.user_tasks.filter')	
<?php 
	$sum_points = 0; 
	$total_points = 0;
	$count_statuses = 0;
	$total_debt = 0;
	$initial_debt = 0;
		
  	$url_charge_account= "charge_account?filter=$request->filter&from_date=$request->from_date&to_date=$request->to_date&project_id=$request->project_id&status_id=56&type_id=$request->type_id&subtype_id=$request->subtype_id&querystr=$request->querystr&priority=$request->priority";
?>
<br>
	<div class="report">
		<table  class="table table-bordered table-sm table-hover">
			<tr>
				
				<th class="title">Users</th>
				
				<th class="input_bg text-center">Debt</th>
				
				
				<script> 
					statuses_color = []; 
					statuses_label_color = []; 
				</script>
			@foreach($task_statuses as $item)
				<th class="text-center" style="background-color:{{$item->background_color}};color:white">{{$item->alias}}</th>
				<script>
					statuses_color[{{$item->id}}] = "{{$item->background_color}}"; 
					statuses_label_color[{{$item->id}}] = "{{$item->color}}"; 
				</script>
				@php $count_statuses++ @endphp
			@endforeach
				<th class="text-center title">Total points</th>	
				<th class="input_bg text-center">Daily Goal</th>
				<th class="text-center title">Acumulated</th>
				<!-- <th>Charge Account</th> -- -->
			</tr>
			
			@foreach($users as $user)
			<tr>  
				<td class="subtitle"><a href="/tasks/?from_date={{$request->from_date}}&to_date={{$request->to_date}}&user_id={{$user->id}}">{{ $user->name }}</a></td>
				<?php 
				$count = $user->countTaskInventoryBydDate($request->from_date); 
				$initial_debt += $count;
				
				?>

				<td class="text-center">@if($count>0){{ $count  }} @endif</td>
			<?php 
				$data= $user->countTaskByStatusAndDates(
						$task_statuses, 
						$request);
				
			 ?>
			 <script> cont=0; </script>
			 	
			@foreach($task_statuses as $subitem)

				<td class="text-center" id="{{$user->id}}_{{$subitem->id}}" 
				@if($data[$subitem->id][1]!=null || $data[$subitem->id][0]!= null)
					style="background-color:{{$subitem->background_color}};color:white;"
					@endif >
						
						@if(isset($data[$subitem->id])  )
							<?php 
							if($subitem->id == 6 || $subitem->id == 56)
								$sum_points += $data[$subitem->id][0] ?>
							<div id="task_points">
								<a style="color:white;" id="task_points_{{$user->id}}_{{$subitem->id}}" href="/tasks/?from_date={{$request->from_date}}&to_date={{$request->to_date}}&user_id={{$user->id}}&status_id={{$subitem->id}}">
									@if($subitem->id != 6 && $subitem->id != 56)
									[{{$data[$subitem->id][1]}}]
									@else
									{{$data[$subitem->id][0]}} 
									@endif
								</a>
							</div>
							
							<script>
							document.getElementById("{{$user->id}}_{{$subitem->id}}").style.backgroundColor = statuses_color[{{$subitem->id}}];
							document.getElementById("task_points_{{$user->id}}_{{$subitem->id}}").style.color = statuses_label_color[{{$subitem->id}}];
							
							</script>
						@endif


				</td>
			@endforeach
				
				<td class="text-center">{{$sum_points}}</td>
				<td class="text-center" >{{$user->daily_goal}}</td>
				<?php 
				$total_points += $sum_points;
				$sum_points = 0; 
				$count = $user->countTaskInventoryBydDate($request->to_date);
				$total_debt += $count;
				
				?>
				
				<td class="text-center">@if($count>0){{$count}}@endif</td>
				<!--
				<td>
					<a href="/{{$url_charge_account}}&user_id={{$user->id}}" target="_blanck">Download</a>
				</td>
				-->
			</tr>
			
			@php 
			$total_goal += $user->daily_goal ;
			$total_debt += $user->daily_goal ;
			
				
			@endphp
			@endforeach()
			<tr>
				<td>Total</td>
				<td class="text-center">{{$initial_debt}}</td>
				<td colspan="{{ $count_statuses}}"></td>
				<td class="text-center">{{$total_points}}</td>
				<td class="text-center">{{$total_goal}}</td>
				<td colspan="1"></td>
			</tr>
		</table>	

</div>


@endsection