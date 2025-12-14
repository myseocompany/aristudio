@extends('layout')

@section('content')
<br>
<h1>Tasks schedules</h1>

@include('tasks.createForm')
@include('tasks.filter')

<?php 
  $sumPoints = 0; 
  $countTask = 0;
  $max_items = 5;


      if($tasksGroup->count()!=0){
        $numGroups = ceil($tasksGroup->count()/ $max_items);
        if($numGroups>1){
          $page_length = round($tasksGroup->count()/round($numGroups));
        }else
        {
          $page_length = $tasksGroup->count();
        }
        //dd(ceil($numGroups));
        $count = 0;
        $page = 0;

?>

@include('tasks.dashBoard')
<?php } ?>
  <script>
    colors = new Array();
    text = new Array();
    
    @foreach ($task_status as $status)
      colors[{{$status->id}}] = "{{$status->color}}"; 
      text[{{$status->id}}] = "{{$status->name}}"; 
    @endforeach
    
    </script>

        <div class="bigimage-container" id="bigimage-container">
          <img src="" alt="" width="100%" class="task_image_big" id="bigimage-file"> 
        </div>
        

	<div class="table-responsive" id="sortable">
            <table class="table table-responsive" id="taskTable">
              <thead class="thead-white">
                <tr>
                  <th colspan="2">Name</th>
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                  <th class="no_print">Priority</th>
                  <th class="no_print">Due date</th>
                  
                  <th class="no_print">Lead time</th>
                  <th class="no_print">User</th>
                  @endif
                  <th>Image</th>
                  <th>Status</th>
                </tr>
              </thead>

<?php $last_task_status_id=-1;
      $last_project_id = -1;
 ?>

              <tbody>
        @foreach($model as $item)
        <!-- Inicio ldel cilco de tareas -->
@if($last_project_id!=$item->project_id)
                  <tr style="background-color:#fff" class="title_status">
                    <td colspan="4" class="project-row">
                      <h3>
                        <a href="/projects/{{$item->project_id}}">{{$item->project->name}}</a>
                        <h3></td>
                  </tr>
                  <?php $last_task_status_id=-1; ?>
                  @endif
                 
                  
                

<form action="" method="POST" name="updateTaskForm{{$item->id}}" id="updateTaskForm{{$item->id}}">
                  {{ csrf_field() }}
    <tr class="task_row" data-id="{{$item->id}}" dragable="true" ondragstart="alert(this)" id="task_id_{{$item->id}}">
                    <input type="hidden" name="token" id="token_id_{{$item->id}}" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="task_id__{{$item->id}}" value="{{$item->id}}">

                    
                    

                 
                  <td id="{{$item->id}}" colspan="2" >
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                    <span class="no_print">[{{ $item->id }}] </span> 
                  @endif
                  <div id="task_dates">

                    

                    @if(isset($item->due_date)) {{ date('M-d', strtotime($item->due_date)) }} @else N/A @endif 
                  </div>
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) ) 
                  <span class="no_print">:</span>
                    @if(isset($item->created_at))
                      <span class="no_print">
                        {{ date('M-d', strtotime($item->created_at)) }}
                      </span>
                    @else 
                      N/A 
                    @endif
                  @endif
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                  <a href="/tasks/{{ $item->id }}"  class="no_print">
                    

                    {{$item->nameSubstr(100)}} 
                  </a> 
                  <div class="just_print">{{$item->name}}</div>
                  <span class="no_print">-</span>
                  @else
                    <div>
                      <h5 class="task_title">{{$item->name}}</h5> 
                    
                      <div class="task_description">{{$item->description}}</div>
                    </div> 
                  @endif

          @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )         
            <a href="/tasks/{{$item->id}}/edit" class="no_print">Editar</a> <span class="no_print">-</span> <a href="#" onclick="updateObserverAjax({{$item->id}});" class="no_print">Observar</a>
          @endif 
          <input type="hidden" id="observer_id" name="observer_id" value="{{Auth::id()}}" >
          <a href="#observe_id{{$item->id}}" onclick="updateObserverAjax({{$item->id}});"> <img src="/images/eye.svg" height="20" alt="" id="observer_img_{{$item->id}}" name="observer_img_{{$item->id}}" @if($item->observer_id!=Auth::id()) style="display: none"  @endif></a> <br clear="no_print">

          <input type="hidden" id="status_id_{{$item->id}}" name="status_id" value="{{$item->status_id}}" >

          
                    
              
           
            
                    <div id="mini_container no_print" >
                      @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                      <div class="user_image_mini_container no_print"  >
                        <a hrefd="#" onclick="showTeam({{$item->id}})" alt="Asignar responsable" >
                        
                                              <img src="/laravel/storage/app/public/files/users/user.png" alt="vanessa guerrero" title="vanessa guerrero" height="26" class="user_image_mini" border="0"> 
                     
                                            </a>
                      </div>


                    
                    @foreach($users as $user)
                      <div class="user_image_mini_container toggle" id="user_{{$item->id}}_{{$user->id}}" style="display: none" > 

                      <a  hrefd="#user_{{$item->id}}_{{$user->id}}" onclick="updateUserAjax({{$item->id}},{{$user->id}});" alt="{{$user->name}}">
                        
                        @if(isset($user->image_url))
                      <img src="/laravel/storage/app/public/files/users/{{$user->image_url}}" alt="{{$user->name}}" title="{{$user->name}}" height="26"  class="user_image_mini" border="0">
                      @else
                      <img src="/laravel/storage/app/public/files/users/user.png" alt="{{$user->name}}" title="{{$user->name}}" height="26" class="user_image_mini" border="0"> 
                     
                      @endif
                      </a>

                      </div>
                    @endforeach
                    @endif
                    <div>
                    	@if(isset($task_types))
	                    <select name="type_id" id="type_id_{{$item->id}}" class="custom-select" onchange="updateTypeAjax({{$item->id}}, this.value );">
		                    <option>Select a type...</option>
		                    @foreach($task_types as $option)
		                    <option value="{{$option->id}}" @if($item->type_id==$option->id) selected="selected" @endif >{{$option->name}}</option>
		                    @endforeach  
	                    </select>
	                    <span id="after_type_{{$item->id}}">
	                    	@if(isset($item->type))
	                    <select name="sub_type_id" id="type_id_{{$item->id}}" class="custom-select" onchange="updateSubTypeAjax({{$item->id}}, this.value );">
		                    <option>Select a type...</option>
		                    @foreach($item->subTypeOptions() as $option)
		                    <option value="{{$option->id}}" @if($item->sub_type_id==$option->id) selected="selected" @endif >{{$option->name}}</option>
		                    @endforeach  
	                    </select>
	                    	
	                    	@endif
	                    </span>
	                    @endif
                    </div>
                    </div>
                    @if(isset($item->url_finished) && ($item->url_finished!=""))
                    <div class="no_print">
                      <a href="{{$item->url_finished}}" target="_blanck">Enlace</a>
                    </div>
                    @endif

                  </td>

                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                  <td class="no_print">
                   
          {{ $item->priority }}
      

                  </td>
                  <td class="no_print">
                    @if(isset($item->due_date))
          {{ date('M-d', strtotime($item->due_date)) }}
        @else N/A @endif

                  </td>

                  <td class="no_print">
                    
                    {{$item->lead_time}}
                  </td>
                 
                  
                  <td class="no_print">
                  <div class="user_image_container no_print">
                  @if(isset($item->user)&&!isset($item->user->image_url)&&isset($item->user_id)&&($item->user_id!=""))
                      <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="" class="user_image" id="task_user_image_{{$item->id}}">
                  
                  @else
                    @if(isset($item->user->image_url))
                  
                    <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="" class="user_image" id="task_user_image_{{$item->id}}">
                  
                    @endif
                  
                  @endif
                  </div>
                   @endif
                   </td>
                  <td>
@if (isset($item->file_url))
     <img src="{{'/laravel/storage/app/public/files/'.$item->file_url}}" alt="" class="task_image @if((Auth::user()!=null) && (Auth::user()->role_id==3) ) larger_image @endif" onmouseenter="showImageFile('{{"/laravel/storage/app/public/files/".$item->file_url}}')" onmouseleave="hideImageFile('{{"/laravel/storage/app/public/files/".$item->file_url}}')">
  
@endif
                  </td>

                  
                  
                  <td id="task_cel_{{$item->id}}" style="background-color: @if(isset($item->status->color)) {{$item->status->color}}  @endif">
                    <div class="task_container">
                    <div>
                      @if(isset($item->children) && (count($item->children)!=0) )
                      <strong>{{ $item->getChildrenPoints() }}</strong>
                      @else
                      <strong>{{ $item->points }} </strong>
                      @endif
                      
                    </div>
                    <div>@if(isset($item->not_billing) && ($item->not_billing==1))Not billing @endif</div>
                      
                    <a id="#ref_{{$item->id}}" href="#ref_{{$item->id}}" onclick="updateNextStatusAjax({{$item->id}});">
                      @if(isset($item->status))
                      {{$item->status->name}}
                      @endif
                    </a>
                    <div>
                      
                    </div>
                    </div>
                  </td>
                </tr>
                <?php 
                  $last_task_status_id = $item->status_id;
                  $last_project_id = $item->project_id;
                if(isset( $item->points )){$sumPoints += $item->points; }
                  $countTask++;
                ?>
              </form>

        <!-- **************
        ********
        Inicio del los hijos
        ********
          -->
@if(true && (isset($item->children)))
<?php $children = $item->getChildrenTask($request, $statuses_id);
?>
@foreach($children as $childItem)
<form action="" method="POST" name="updateTaskForm{{$childItem->id}}" id="updateTaskForm{{$childItem->id}}">
                  {{ csrf_field() }}

    <tr class="task_row" data-id="{{$childItem->id}}" dragable="true" ondragstart="alert(this)" id="task_id_{{$childItem->id}}">
      <td class="empty">&nbsp;</td>
                 
      <td >

        
          
        
                  
                   [{{ $childItem->id }}]  
          
                  @if(isset($childItem->created_at)) {{ date('M-d', strtotime($childItem->created_at)) }} @else N/A @endif :
                  @if(isset($childItem->due_date))

                   <span class="no_print">
                   }

          {{ date('M-d', strtotime($childItem->due_date)) }}
          </span>
          
        @else N/A @endif
                      <a href="/tasks/{{ $childItem->id }}"  class="no_print">
                    

                    {{$childItem->nameSubstr(100)}} 
                  </a> <span class="no_print">-</span>
          <a href="/tasks/{{$childItem->id}}/edit" class="no_print">Editar</a> <span class="no_print">-</span> <a href="#" onclick="updateObserverAjax({{$childItem->id}});" class="no_print">Observar</a> 
          <input type="hidden" id="observer_id" name="observer_id" value="{{Auth::id()}}" >
          <a href="#observe_id{{$childItem->id}}" onclick="updateObserverAjax({{$childItem->id}});"  class="no_print"> <img src="/images/eye.svg" height="20" alt="" id="observer_img_{{$childItem->id}}" name="observer_img_{{$childItem->id}}" @if($childItem->observer_id!=Auth::id()) style="display: none"  @endif></a> 
                   <div  class="just_print">
            <strong>{{$childItem->name}}</strong>: 
            {{$childItem->description}}
          </div> 
              
           
            
                    <div id="mini_container no_print" >

                      <div class="user_image_mini_container no_print"  >
                        <a hrefd="#" onclick="showTeam({{$childItem->id}})" alt="Asignar responsable" >
                        
                                              <img src="/laravel/storage/app/public/files/users/user.png" alt="vanessa guerrero" title="vanessa guerrero" height="26" class="user_image_mini" border="0"> 
                     
                                            </a>
                      </div>

                    @foreach($users as $user)
                      <div class="user_image_mini_container toggle" id="user_{{$childItem->id}}_{{$user->id}}" style="display: none" > 

                      <a  hrefd="#user_{{$childItem->id}}_{{$user->id}}" onclick="updateUserAjax({{$childItem->id}},{{$user->id}});" alt="{{$user->name}}">
                        
                        @if(isset($user->image_url))
                      <img src="/laravel/storage/app/public/files/users/{{$user->image_url}}" alt="{{$user->name}}" title="{{$user->name}}" height="26"  class="user_image_mini" border="0">
                      @else
                      <img src="/laravel/storage/app/public/files/users/user.png" alt="{{$user->name}}" title="{{$user->name}}" height="26" class="user_image_mini" border="0"> 
                     
                      @endif
                      </a>

                      </div>
                    @endforeach
                    
                    
              
                  </td>

      
                  <td class="no_print">
                   
          {{ $childItem->priority }}
      

                  </td>
                  
                  <td class="no_print">
                    @if(isset($childItem->due_date))
          {{ date('M-d', strtotime($childItem->due_date)) }}
        @else N/A @endif

                  </td>
                  <td class="no_print">
                    
                    {{$childItem->lead_time}}
                  </td>
                  
                  <td>
                  <div class="user_image_container no_print">
                  @if(isset($childItem->user)&&!isset($childItem->user->image_url)&&isset($childItem->user_id)&&($childItem->user_id!=""))
                      <img src="/laravel/storage/app/public/files/users/{{$childItem->user->image_url}}" alt="" class="user_image" id="task_user_image_{{$childItem->id}}">
                  
                  @else
                    @if(isset($childItem->user->image_url))
                  
                    <img src="/laravel/storage/app/public/files/users/{{$childItem->user->image_url}}" alt="" class="user_image" id="task_user_image_{{$childItem->id}}">
                  
                  @endif
                  
                  @endif
                  </div>
                   </td>
                  
                  
                  <td>
        <input type="hidden" name="token" id="token_id_{{$childItem->id}}" value="{{csrf_token()}}">
        <input type="hidden" name="id" id="task_id__{{$childItem->id}}" value="{{$childItem->id}}">
        
        @if (isset($childItem->file_url))
    
        <img src="{{'/laravel/storage/app/public/files/'.$childItem->file_url}}" alt="" class="task_image " onmouseenter="showImageFile('{{"/laravel/storage/app/public/files/".$childItem->file_url}}')" onmouseleave="hideImageFile('{{"/laravel/storage/app/public/files/".$childItem->file_url}}')">
        
  @endif
      </td>
                  <td id="task_cel_{{$childItem->id}}" style="background-color: @if(isset($childItem->status->color)) {{$childItem->status->color}}  @endif">
                    <div class="task_container">
                    <div>
                      <strong>{{ $childItem->points }} </strong>
                    </div>
                    <a id="#ref_{{$childItem->id}}" href="#ref_{{$childItem->id}}" onclick="updateNextStatusAjax({{$childItem->id}});">
                      {{$childItem->status->name}}
                    </a>
                    <div>
                      
                    </div>
                    </div>
                  </td>
                </tr>
                <?php 
                  $last_task_status_id = $childItem->status_id;
                  $last_project_id = $childItem->project_id;
                if(isset( $childItem->points )){$sumPoints += $childItem->points; }
                  $totalTask++;
                ?>
              </form>  
@endforeach
@endif

        <!-- Fin del los hijos
        ********************************************
        -->  
        

                  
 				<!--- fin del ciclo de tareas -->
        @endforeach
        </tbody>
        <thead class="thead-dark">
          <tr>
                  <td></td>
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                  <td class="no_print"></td>
                  <td class="no_print"></td>
                  <td class="no_print"></td>
                  <td class="no_print"></td>
                  @endif
                  <td></td>
                  <td>Total Points</td>
                  <td class="task_container">{{ $sumPoints }}</td>
                  
                  </tr>
                  <tr>
                  <td></td>
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                  <td class="no_print"></td>
                  <td class="no_print"></td>
                  <td class="no_print"></td>
                  <td class="no_print"></td>
                  @endif
                  <td></td>
                  <td>Total Task</td>
                  <td class="task_container">{{ $countTask }}</td>
                  
                  </tr>
        </thead>
      
    </table>
    <form id="codigoSeg" style="display: none">
        {{ csrf_field() }}
    </form>
    
  </div>
  
<div>
     
</div>


@endsection

@section('footerjs')
<script>
    
$(document).ready(function(){
  update();

});


function showTeam(){
  if($('.toggle').css("display")=="none")
    $('.toggle').show();
  else
    $('.toggle').hide();

}  


jQuery( document ).ready(function(){
  // vars
  startArray = [];
  finalArray = [];

  child = 0;

  function startDrag(ui){

  }  
  function stopDrag(ui){

  }

  function getTaskList(vector){
    array = Array();
    
    for(i=0; i<vector.length; i++){

      if(vector[i].indexOf("task_id_")!=-1)
        array.push( parseInt( vector[i].replace('task_id_', '') ));
        //array.push( vector[i] );
    }
    return array;
  }

  function getParent(vector, child){
    parent = "";
    for(i=0; i<vector.length; i++){

      if(vector[i] == child)
        parent = vector[i+1];
    }
    return parent;
  }

  function setParentAjax(child, parent){
    endpoint = '/tasks/'+child+'/setParent/'+parent;
    console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        success: function (data) {
            console.log(data);
            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }


  


  function isEqual(v1, v2){
    equal = true;
    if(v1.length == v2.length){
      for(i=0; i<v1.length; i++)
        if(v1[i]!=v2[i])
          equal = false;
    }else{
      equal = false;
    }
    return equal;
  }

  dragged = null;

  $(function() {
    $("#sortable tbody").sortable({
      cursor: "move",
      placeholder: "sortable-placeholder",
      start: function( event, ui ) {
        startArray = getTaskList($( "#sortable tbody" ).sortable( "toArray" ));
        console.log(startArray);
        console.log("start");
        child = parseInt($(ui.item).attr('id').replace('task_id_', ''));
        console.log(child);
      },
      stop: function( event, ui ) {
        finalArray = getTaskList($( "#sortable tbody" ).sortable( "toArray" ));

        console.log(finalArray);

        console.log("stop");
        if(!isEqual(startArray, finalArray)){
          parent = getParent(finalArray, child);
          if(parent == undefined)
            parent = -1;
          console.log(parent);
          setParentAjax(child, parent);
          //ui.item.html="";
          dragable = ui;
          dragable.item.css('display', 'none');
        }

      },
    }).disableSelection();
  });





});

function updateTypeAjax(tid, tyid){
	console.log(tyid);
	type_id = "0";
	if (!isNaN(parseInt(tyid)))
		type_id = tyid;

    endpoint = '/tasks/'+tid+'/setType/'+type_id;
    
    console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            showSubTypes(tid, data);
            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }


function updateSubTypeAjax(tid, tyid){
	console.log(tyid);
	type_id = "0";
	if (!isNaN(parseInt(tyid)))
		type_id = tyid;

    endpoint = '/tasks/'+tid+'/setSubType/'+type_id;
    
    console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            console.log(data);
            showSubTypes(tid, data);
            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }


  function showSubTypes(tid, data){
  	str = '<select name="subtype_id" id="subtype_id_"'+tid+' class="custom-select" onchange="updateSubTypeAjax('+tid+', this.value );">;';
  	str += '<option>Select a type...</option>';
  	$.each(data, function(i, obj) {
	  str += '<option value="'+obj.id+'">'+obj.name+'</option>';
	});

	str += '</select>';



  	$("#after_type_"+tid).html(str);
  }

</script>
@endsection

