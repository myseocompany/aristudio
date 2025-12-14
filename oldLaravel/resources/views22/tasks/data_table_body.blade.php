<!-- Inicio del ciclo de tareas -->
<tr class="task_row edit-container"  data-id="{{$item->id}}"  id="task_id_{{$item->id}}" draggable='true' ondragstart='dragStart()' ondragover='dragOver()'>
    <input type="hidden" name="token" id="token_id_{{$item->id}}" value="{{csrf_token()}}">
    <input type="hidden" name="id" id="task_id__{{$item->id}}" value="{{$item->id}}">

      


   
    <td id="{{$item->id}}" >
    
    
    
    @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
    
    
   <div>
        <a href="/tasks/{{$item->id}}" class="no_print">
            {{str_limit($item->name, 50)}}   
        </a>
   </div>
   <a href="/projects/{{$item->project_id}}">
    <span class="badge" style="background-color: {{$item->project->color}}; color: white">
      
        {{$item->project->name}}
      
    </span>
  </a>
    @if(isset($item->type_id))
    <span class="badge" style="background-color: {{$item->project->color}}; color: white">{{$item->type->name}}  -</span> 
    @endif
      

       

    
    @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )         
      <!--<a href="/tasks/{{$item->id}}/edit" class="no_print ">-->
        <i class="fas fa-edit hide editTaskBtn" data-toggle="modal" data-target="#editTaskModal"
        data-task="{{ htmlspecialchars(json_encode($item->forModal()), ENT_QUOTES, 'UTF-8') }}" 
          onclick="getMessages({{$item->id}},{{$item->getUser(Auth::user()->id)}})"></i>
          <input type="hidden"  id="task_id_m">

          





    @endif 
<i class="fas fa-filter hide select-container"  id="demo_{{$item->id}}"  onclick="hideSubTypes('{{$item->id}}')" ></i>
<i onclick="openTaskModal({{$item->project_id}},{{Auth::user()->id}});" class="fas fa-plus-circle" style="color:{{$item->project->color}}"></i>



     @if(isset($item->url_finished) && ($item->url_finished!=null))
      <a href="{{$item->url_finished}}" target="_blanck">  <i class="fas fa-link"></i>
        </a>
        @endif


    <div class="sub-hide_{{$item->id}}" id="sub">
        @if(isset($task_types))
        <select name="type_id" id="type_id_{{$item->id}}" class="custom-select" onchange="updateTypeAjax({{$item->id}}, this.value );">
                <option>Select a type...</option>
                @foreach($task_types as $key=>$value)
                <option value="{{$key}}" @if($item->type_id==$key) selected="selected" @endif >{{substr($value,0,40)}}</option>
                @endforeach  
        </select>
        
        @endif
      </div>
      <div class="just_print">::{{$item->nameSubstr(100)}}  </div>

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
    <td>
      {{$item->due_date}}
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


    <td class="no_print text-center">{{ $item->getPointsAsTimeString() }}</td>
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
      <a  href="#ref_{{$item->id}}"  class="btn btn-sm btn-warning my-2 my-sm-0">
        <i id="#ref_{{$item->id}}" onclick="setStatusAjax({{$item->id}}, 2);"  class="fas fa-spinner" title="Ver task due date"></i>
      </a>
    </td>
    <td>
      <a  href="#ref_{{$item->id}}"  class="btn btn-sm btn-primary my-2 my-sm-0">
        <i id="#ref_{{$item->id}}" onclick="setStatusAjax({{$item->id}}, 6);"  class="fas fa-check" title="Ver task due date"></i>
      </a>
    </td>
    <td>
      <a  href="#ref_{{$item->id}}"  class="btn btn-sm btn-success my-2 my-sm-0">
        <i id="#ref_{{$item->id}}" onclick="setStatusAjax({{$item->id}}, 56);"  class="fas fa-file-invoice" title="Ver task due date"></i>
      </a>
    </td>
    <td>
      <a  href="#ref_{{$item->id}}"  class="btn btn-sm btn-secondary my-2 my-sm-0">
        <i id="#ref_{{$item->id}}" onclick="setStatusAjax({{$item->id}}, 4);"  class="fas fa-times" title="Cancel task due date"></i>
      </a>
    </td>
    <td>
      <a  href="#ref_{{$item->id}}"  class="btn btn-sm btn-info my-2 my-sm-0">
        <i id="#ref_{{$item->id}}" onclick="updateDate({{$item->id}});"  class="fas fa-calendar-check" title="Update task due date"></i>
      </a>
    </td>
   
    
    
    
  <?php 
    $last_task_status_id = $item->status_id;
    $last_project_id = $item->project_id;
  if(isset( $item->points )){$sumPoints += $item->points; }
    $countTask++;
  ?>  
</tr>
<!--- fin del ciclo de tareas -->