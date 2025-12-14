@extends('layout_pieces')
@section('content')
<body onload="getMessages({{$task->id}},{{$task->getUser(Auth::user()->id)}})"></body>
<div class="container">
	<div class="card">
		<div class="card-header">
			<h2>{{$task->name}}</h2>
		</div>
		<div class="card-body">
      @if(isset($task->file_url))
			  <img class="task-image" src="{{'/laravel/storage/app/public/files/'.$task->file_url}}" alt="">
      @else
      Sin Imagen
      @endif
		</div>
		<div class="card-footer">
			@if(isset($task->description))<strong>Description: </strong>{{$task->description}}@else {{"Sin Descripción"}} @endif<br>
      @if(isset($task->copy))<strong>Copy: </strong>{{$task->copy}}@else {{"Sin Copy"}} @endif<br>
      @if(isset($task->caption))<strong>Caption: </strong>{{$task->caption}}@else {{"Sin Caption"}} @endif
			<br><br>

				<a class="btn btn-danger" id="btn-rejeact" style="color: white;" onclick="rejectTask({{$task->id}});">Reject</a>
				<a class="btn btn-primary" id="btn-approve" style="color: white;" onclick="approveTask({{$task->id}});">Approve</a>

		</div>
	</div>
	<br>
	
	
	<div class="card">
		<div class="card-header">
			<h4>Comments</h4>
		</div>
		<div class="card-body" id="comment">
			<label for="description_message">Add Comment</label>
			<div class="input-group">
			  	<input class="form-control" style="width: 45%;" type="text" name="description_message" id="description_message" class="form-control">
				<input type="hidden" name="task_id" value="" id="task_id">
			  <span class="input-group-btn">
			    <button id="show_password" class="btn btn-primary" type="button" onclick="sendMessage({{$task->id}},{{$task->getUser(Auth::user()->id)}} );"> 
			    	<span class="far fa-paper-plane"></span>
			    </button>
			  </span>
			</div>
		</div>
		<div class="card-footer">
			<div id="all_messages"> 
			</div>
		</div>
	</div>



</div>
</body>
<script type="text/javascript">
	
	function sendMessage(task_id,user_id){  
           var description = $("#description_message").val();
            $.ajax({
                type: "GET",
                url : "/task/message/"+task_id+"/"+user_id.id+"/"+description,
                success : function(res){
                   $("#messageModal_2 input").val("");
                   $("#messageModal_2 .close").click()
                   $("#description_message").val("");
                    getMessages(task_id,user_id); 

                },
                error:function(){
                    alert("Error");
                }
            },"html");
    }

    function rejectTask(task_id){
           var description = $("#description_message").val();
            $.ajax({
                type: "GET",
                url : "/task/reject/"+task_id,
                success : function(res){
                	$("#btn-rejeact").css("display","none");
                	$("#btn-approve").css("display","none");
                },
                error:function(){
                    alert("Error");
                }
            },"html");
    }

    function approveTask(task_id){ 
           var description = $("#description_message").val();
            $.ajax({
                type: "GET",
                url : "/task/approve/"+task_id,
                success : function(res){
                	$("#btn-rejeact").css("display","none");
                	$("#btn-approve").css("display","none");
                },
                error:function(){
                    alert("Error");
                }
            },"html");
    }

    function getMessages(task_id,creator_user){  
        var str = "";
        $.ajax({
        type: "GET",
        url :"/task/get_messages/"+task_id,
        success : function(res){
          $.each(res, function(i, obj) {
                        str +='<table >';
                        if(creator_user.id == obj.id){
                        str +='<tr >';
                        str += '<td  style="float: left;"><img  style="clip-path: circle(15px at center);width: 30px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                        str += '<td class="creator" ><textarea class=" form-control" disabled style="background: #ffffff; box-shadow: 0px 4px 4px rgb(50 50 71 / 8%), 0px 4px 8px rgb(50 50 71 / 6%);border-radius: 20px;" >'+obj.description+'</textarea></td>';
                        str +='</tr>';
                        }else{
                           str +='<tr >';
                           str += '<td class="user"><textarea class=" form-control" disabled style="width: 100%;float: right;background-color: #2196F3; border-radius: 20px;" " >'+obj.description+'</textarea></td>';
                        str += '<td  style="float: right;"><img  style="clip-path: circle(15px at center);width: 30px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                        str +='</tr>';
                        }
             str +='</table>';   
             $("#all_messages").html(str); 

              });
          console.log("get"+res);
        },
      },"html");
    }
</script>
<style type="text/css">
	th, td {
	padding: 9px 0px !important;
	}
</style>
@endsection