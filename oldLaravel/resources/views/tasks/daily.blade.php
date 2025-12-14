@extends('layout')


@section('content')
<h1>Daily</h1>

@include('tasks.filterMini')

<!-- Incio del tablero -->
<table  id="daily" class="table tableFixHead">
	<thead>
		<tr class="">
			
		<th {{$column_width}}> Projects</th>
			
		@foreach($users as $user)
		<th {{$column_width}} class="">@if(isset($user->image_url))
			<img src="/laravel/storage/app/public/files/users/{{$user->image_url}}" alt="{{$user->name}}" title="{{$user->name}}" height="26"  class="user_image_mini" border="0">
			@else
			<img src="/laravel/storage/app/public/files/users/user.png" alt="{{$user->name}}" title="{{$user->name}}" height="26" class="user_image_mini" border="0"> 

			@endif
		</th>
			
		@endforeach
	</tr>
	</thead>

	<!-- cuerpo del tablero -->
	<tbody>
	@foreach($projects as $project)
	<tr class="">
		<td {{$column_width}} class="">
			<a href="/projects/{{$project->id}}/edit">{{$project->name}}</a>
		</td>
		@foreach($users as $user)
		<td ondrop="drop(event)" ondragover="allowDrop(event)" {{$column_width}} class="daily-slot" name="p{{$project->id}}u{{$user->id}}" id="p{{$project->id}}u{{$user->id}}">
			<a href="#" onclick="setCel('p{{$project->id}}u{{$user->id}}');showModal({{$user->id}}, {{$project->id}});">[+]</a>

			@foreach($user->getTodayTaskFromProject($project->id, $request, $statuses_id) as $task)


				<div draggable="true" ondragstart="drag(event)" class="daily-task noDrop" id="t{{$task->id}}" sid="{{$task->status_id}}" style="background-color: @if(isset($task->status)){{$task->status->background_color}}@endif;" ondblclick="updateNextStatusTask({{$task->id}})">
				<!--
				<a href="#" onclick="updateStatusTask({{$task->id}}, 4)">[-]</a>
				-->
				<a href="/tasks/{{$task->id}}/edit" >[.]</a>
				<a href="#"  onclick="updateNextDay({{$task->id}});">[>]</a>
				@if($task->points>0)
				<strong class="points">{{$task->points}} </strong>
				@endif
					{{substr($task->name, 0, 40)}}
					<div class="task-time"> <strong>{{ substr($task->status->name, 0, 3) }}</strong> {{Carbon\Carbon::parse($task->due_date)->format('H:i')}}</div>
				
			</div>
			
			@endforeach
		
		</td>
		@endforeach
	</tr>
	@endforeach
	</tbody>
</table>


@include('tasks.createModal')

@section('footerjs')
<script type="text/javascript">
	var activeCel = "";
	var is_draggin = false;
	var target = "";

	function showModal(uid, pid){
	    $('#form_modal #user_id').val(uid);
	    $('#project_id').val(pid);	

	    $('#name').val("");
	    $('#points').val("");
	    $('#description	').val("");
	    
	    
	    
	    $('#myModal').modal('toggle');
	}

	function postData(data){
		var jqxhr = $.post( "example.php", function() {
			  alert( "success" );
			})
			  .done(function() {
			    alert( "second success" );
			  })
			  .fail(function() {
			    alert( "error" );
			  })
			  .always(function() {
			    alert( "finished" );
			  });
			 
			
	}


	function sendData(){
		$('#myModal').modal('hide');
		name = $('#name').val(); 
    	
		project_id = $('#project_id').val(); 
    	user_id = $('#form_modal #user_id').val();
    	due_date = $('#due_date').val();
    	priority = $('#priority').val();
    	points = $('#points').val();
    	
    	status_id = 1;
    	
    	not_billing = $('#not_billing').val();
    	description = $('#description').val();
		
		request = { name: name, 
    		project_id: project_id, 
    		user_id: user_id,
    		due_date: due_date,
    		priority: priority,
    		not_billing: not_billing,
    		description: description,
    		status_id: status_id,
    		points: points,
    		  
    	};
    	console.log(request);
    	
    	$.post( "/api/tasks", request,  function(response) {
				})
			  .done(function(response) {
			  	console.log(response);
			    activeCel.append(getTaskHtml(response.name, response.color, response.id, points));

			    //Parece que el enlace de WP o el fanpage generaba errores
			  })
			  .fail(function() {
			    alert( "Error" );
			  });
	}

	function getTaskHtml(name, color, id, points){

		str_points = "";
		if(points>0){
			str_points = '<strong class="points">'+points+'</strong>';
		}
		str = '<div ondblclick="updateNextStatusTask('+id+')" id="t'+id+'"" class="daily-task" style="background-color:'+color+'">'+
			'<a href="/tasks/'+id+'/edit" >[.]</a>'+
			'<a href="#" onclick="updateNextDay('+id+');" >[>]</a>'+
		str_points+
		name+
		'</div>';
		
		return str;
	}
	/*
	$('#myModal').on('hidden.bs.modal', function (e) {
    	sendData();
  	})
  	*/

  	var input = document.getElementById("name");
  	addEnter(input);


  	var input = document.getElementById("points");
  	addEnter(input);
  	
	function addEnter(input){
		// Execute a function when the user releases a key on the keyboard
		input.addEventListener("keyup", function(event) {
		  // Number 13 is the "Enter" key on the keyboard
		  if (event.keyCode === 13) {
		    event.preventDefault();
		    sendData();
		  }
		});
	}
	function setCel(id){
		
		activeCel = $("#"+id);
		
	}
	function updateStatusTask(tid, sid){
		if(!is_draggin){
			request = { name: name, 
	    		id : tid,
	    		status_id: sid
	    		  
	    	};
	    	console.log(request);
			$.post( "/api/tasks/update", request,  function(response) {
					})
				  .done(function(response) {
				    console.log(response);
	    			$("#t"+response).remove();
				  })
				  .fail(function() {
				    alert( "Error" );
				  });
		}
	}

	function updateNextStatusTask(tid){
		if(!is_draggin){
			console.log("next");
			request = { name: name, 
    			id : tid,
    		};
    		console.log(request);

            $.post("/api/tasks/next_status",request, function(response) {
				})
                .done(function(response){
                	selector = '#t'+tid;
                	console.log(selector);
                	console.log(response);
                	
                	$(selector).css('background-color', response);
                	console.log("r"+tid);
                })
        }            
                
	}
	function allowDrop(ev) {
	  ev.preventDefault();
	  

	}

	function drag(ev) {
	  ev.dataTransfer.setData("text", ev.target.id);
	  draggin = true;
	  
	}

	function drop(ev) {
		var _target = $("#" + ev.target.id);
		if ($(_target).hasClass("noDrop")) {
	 		console.log('no drop');
	 	}else{

		  ev.preventDefault();

		  var data = ev.dataTransfer.getData("text");
		  ev.target.appendChild(document.getElementById(data));
		  console.log(ev.dataTransfer.getData("text"));
		  str = ev.target.id;
		  str = str.substr(1);
		  str = str.split('u');
		  uid = str[1];
		  pid = str[0];

		  tid = ev.dataTransfer.getData("text");
		  tid = tid.substr(1); // quito la t del inicio

		  console.log(uid);
		  console.log(tid);
		  updateUser(tid, pid, uid);
	  }
	  draggin = false;
	}


	function updateUser(tid, pid, uid){
		
		request = { 
			user_id: uid, 
    		id: tid,
    		project_id: pid
    		  
    	};
    	console.log(request);
    	
    	$.post( "/api/tasks/update", request,  function(response) {
				})
			  .done(function(response) {
			    console.log(response);
    			//$("#t"+response).remove();
			  })
			  .fail(function() {
			    alert( "Error" );
			  });

		
	}

	function updateNextDay(tid){
		
		request = { 

    		id: tid,
    		
    		  
    	};

    	
    	
    	$.post( "/api/tasks/nextDay", request,  function(response) {
				})
			  .done(function(response) {
			    console.log(response);
    			$("#t"+response).remove();
			  })
			  .fail(function() {
			    alert( "Error" );
			  });

		
	}
</script>
@endsection

@endsection

