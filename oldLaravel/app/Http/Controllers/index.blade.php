@extends('layout')

@section('content')
<br>
<h1>Tasks</h1>

@include('tasks.createForm')
@include('tasks.filter')

<?php 
  $sumPoints = 0; 
  $countTask = 0;
  $max_items = 5;
  $totalTask = 0;


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
      colors[{{$status->id}}] = "{{$status->background_color}}"; 
      text[{{$status->id}}] = "{{$status->name}}"; 
    @endforeach
    
    </script>

        <div class="bigimage-container" id="bigimage-container">
          <img src="" alt="" width="100%" class="task_image_big" id="bigimage-file"> 
        </div>
        

  <div class="table-responsive" id="">
            <table class="table table-sm borderless" id="taskTable">
              
                

<?php $last_task_status_id=-1;
      $last_project_id = -1;
 ?>

              <tbody>
   @foreach($model as $item)
        <!-- Inicio ldel cilco de tareas -->


@if($last_project_id!=$item->project_id)
                <tr>
                  <th colspan="6">&nbsp;</th>
                </tr>
                <tr>
                  <th>
                    <a href="/projects/{{$item->project_id}}" style="font-size:larger;color:{{$item->project->color}}">{{$item->project->name}}</a>
                    <i onclick="openTaskModal({{$item->project_id}},{{Auth::user()->id}});" class="fas fa-plus-circle" style="color:{{$item->project->color}}"></i>
                  </th>
                  <th>&nbsp;</th>
                  <th>Priority</th>
                  <th>Points</th>
                  <th>Person</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              
                  <?php $last_task_status_id=-1; ?>
                  @endif
                 
                  
                

<form action="" method="POST" name="updateTaskForm{{$item->id}}" id="updateTaskForm{{$item->id}}">
                  {{ csrf_field() }}


  
    <tr class="task_row edit-container" data-id="{{$item->id}}"  id="task_id_{{$item->id}}">
                    <input type="hidden" name="token" id="token_id_{{$item->id}}" value="{{csrf_token()}}">
                    <input type="hidden" name="id" id="task_id__{{$item->id}}" value="{{$item->id}}">

                    
                    

                 
                  <td id="{{$item->id}}" >
                  
                  
                  
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  {{$item->type_id}}  - 

                  <a href="/tasks/{{ $item->id }}"  class="no_print">
                    

                    {{$item->nameSubstr(50)}} 

                  </a>
                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )         
                    <!--<a href="/tasks/{{$item->id}}/edit" class="no_print ">-->
                      <i class="fas fa-edit hide" data-toggle="modal" data-target="#editModal_{{$item->id}}" 
                        onclick="getMessages({{$item->id}},{{$item->getUser(Auth::user()->id)}})"></i>
                        <input type="hidden"  id="task_id_m">

                        
                  
                    <!-- Modal -->
                    <div class="modal fade" id="editModal_{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                            <div class="modal-body">
                                <div class="row">
                                  <div class="col-md">
                                    <div class="form-group">
                                      <input type="hidden" name="task_id_{{$item->id}}" id="task_id_{{$item->id}}">
                                      <label for="name">Name</label>
                                      <input type="text" class="form-control" id="name_{{$item->id}}" name="name_{{$item->id}}" placeholder="Name" required="required" value="{{$item->name}}">
                                    </div>
                                    <div class="form-group">
                                      <label for="user_id">Project</label>
                                      <select name="project_id_{{$item->id}}" id="project_id_{{$item->id}}" class="form-control" required="required">
                                        <option value="">Select a Project</option>
                                      @foreach ($projects as $project)
                                          <option value="{{$project->id}}" @if($item->project_id == $project->id) selected="" @endif>{{$project->name}}</option>
                                      @endforeach
                                      </select>
                                    </div>
                                    <div class="form-group">
                                      <label for="priority">Priority</label>    
                                      <input type="text" class="form-control" id="priority_{{$item->id}}" name="priority_{{$item->id}}" placeholder="Priority" value="{{$item->priority}}">
                                    </div>

                                    <div class="form-group">
                                      <label for="due_date">Due Date</label>
                                      
                                      <input type="date" class="form-control" id="due_date_{{$item->id}}" name="due_date_{{$item->id}}" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date("Y-m-d", strtotime($item->due_date)); ?>">
                                    </div>
                                    <div class="form-group row">
                                      <label for="not_billing" class="col-6">Not Billing</label>
                                      <input type="checkbox" class="form-control col-1" id="not_billing_{{$item->id}}" name="not_billing_{{$item->id}}" value="{{$item->not_billing}}">
                                    </div> 
                                  </div>

                                  <div class="col-md">
                                    <div class="form-group">
                                      <label for="user_id">User</label>
                                      <select name="user_id_{{$item->id}}" id="user_id_{{$item->id}}" class="form-control">
                                        <option value="">Select a User</option>
                                      @foreach ($users as $user)
                                          <option value="{{$user->id}}" @if($item->user_id == $user->id) selected="" @endif>{{$user->name}}</option>
                                      @endforeach
                                      </select>
                                    </div>

                                    <div class="form-group">
                                     <label for="status_id">Status</label>
                                       <select name="status_id_{{$item->id}}" id="status_id_{{$item->id}}" class="form-control" >
                                      @foreach($task_status as $status)
                                          <option value="{{$status->id}}" @if($item->status_id == $status->id) selected="" @endif>{{$status->name}}</option>
                                      @endforeach
                                      ?>
                                      </select>
                                    </div>

                                    <div class="form-group">
                                      <label for="points">Points</label>    
                                      <input type="text" class="form-control" id="points_{{$item->id}}" name="points_{{$item->id}}" placeholder="Points" value="{{$item->points}}">
                                    </div>



                                    <div class="form-group">
                                      <label for="file">File</label>
                                      <input type="file" class="form-control" id="file_{{$item->id}}" name="file_{{$item->id}}" placeholder="Name">
                                    </div>

                                    <div class="form-group">
                                    <label for="url_finished">Url Finished Task </label>
                                      <input class="form-control" name="url_finished_{{$item->id}}" id="url_finished_{{$item->id}}" placeholder="Url" value="{{$item->url_finished}}">
                                    </div>
                                  </div>
                                  <div class="form-group col-md-12">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" name="description_{{$item->id}}" id="description_{{$item->id}}" cols="30" rows="2" value="{{$item->description}}">{{$item->description}}</textarea>
                                  </div>
                                  <input type="hidden" name="from_{{$item->id}}" id="from_{{$item->id}}" class="form-control" value="project">
                                  <div class="form-group col-md-12">
                                    <button id="btnCancel" class="btn btn-sm btn-warning my-2 my-sm-0" data-dismiss="modal">Cancel</button>
                                    <button id="btn_edit_{{$item->id}}" onclick="updateTaskFromModal({{$item->id}});" class="btn btn-sm btn-primary my-2 my-sm-0">Update</button>
                                    
                                  </div>
                                       <div >
                                               <label style="margin-left: 10px;"><strong>Commentary</strong></label>
                                           <a  data-toggle="modal" data-target="#messageModal" >
                                             <img src="/images/mas.png" style="width: 25px">
                                           </a> 
                                           
                                            <input type="text" style="margin-left: 15px;" name="description" id="description_message" class="form-control">
                                             <input type="hidden" name="" value="" id="task_id">
                                       
                                        
                                        <div style="margin-top:8px;">
                                          <a class="btn btn-sm btn-primary my-2 my-sm-0" style="margin-top: 5px;color: #ffff !important; margin-left: 15px;" onclick="sendMessage({{$item->id}},{{$item->getUser(Auth::user()->id)}} );">Save</a>
                                        </div>
                                      </div>

                                  <div  style="width: 100% !important;" id="count_messages_{{$item->id}}"> 
                                  </div>
                                    
                              </div>
                                
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>


<!--modal mensajes-->
 <div class="modal_messages" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" 
                  aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content-messages" id="modal-messages" >
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Send Message</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        
          <div class="form-group">
            <label for="message">Description:</label>
            <input type="text" name="description" id="description_message" class="form-control">
             <input type="hidden" name="" value="" id="task_id">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary"  data-dismiss="modal">Close</button>
          <a class="btn btn-primary" onclick="sendMessage({{$item->id}},{{$item->getUser(Auth::user()->id)}} );">Save</a>
        </div>
      </div>
    </div>
</div>


                  @endif 
         <i class="fas fa-filter hide select-container"  id="demo_{{$item->id}}"  onclick="hideSubTypes('{{$item->id}}')" ></i>
             

                   @if(isset($item->url_finished) && ($item->url_finished!=null))
                    <a href="{{$item->url_finished}}" target="_blanck">  <i class="fas fa-link"></i>
                      </a>
                      @endif

        
                  <div class="sub-hide_{{$item->id}}" id="sub">
                      @if(isset($task_types))
                <select name="type_id" id="type_id_{{$item->id}}" class="custom-select" onchange="updateTypeAjax({{$item->id}}, this.value );">
                        <option>Select a type...</option>
                        @foreach($task_types as $key=>$value)
                        <option value="{{$key}}" @if($item->type_id==$key) selected="selected" @endif >{{substr($value,0,20)}}</option>
                        @endforeach  
                </select>
                      
                      @endif
                    </div>
                    <div class="just_print">{{$item->nameSubstr(100)}}  </div>

                  @else
                    <div>
                      <h5 class="task_title">{{$item->name}}</h5> 
                    
                      <div class="task_description">{{$item->description}}</div>
                    </div> 
                  @endif

          <input type="hidden" id="observer_id" name="observer_id" value="{{Auth::id()}}" >
          
          <input type="hidden" id="status_id_{{$item->id}}" name="status_id" value="{{$item->status_id}}" >

                    <div id="mini_container no_print" >
                      @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                    @endif
           

                  </td>

                  <td >
                    <!--  estaba -->
                  </td>

                  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
                  
                  
                  @if($item->priority > 0 && $item->priority <= 3)
                    <td class="no_print text-center"><i class="fas fa-exclamation-triangle" style="color: green;" title="{{$item->priority}}"></i></i></td>
                  @elseif($item->priority > 3 && $item->priority <= 6)
                    <td class="no_print text-center"><i class="fas fa-exclamation-triangle" style="color: orange;" title="{{$item->priority}}"></i></i></td>
                  @elseif($item->priority > 6 && $item->priority <= 10)
                    <td class="no_print text-center"><i class="fas fa-exclamation-triangle" style="color: red;" title="{{$item->priority}}"></i></i></td>
                  @else
                    <td class="no_print text-center"></td>
                  @endif 


                  <td class="no_print text-center">{{ $item->points }}</td>
                  <td class="no_print text-center" >
                  <div class="user_image_container no_print">
                  <div class="user_image_mini_container no_print"  >
                        <a hrefd="#" onclick="showTeam({{$item->id}})" alt="Asignar responsable" >
                        
                @if(isset($item->user)&&!isset($item->user->image_url)&&isset($item->user_id)&&($item->user_id!=""))
                      <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="" class="user_image user_image_mini" id="task_user_image_{{$item->id}}">
                  
                  @else
                    @if(isset($item->user->image_url))
                  
                    <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="" class="user_image user_image_mini" id="task_user_image_{{$item->id}}">
                  
                    @endif
                       </a>
                      </div>


                    
                    @foreach($users as $user)
                      <div class="user_image_mini_container toggle_{{$item->id}}" id="user_{{$item->id}}_{{$user->id}}" style="display: none" > 

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
                  </div>
                   @endif
                   </td>

                   <td id="task_cel_{{$item->id}}" style="background-color: @if(isset($item->status->background_color)) {{$item->status->background_color}}  @endif">
                    <div class="task_container">
                    
                      
                    <a id="#ref_{{$item->id}}" href="#ref_{{$item->id}}" onclick="updateNextStatusAjax({{$item->id}});">
                      @if(isset($item->status))
                      {{$item->status->name}}
                      @endif
                    </a>
                    <div>
                      
                    </div>
                    </div>
                  </td>

                  <td class="no_print" id="date_{{$item->id}}">
                    @if(isset($item->due_date))
          <strong>{{ date('M-d', strtotime($item->due_date)) }}</strong>
        @else N/A @endif

                  </td>
                  <td>
                    <a  href="#ref_{{$item->id}}"  class="btn btn-sm btn-primary my-2 my-sm-0">
                      <i id="#ref_{{$item->id}}" onclick="updateDate({{$item->id}});"  class="fas fa-redo" title="Update task due date"></i>
                    </a>
                  </td>
                  
                 
                  
                  
                  
                <?php 
                  $last_task_status_id = $item->status_id;
                  $last_project_id = $item->project_id;
                if(isset( $item->points )){$sumPoints += $item->points; }
                  $countTask++;
                ?>  
    </tr>
  



</form>
  <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

          <div class="row">
            <div class="col-md">
              <div class="form-group">
                <input type="hidden" name="id" id="id">
                <label for="task_name">Name</label>
                <input type="text" class="form-control" id="task_name" name="task_name" placeholder="Name" required="required">
              </div>
              <div class="form-group">
                <label for="user_id">Project</label>
                <select name="task_project_id" id="task_project_id" class="form-control" required="required">
                  <option value="">Select a Project</option>
                @foreach ($projects as $project)
                    <option value="{{$project->id}}">{{$project->name}}</option>
                @endforeach
                </select>
              </div>

              <div class="form-group">
                <label for="task_priority">Priority</label>    
                <input type="text" class="form-control" id="task_priority" name="task_priority" placeholder="Priority" >
              </div>

              <div class="form-group">
                <label for="task_due_date">Due Date</label>
                <input type="date" class="form-control" id="task_due_date" name="task_due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date('Y-m-d');?>">
              </div>

              <div class="form-group row">
                <label for="task_not_billing" class="col-6">Not Billing</label>
                <input type="checkbox" class="form-control col-1" id="task_not_billing" name="task_not_billing">
              </div> 


            </div>
            <div class="col-md">
              <div class="form-group">
                <label for="task_user_id">User</label>
                <select name="task_user_id" id="task_user_id" class="form-control">
                  <option value="">Select a User</option>
                @foreach ($users as $user)
                    <option value="{{$user->id}}" @if ($user->id == $request->user_id) selected="selected" @endif>{{$user->name}}</option>
                @endforeach
                </select>
              </div>

              <div class="form-group">
               <label for="task_status_id">Status</label>
                 <select name="task_status_id" id="task_status_id" class="form-control" >
                @foreach($task_status as $item)
                    <option value="{{$item->id}}" @if($item->id == 1) selected="" @endif>{{$item->name}}</option>
                @endforeach
                ?>
                </select>
              </div>

              <div class="form-group">
                <label for="task_points">Points</label>    
                <input type="text" class="form-control" id="task_points" name="task_points" placeholder="Points">
              </div>



              <div class="form-group">
                <label for="task_file">File</label>
                <input type="file" class="form-control" id="task_file" name="task_file" placeholder="Name">
              </div>

              <div class="form-group">
              <label for="task_url_finished">Url Finished Task </label>
                <input class="form-control" name="task_url_finished" id="task_url_finished" placeholder="Url" value="" >
              </div>
            </div>
            <div class="form-group col-md-12">
              <label for="task_description">Description</label>
              <textarea class="form-control" name="task_description" id="task_description" cols="30" rows="2"></textarea>
            </div>
            <input type="hidden" name="from" id="from" class="form-control" value="project">
            <div class="form-group col-md-12">
              <button id="btnCancel" class="btn btn-sm btn-warning my-2 my-sm-0" data-dismiss="modal">Cancel</button>
              <button id="btnAdd" class="btn btn-sm btn-primary my-2 my-sm-0">Create</button>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>






        <!--- fin del ciclo de tareas -->
        @endforeach
        </tbody>
        
      
    </table>
    <div>{{ $sumPoints }} ptos/ {{ $countTask }}</div>
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
    /*ocular*/
     $(" #sub").css("visibility", "hidden");
     $(" #sub").css("display", "none");
  });
    
  function hideSubTypes(id) {
    var type = $(".sub-hide_"+id).attr("style");

    if(type == "visibility: visible; display: inline-block;"){
      $(".sub-hide_"+id).css("visibility", "hidden");
      $(".sub-hide_"+id).css("display", "none");
    }else if(type == "visibility: hidden; display: none;"){
      $(".sub-hide_"+id).css("visibility", "visible");
      $(".sub-hide_"+id).css("display", "inline-block");
    }
  }
</script>







<script>
    
$(document).ready(function(){
  update();

});

function openTaskModal(project_id,user_id){
  console.log(user_id);
    cleanFormModal();
    $("#task_project_id option[value='"+project_id+"']").attr("selected", true);
    $("#task_user_id option[value='"+user_id+"']").attr("selected", true);
    $("#exampleModal").modal();
    
}

function openEditTaskModal(){
  
  cleanFormModal();
    /*
    console.log(user_id);
    
    $("#task_project_id option[value='"+project_id+"']").attr("selected", true);
    $("#task_user_id option[value='"+user_id+"']").attr("selected", true);
    */
    $("#editModal").modal();
    
  }


function cleanFormModal(){
            $("#task_name").val("");
            /*$("#task_status_id").val("");*/
            /*$("#task_project_id").val("");*/
            /*$("#task_user_id").val("");*/
            $("#task_priority").val("");
            /*$("#task_due_date").val("");*/
            $("#task_not_billing").val("");
            $("#task_points").val("");
            $("#task_file").val("");
            $("#task_url_finished").val("");
            $("#task_description").val("");
          }

$("#btnAdd").click(function(){
  $("#btnAdd").attr('disabled','disabled');
  objEvent = getDataGUI();
  sendRequest('',objEvent, "GET");
})

function getDataGUI(){
  newEvent={
    name : $("#task_name").val(),
    status_id : $("#task_status_id").val(),
    project_id : $("#task_project_id").val(),
    user_id : $("#task_user_id").val(),
    priority : $("#task_priority").val(),
    due_date : $("#task_due_date").val(),
    not_billing : $("#task_not_billing").val(),
    points : $("#task_points").val(),
    file : $("#task_file").val(),
    url_finished : $("#task_url_finished").val(),
    description : $("#task_description").val(),
    '_token':$("input[name=_token]").val()
  }
  return newEvent;
}

function sendRequest(action, objEvent, method, modal){
  console.log(objEvent);
  $.ajax({
    type : method,
    url : "{{url('/task_from_calendar')}}" + action,
    data:objEvent,
    success:function(msg){
      if(!modal){
        $("#exampleModal").modal('toggle');
        location.reload();
      }
    },
    error:function(){
      alert("Error");
    }
  });
}





function updateTaskFromModal(id){
  $("#btn_edit_"+id).attr('disabled','disabled');
  objEvent = getDataGUIEdit(id);
  sendRequestEdit('',objEvent, "POST",id);
}


function getDataGUIEdit(id){
  newEvent={
    name : $("#name_"+id).val(),
    status_id : $("#status_id_"+id).val(),
    project_id : $("#project_id_"+id).val(),
    user_id : $("#user_id_"+id).val(),
    priority : $("#priority_"+id).val(),
    due_date : $("#due_date_"+id).val(),
    not_billing : $("#not_billing_"+id).val(),
    points : $("#points_"+id).val(),
    file : $("#file_"+id).val(),
    url_finished : $("#url_finished_"+id).val(),
    description : $("#description_"+id).val(),
    '_token':$("input[name=_token]").val()
  }
  return newEvent;
}

function sendRequestEdit(action, objEvent, method, task_id, modal){ 
  console.log(objEvent);
  var tid = task_id;
  var url = '/task_from_calendar/'+tid+'/update';
  $.ajax({
    type : method,

    


    url : url,
    data:objEvent,
    success:function(msg){
      if(!modal){
        $("#editModal_"+tid).modal('toggle');
        location.reload();
      }
    },
    error:function(){
      alert("Error");
    }
  });
}









function showTeam(id){
  if($('.toggle_'+id).css("display")=="none")
    $('.toggle_'+id).show();
  else
    $('.toggle_'+id).hide();

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
            //showSubTypes(tid, data);
            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }

  


function updateSubTypeAjax(tid, tyid){


  //console.log(tyid);
  type_id = "0";
  if (!isNaN(parseInt(tyid)))
    type_id = tyid;

    endpoint = '/tasks/'+tid+'/setSubType/'+type_id;
    
   // console.log(endpoint);
    $.ajax({
        type: 'GET', //THIS NEEDS TO BE GET
        url: endpoint,
        dataType: 'json',
        success: function (data) {
            //console.log(data);
            
              $(" #subtype_id_"+tid).css("visibility", "hidden");
              $(" #subtype_id_"+tid).css("display", "none");
            console.log(data);
            location.reload();

            
        },
        error: function(data) { 
             console.log(data);
        }
    });

  }


  function showSubTypes(tid, data){
    //subtype_id_31520
   
     $(" #type_id_"+tid).css("visibility", "hidden");
    $(" #type_id_"+tid).css("display", "none");
    str = '<select name="subtype_id" id="subtype_id_'+tid+'" class="custom-select" onchange="updateSubTypeAjax('+tid+', this.value );">;';
    str += '<option>Select a type...</option>';
    $.each(data, function(i, obj) {
    str += '<option value="'+obj.id+'">'+obj.name+'</option>';
  });

  str += '</select>';



    $("#after_type_"+tid).html(str);
  }

   function getMessages(task_id,creator_user){  
              $("#task_id_m").val(task_id);
                        var str = "";

                        $.ajax({
                        type: "GET",
                        url :"/task/get_messages/"+task_id,
                        success : function(res){
                          $.each(res, function(i, obj) {

                                        str +='<table >';
                                        if(creator_user.id == obj.id){
                                        str +='<tr >';
                                        str += '<td  style="float: left;background-color: transparent;border-top: 0px;"><img  style="clip-path: circle(13px at center);width: 26px;margin-left: 10px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                                        str += '<td class="creator" style="width: 250px;float: left;background-color: transparent; border-top: 0px;" ><input class=" form-control" disabled value="'+obj.description+'" style="background: #ffffff; box-shadow: 0px 4px 4px rgb(50 50 71 / 8%), 0px 4px 8px rgb(50 50 71 / 6%);border-radius: 20px;padding: 15px 12px !important;" >'+'</input></td>';
                                        str +='</tr>';
                                        }else{
                                           str +='<tr >';
                                        str += '<td  style="float: right;background-color: transparent;border-top: 0px;"><img  style="clip-path: circle(13px at center);width: 26px;" src="/laravel/storage/app/public/files/users/'+ obj.image_url+' "</td>';
                                        str += '<td class="user" style="width: 250px;float: rigth;background-color: transparent; border-top: 0px;" ><input class=" form-control" disabled value="'+obj.description+'" style="width: 250px;float: right;background-color: #2196F3; border-radius: 20px;padding: 15px 12px !important;" " >'+'</input></td>';
                                        str +='</tr>';
                                        }
                             str +='</table>';   

                             $("#count_messages_"+task_id).html(str); 
                    
                              });

                          console.log("get"+res);
                        },
                      },"html");
                    }

           function sendMessage(task_id,user_id){  
            
                  var description = $("#description_message").val();
                  var task_id_m = $("#task_id_m").val();
                
                    $.ajax({
                        type: "GET",
                        url : "/task/message/"+task_id_m+"/"+user_id.id+"/"+description,
                        success : function(res){
                           $("#messageModal input").val("");
                           $("#messageModal .close").click()
                            getMessages(task_id_m,user_id); 
                        },
                        error:function(){
                            alert("Error");
                        }
                    },"html");
            }







</script>
    
<style type="text/css">
      th, td {
        padding: 9px 0px !important;
        }
        .modal_messages{
          position: fixed;
          top: 0;
          left: 0;
          z-index: 999999999999999;
          display: none;
          width: 100%;
          height: 100%;
          overflow: hidden;
          outline: 0;
          box-shadow: 0px 10px 10px black; 
        }
   
        .modal-content-messages {
         box-shadow: 0px 10px 10px #0000005c;
             position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            width: 70%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: .3rem;
            outline: 0;
            top:175px;
            left: 65px;
        }



     </style>
                    
                  



@endsection

