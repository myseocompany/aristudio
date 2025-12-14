@extends('layout')

@section('content')
@if(isset($model->project))
<div>
  <a href="/tasks">Tasks</a> >> 
  <a href="/tasks?project_id={{$model->project_id}}" style="color: {{$model->project->color}}">
    {{$model->project->name}}
  </a>
</div>
@endif
<h1>{{ $model->name}}</h1>

<body onload="getMessages({{$model->id}},{{$model->getUser(Auth::user()->id)}}); viewUserTask({{$model->id}}, {{Auth::user()->id}})">
<form method="POST" action="/tasks/{{ $model->id}}/edit">
  <input type="hidden" name="_token" value="{{ csrf_token() }}">

    {{ csrf_field() }}
  @if (isset($model->file_url))

    <img class="task-image" src="{{'/laravel/storage/app/public/files/'.$model->file_url}}" alt="">
    <div>
      <button type="button" class="btn btn-basic"><a href="/tasks/{{$model->id}}/deleteFile">
        Delete File
      </a></button>
    </div>
  @endif
  <div class="form-group">
    <label for="name"><strong>File</strong></label>
    <script type="text/javascript">
         function getMessages(task_id,creator_user){  
    
                        var str = "";

                        $.ajax({
                        type: "GET",
                        url :"/task/get_messages/"+task_id,
                        success : function(res){

                          $.each(res, function(i, obj) {
                            console.log(obj);
                                        str +='<table >';
                                        if(creator_user.id == obj.id){
                                        str +='<tr >';
                                        str += '<td  style="float: left;"><img  style="clip-path: circle(13px at center);width: 26px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                                        str += '<td class="creator" style="width: 400px;float: left;" ><textarea class=" form-control" value="'+obj.description+'" style="background: #ffffff; box-shadow: 0px 4px 4px rgb(50 50 71 / 8%), 0px 4px 8px rgb(50 50 71 / 6%);border-radius: 20px;padding: 15px 12px !important;" name="commentary_'+obj.task_message_id+'">'+' '+obj.description+'</textarea><i class="fas fa-edit hide" onclick="editTaskCommentary('+obj.task_message_id+')"></i><label>'+obj.task_message_created_at+'</label></td>';
                                        str +='</tr>';
                                        }else{
                                           str +='<tr >';
                                        str += '<td  style="float: right;"><img  style="clip-path: circle(13px at center);width: 26px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                                        str += '<td class="user" style="width: 400px;float: rigth;" ><textarea class=" form-control" disabled value="'+obj.description+'" style="width: 400px;float: right;background-color: #DDD; border-radius: 20px;padding: 15px 12px !important;" " >'+' '+obj.description+'</textarea></td>';
                                        str +='</tr>';
                                        }
                             str +='</table>';   
                             $("#count_messages2").html(str); 

                              });
                          console.log("get"+res);
                        },
                      },"html");
                    } 
             //post
              function sendMessage(task_id,user_id){
                  var description = $("#description_message").val();
                    var parametros = {
                            "task_id" : task_id,
                            "user_id" : user_id,
                            "description" : description
                    };
                    console.log(parametros);
                    $.ajax({
                            data:  parametros, //datos que se envian a traves de ajax
                            url:   '/task/message/post', //archivo que recibe la peticion
                            type:  'post', //método de envio
                            beforeSend: function () {
                                    $("#resultado").html("Procesando, espere por favor...");
                            },
                            success:  function (response) { //una vez que el archivo recibe el request lo procesa y lo devuelve
                                     $("#messageModal_2 input").val("");
                                     $("#messageModal_2 .close").click();
                                     $("#description_message").val("");
                                     getMessages(task_id,user_id); 
                            }
                    });
            }







     </script>
    
    <div class="help-block">
      <a href="{{'/laravel/storage/app/public/files/'.$model->file_url}}">
      {{ $model->file_url}}
      </a>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="form-group p-5 mb-4 bg-light rounded-3">
            
        <div class="help-block">{!! nl2br($model->description) !!}</div>
    
      </div>  
    </div> 
    <div class="col">
      <div class="card shadow-sm">
        <div style="background-color: #55595c; padding: 5px; color: white; text-align:center">
        <pre  style="background-color: #55595c; padding: 5px; color: white; text-align:center">{{$model->copy}}</pre>
      </div>
      <!--
        <svg class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">
          
        </text></svg>
      -->
        <div class="card-body">
          <p class="card-text">
            {{$model->caption}}
          </p>
          <div class="d-flex justify-content-between align-items-center">
            
            <div class="btn-group">
              <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
              <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
            </div>
            <small class="text-muted">9 mins</small>
        
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="row">
    <div class="col-6">
      <label for="description"><strong>User</strong></label>    
      <div class="help-block">@if(isset($model->user)){{ $model->user->name }} @endif</div>
    </div>
    <div class="col-6">
      <label for="name"><strong>Due Date</strong></label>
      <div class="help-block">{{ $model->due_date}}</div>
    </div>
    
  </div>
  <div class="row">

      <div class="col-6">
        <label for="name"><strong>Created At</strong></label>
        <div class="help-block">{{ $model->created_at}}</div>
      </div>
      <div class="col-6">
        <label for="description"><strong>Project</strong></label>    
        <div class="help-block">{{ $model->project_name }}</div>
      </div>
      <div class="col-6">
        <label for="description"><strong>Status</strong></label>    
        <div class="help-block"><span class="badge" style="background-color:{{ $model->status->background_color}}; color:{{ $model->status->color}}">{{ $model->task_status }}</span></div>
      </div>
      
      <div class="col-6">
        <label for="name" ><strong>Not Billing</strong></label>
        <input type="checkbox" style="pointer-events: none;" class="form-control col-1" id="not_billing" name="not_billing" @if($model->not_billing == true) checked @endif>
      </div>
      <div class="col-6">
        <label for="points"><strong>Copy In</strong></label>
        <div class="help-block">{{ $model->copy }}</div>
      </div>

      <div class="col-6">
        <label for="points"><strong>Copy Out</strong></label>
        <div class="help-block">{{ $model->caption }}</div>
      </div>

      <div class="col-6">
        <label for="name"><strong>Delivered Date</strong></label>
        <div class="help-block">{{ $model->updated_at}}</div>
      </div>
      <div class="col-6">
        <label for="name"><strong>Updated At</strong></label>
        <div class="help-block">{{ $model->updated_at}}</div>
      </div>
      <div class="col-6">
        <label for="name"><strong>Points</strong></label>    
        <div class="help-block">{{ $model->points}}</div>
      </div>
 
      
      <div class="col-6">
        <label for="description"><strong>Priority</strong></label>    
        <div class="help-block">{{ $model->priority }}</div>
      </div>

      <div class="col-6">
        <label for="points"><strong>Url Finished Task</strong></label>
        <div class="help-block"><a href="{{ $model->url_finished }}">{{ $model->url_finished }}<a></div>
      </div>
      

    
  </div>
  


  



  <button type="submit" class="btn btn-sum btn-primary my-2 my-sm-0">Edit</button>
  <br>


<!--
	  	<a  data-toggle="modal" data-target="#messageModal_2" >
	   	<img src="/images/mas.png" style="width: 25px">
	 	</a>
-->
  
   

</form>

 <div>
   <p><strong>Commentary</strong></p>
   <input class="form-control" style="width: 45%;" type="text" name="description" id="description_message" class="form-control">
    <input type="hidden" name="" value="" id="task_id">
      <div class="btn-save" style="margin-top:8px;">
      <a class="btn btn-sum btn-primary my-2 my-sm-0" style="margin-top: 5px;color: #ffff !important;" onclick="sendMessage({{$model->id}},{{$model->getUser(Auth::user()->id)}} );">Save</a>
      </div>
<div>

  
  <div id="count_messages2"> 
  </div>

<h2>Seen by</h2>

  @foreach($task_users as $task_user)
    <div class="user_image_mini_container no_print">
      <img src="/laravel/storage/app/public/files/users/{{$task_user->user->image_url}}" alt="" class="user_image user_image_mini" title="{{$task_user->user->name}}">
    </div>
  @endforeach


<h2>Version</h2>
    <div class="table-responsive">
    
          <ul class="list-group">
            <?php $now = \Carbon\Carbon::now();?>
             @foreach($versions as $item)
               
            <li class="list-group-item">Change state to @if (isset($item->status_id) && ($item->status_id != ''))
               <strong>{{$item->status->name}}</strong>, Points {{$item->points}}, due {{ $item->due_date }} , updated {{$item->updated_at}}
            @endif
             
            <span class="badge" style="background-color: @if(isset($item->status)){{$item->status->color}}@endif;">
<?php // $end = \Carbon\Carbon::parse($item->updated_at->format('Y.m.d H:i:s'));
                $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->updated_at);
                $years = $end->diffInYears($now);
                $months = $end->diffInMonths($now);
                $days = $end->diffInDays($now);
                $hours = $end->diffInHours($now);

                $minutes = $end->diffInMinutes($now);
                $seconds = $end->diffInSeconds($now);
                
               // dd($now);
             ?>
            @if($years>0){{ $years }} years
            @else @if ($months>0) {{ $months }} hours 
            @else @if ($days>0) {{ $days }} days 
            @else @if ($hours>0) {{ $hours }} hours 
            @else @if ($hours>0) {{ $hours }} hours 
            @else @if ($minutes>0) {{$minutes}} minutes 
            @else @if ($seconds>0) {{$seconds}} seconds 
             @endif @endif @endif @endif @endif @endif @endif
            </span></li>
            @endforeach
          </ul>
    
    </div>


 
 <style type="text/css">
      th, td {
    padding: 9px 0px !important;
    }
     </style>

<script type="text/javascript">
  function viewUserTask(tid, uid){
    var endpoint = "/tasks/setTask/"+tid+"/setUser/"+uid;
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
          //console.log(data);                      
        },
        error: function(data) { 
          //console.log(data);
        }
    });
  }

  function editTaskCommentary(tid){
    console.log("commentary_"+tid);
    var description = $("textarea[name=commentary_"+tid+"]").val();
    console.log(description);
    var parametros = {
      "task_message_id" : tid,
      "description" : description
    };
    $.ajax({
            data:  parametros, //datos que se envian a traves de ajax
            url:   '/task/message/update', //archivo que recibe la peticion
            type:  'post', //método de envio
            beforeSend: function () {
              $("#resultado").html("Procesando, espere por favor...");
            },
            success:  function (response) { //una vez que el archivo recibe el request lo procesa y lo devuelve
              /*
              $("#messageModal_2 input").val("");
              $("#messageModal_2 .close").click();
              $("#description_message").val("");
              getMessages(task_id,user_id); 
              */

              console.log("si");
            }
    });
  }

</script>
@endsection