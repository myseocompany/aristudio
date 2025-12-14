@extends('layouts.agile_modi')
@section('content')
@php
	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
@endphp
<div class="page add-tasks" data-name="add-lead">

  <!-- Header -->
  <div class="navbar">
    <div class="navbar-inner align-items-center">
      @section('content-back-page')
        <div class="left back-page">
          <a href="#" onclick="openView('/');" >
            <i class="fas fa-angle-left"></i>
          </a>
        </div>
      @endsection
      <div class="page-header-title">
        <h4>Parrilla</h4>
      </div>
    </div>
  </div>
  <!-- /Header -->
  
  <div class="page-content">
    <div class="block"> 
      <div class="container">
	  @include('tasks.pieces.filter')




<?php 
	$countTask = 0;
	$max_items = 5;
	$totalTask = 0;
	if($tasksGroup->count()!=0){
		$numGroups = ceil($tasksGroup->count()/ $max_items);
	if($numGroups>1){
		$page_length = round($tasksGroup->count()/round($numGroups));
	}else{
		$page_length = $tasksGroup->count();
	}
	$count = 0;
	$page = 0;
?>
 @include('tasks.pieces.dash_board')
<?php } ?>




	 




		<table class="table table-hover" id="myTable">
		
			<thead>
				<tr>
					<th>Task</th>
					<!-- <th>User</th> -->
					<th>Description</th>
					<!-- <th>Status</th> -->
					<!-- <th>Visual detail / Text</th> -->
					<!-- <th>Description</th> -->
					<th>Copy/caption</th>
					
					<th>Status</th>
					
				</tr>
			</thead>
			<tbody class="task_body">
				@foreach($tasks as $task)
				<tr>
					<td>
						<a href="#" onclick="openNewView('tasks/{{$task->id}}')">{{$task->name}}</a><br><br><br>
						<select id="user_{{$task->id}}" name="user" class="form-control" onchange="changeUser({{$task->id}});">
							@foreach($users as $user)
								<option value="{{$user->id}}" @if($user->id == $task->user_id) selected="" @endif>{{$user->name}}</option>
							@endforeach
						</select>		
					</td>

					<!-- IMG -->
					<!-- <td>
						<img width="200" src="/laravel/storage/app/public/files/{{$task->file_url}}">
					</td> -->

					<!-- USER -->
					<!-- <td>
						<select id="user_{{$task->id}}" name="user" class="form-control" onchange="changeUser({{$task->id}});">
							@foreach($users as $user)
								<option value="{{$user->id}}" @if($user->id == $task->user_id) selected="" @endif>{{$user->name}}</option>
							@endforeach
						</select>
					</td> -->

					<!-- STATUS -->
					<!-- <td>
						<select id="status_{{$task->id}}" name="status" class="form-control" onchange="changeStatus({{$task->id}});">
							@foreach($task_status as $ts)
								<option value="{{$ts->id}}" @if($ts->id == $task->status_id) selected="" @endif>{{$ts->name}}</option>
							@endforeach
						</select>
					</td> -->

					<!-- DESCRIPTION -->
					<td><textarea rows="10" cols="40" name="description{{$task->id}}" id="description{{$task->id}}" class="form-control" onblur="changeValue('description',{{$task->id}});">{{$task->description}}</textarea></td>

					<!-- URL  -->
					<!-- <td><textarea rows="5" cols="40" name="url_finished{{$task->id}}" id="url_finished{{$task->id}}" class="form-control" onblur="changeValue('url_finished',{{$task->id}});">{{$task->url_finished}}</textarea></td> -->

					<!-- COPY -->
					<td><textarea placeholder="copy" rows="5" cols="40" name="copy{{$task->id}}" id="copy{{$task->id}}" class="form-control" onblur="changeValue('copy',{{$task->id}});">{{$task->copy}}</textarea>

					<!-- CAPTION -->
					<br><textarea placeholder="caption" rows="5" cols="40" name="caption{{$task->id}}" id="caption{{$task->id}}" class="form-control" onblur="changeValue('caption',{{$task->id}});">{{$task->caption}}</textarea>
				</td>
					<!-- <td>
						<a class="btn btn-link btn-sm" href="#" onclick="openNewView('pieces/{{$task->id}}')">Show Piece</a>
					</td> -->

					<!-- URL  -->
					
					<!-- STATUS -->
					<td id="task_cel_{{$task->id}}" style="background-color: @if(isset($task->status->background_color)) {{$task->status->background_color}}  @endif"><!-- } -->
						<!-- <td> -->
						

								<select id="status_{{$task->id}}" name="status" class="form-control" onchange="changeStatus({{$task->id}});">
									@foreach($task_status as $ts)
										<option value="{{$ts->id}}" @if($ts->id == $task->status_id) selected="" @endif>{{$ts->name}}</option>
									@endforeach
								</select><br><br>
								<img width="200" src="/laravel/storage/app/public/files/{{$task->file_url}}">
<br>							
							<input placeholder="finished url" name="url_finished{{$task->id}}" id="url_finished{{$task->id}}" class="form-control" onblur="changeValue('url_finished',{{$task->id}});" value="{{$task->url_finished}}">
							<br>
							<a class="btn btn-link btn-sm" href="#" onclick="openNewView('pieces/{{$task->id}}')">Show Piece</a>	

					<!-- </td> -->
					</td>
					
				</tr>
				@endforeach
			</tbody>
		</table>
	  </div>
    </div>
  </div>
  <!-- Page Content end -->

</div>
<!-- App end -->




<script type="text/javascript">

	function changeUser(task_id){
	    var new_user = $("#user_"+task_id+" option:selected").val();
	   	var endpoint = '/pieces/setUser/'+new_user+'/task/'+task_id;
	    $.ajax({
	        type: 'GET', //THIS NEEDS TO BE GET
	        url: endpoint,
	        dataType: 'json',
	        success: function (data) {
	        },
	        error: function(data) { 
	        }
	    });
	}

	function changeStatus(task_id){
	    var new_status = $("#status_"+task_id+" option:selected").val();
	   	var endpoint = '/pieces/setStatus/'+new_status+'/task/'+task_id;
	    $.ajax({
	        type: 'GET', //THIS NEEDS TO BE GET
	        url: endpoint,
	        dataType: 'json',
	        success: function (data) {
				console.log("hola"+data);
	        },
	        error: function(data) { 
				$("task_cel_"+task_id).css("background-color",data);
				console.log("worl");
	        }
	    });
	}

	function changeValue(name, task_id){
	    var new_value = $("textarea[name="+name+task_id+"]").val();
	    var parametros = {
	        value : new_value,
	        "task_id" : task_id,
	        attribute : name
	    };
	    $.ajax({
			data:  parametros,
			url:   '/pieces/set_'+name,
			type:  'post',
			beforeSend: function () {

			},
			success:  function (response) {
			}
	    });
	}

	function openNewView(url){
		window.open(url);
	}
</script>

<style type="text/css">
	.form-control{
		height: 100% !important; 
	}
	
	.container {
    width: 100%;
    max-width: 100%;
    padding-left: 0px;
    padding-right: 0px;
    margin: 0 auto;
}
</style>
@endsection