@extends('layout')

@section('content')

<h1>Projects by state</h1>


	
	<!--  
	*
	*    Tabla reportes
	*
	-->

@include('reports.projects.filter')	

<?php 
	$sum_points = 0; 
 ?>
<br>
	<div class="report">
		<table  class="table table-bordered table-sm table-hover">
			<tr>
			<script> 
				statuses_color = []; 
				statuses_label_color = []; 
			</script>	
				<th class="title">Projects</th>
				<th class="input_bg text-center">Task at<br>Start</th>
				<th class="input_bg text-center">Montly<br>Goal</th>
			
			@foreach($task_statuses as $item)
				<th class="text-center" style="background-color:{{$item->background_color}}">{{$item->alias}}</th>
				<script> 
					statuses_color[{{$item->id}}] = "{{$item->background_color}}"; 
					statuses_label_color[{{$item->id}}] = "{{$item->color}}";
				</script>
				<?php 
							$monthly_points_goal = $project->monthly_points_goal;
						?>
			@endforeach
				
				<th class="text-center title">Debt</th>
				<th class="input_bg text-center">Total</th>
				<th class="input_bg text-center">Goal<br> %</th>
				
			</tr>
			
			@foreach($projects as $project)
			<tr>  
				<td class="subtitle"><a href="/tasks/?from_date={{$request->from_date}}&to_date={{$request->to_date}}&project_id={{$project->id}}&user_id={{$request->user_id}}">{{ $project->name }}</a></td>
				<td class="text-center"><?php $count = $project->countTaskInventoryBydDate($request->from_date); ?>@if($count>0){{ $count  }} @endif</td>
				<td class="text-center">{{$project->monthly_points_goal}}</td>
			<?php 
				$data= $project->countTaskByStatusAndDates($task_statuses, $request);
			 ?>
			 
			@foreach($task_statuses as $subitem)
			
				<td class="text-center" id="{{$project->id}}_{{$subitem->id}}">
						
						@if(isset($data[$subitem->id]))

						<?php 
							if($subitem->id != 4 || $subitem->id !=5 || $subitem->id !=7 || $subitem->id !=3  || $subitem->id !=57 || $subitem->id !=59){
								$sum_points += $data[$subitem->id][0];
							}
						?>
							<div id="task_points">
								<a id="task_points_{{$project->id}}_{{$subitem->id}}" href="/tasks/?from_date={{$request->from_date}}&to_date={{$request->to_date}}&project_id={{$project->id}}&user_id={{$request->user_id}}&status_id={{$subitem->id}}">
								{{$data[$subitem->id][0]}} / {{$data[$subitem->id][1]}}
								</a>
							</div>
							<?php 

								
							?>
							<!--
							<br>
							<div id="task_content">
								<div id="task_content_left">{{$data[$subitem->id][1]}}</div>
								<div id="task_content_right">{{$data[$subitem->id][2]}}</div>
							</div>
						-->
						@endif

						<script>
							<?php 
								if(isset($data[$subitem->id][0])||isset($data[$subitem->id][1])||isset($data[$subitem->id][2])){
							?>
								document.getElementById("{{$project->id}}_{{$subitem->id}}").style.backgroundColor = statuses_color[{{$subitem->id}}];
								document.getElementById("task_points_{{$project->id}}_{{$subitem->id}}").style.color = statuses_label_color[{{$subitem->id}}];
							<?php
								}
							 ?>
						</script>
				</td>

			@endforeach
				<td class="text-center"><?php $count = $project->countTaskInventoryBydDate($request->to_date); ?>@if($count>0){{$count}}@endif</td>
				<td class="text-center">{{$sum_points}}</td>
				<td class="text-center">@if(isset($project->monthly_points_goal)&&($project->monthly_points_goal>0))

								{{number_format($sum_points * 100 / $project->monthly_points_goal, 1, '.', '') }}
								@endif
				</td>
				<?php $sum_points = 0; ?>
			</tr>
			
			@endforeach()
		</table>	

</div>


@endsection