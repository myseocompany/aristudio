<!-- Inicio del ciclo de tareas -->
<tr class="task_row edit-container" data-id="{{$item->id}}" id="task_id_{{$item->id}}" draggable='true' ondragstart='dragStart()' ondragover='dragOver()'>
  <input type="hidden" name="token" id="token_id_{{$item->id}}" value="{{csrf_token()}}">
  <input type="hidden" name="id" id="task_id__{{$item->id}}" value="{{$item->id}}">
 
<!-- Inicio de barra de estado --> 
  <!-- if(isset($item->type_id))
  <td colspan="13">
   <h4> <a href="/tasks/{{$item->id}}">{{$item->type->name}}</a> </h4>
  </td>
-->
  <td class="text-center align-middle">
      <div class="d-md-none mr-2">
          @if(isset($item->status))
          <span class="badge badge-info justify-content-center align-items-center p-1" 
                id="mobile_status_{{$item->id}}" 
                style="background-color: @if(isset($item->status->background_color)) {{$item->status->background_color}} @endif; 
                      font-size: 0.9rem;
                      height: 80px">
                      
          </span>
          
      </div>
  </td>

<!-- Foto de perfil usuario -->
<td class="no_print text-center mr-2">
  <div class="user_image_container no_print mr-3">
    <div class="user_image_mini_container no_print">
      <a href="#" onclick="showTeam({{$item->id}})" alt="Asignar responsable">
        @if(!empty($item->user->image_url))

        <img src="/laravel/storage/app/public/files/users/{{$item->user->image_url}}" alt="{{$item->user->name}}" class="user_image user_image_mini" id="task_user_image_{{$item->id}}">
        @else
        <img src="/laravel/storage/app/public/files/users/user.png" alt="Sin asignar" class="user_image user_image_mini" id="task_user_image_{{$item->id}}">
        @endif
      </a>
    </div>

    @foreach($users as $user)
    <div class="user_image_mini_container toggle_{{$item->id}}" id="user_{{$item->id}}_{{$user->id}}" style="display: none">

      <a hrefd="#user_{{$item->id}}_{{$user->id}}" onclick="updateUserAjax({{$item->id}},{{$user->id}});" alt="{{$user->name}}">

        @if(isset($user->image_url))
        <img src="/laravel/storage/app/public/files/users/{{$user->image_url}}" alt="{{$user->name}}" title="{{$user->name}}" class="user_image_mini" border="0">
        @else
        <img src="/laravel/storage/app/public/files/users/user.png" alt="{{$user->name}}" title="{{$user->name}}" class="user_image_mini" border="0">

        @endif
      </a>

    </div>
    @endforeach
    <!-- Mostrar solo en versión móvil -->
    <div class="d-md-none">
      <span>{{ $item->getPointsAsTimeString() }}</span>
      <div>
        @if(isset($item->due_date))
        <strong id="mobile_date_{{$item->id}}">{{ date('M-d', strtotime($item->due_date)) }}</strong>
        @else N/A @endif
    </div>
    </div>
  </div>
</td>
<!-- Fin foto de perfil usuario -->



  

  <td id="{{$item->id}}">
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

    @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
    <i class="fas fa-edit hide editTaskBtn" data-toggle="modal" data-target="#editTaskModal"
      data-task="{{ htmlspecialchars(json_encode($item->forModal()), ENT_QUOTES, 'UTF-8') }}"
      onclick="getMessages({{$item->id}},{{$item->getUser(Auth::user()->id)}})"></i>
    <input type="hidden" id="task_id_m">
    @endif
    <i class="fas fa-filter hide select-container" id="demo_{{$item->id}}" onclick="hideSubTypes('{{$item->id}}')"></i>
    <!-- i onclick="openTaskModal({{$item->project_id}},{{Auth::user()->id}});" class="fas fa-plus-circle" style="color:{{$item->project->color}}"></i -->
    <a 
      id="ref_{{$item->id}}" 
      href="#ref_{{$item->id}}" 
      onclick="handleValueGenerated(event, {{$item->id}});" 
      class="px-2 py-1 text-white rounded shadow-md transition-all duration-300 btn-sm"
      style="background-color: {{ $item->value_generated == 1 ? '#48bb78' : '#f56565' }};">
      <i class="fas fa-check-circle"></i>
    </a>



    @if(isset($item->url_finished) && ($item->url_finished!=null))
    <a href="{{$item->url_finished}}" target="_blanck"> <i class="fas fa-link"></i>
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
    <div class="just_print">::{{$item->nameSubstr(100)}} </div>

    @else
    <div>
      <h5 class="task_title">{{$item->name}}</h5>
      <div class="task_description">{{$item->description}}</div>
    </div>
    @endif
    <input type="hidden" id="observer_id" name="observer_id" value="{{Auth::id()}}">
    <input type="hidden" id="status_id_{{$item->id}}" name="status_id" value="{{$item->status_id}}">

    <div id="mini_container no_print">
      @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
      @endif

  </td>

  <td class="d-none d-md-table-cell">
    <div class="flex flex-col">
        <span>{{$item->due_date}}</span>
    </div>
    <div class="flex flex-col">
        <span>{{$item->delivery_date}}</span>
    </div>
</td>

  @if((Auth::user()!=null) && (Auth::user()->role_id!=3) )
  @if($item->priority > 0 && $item->priority <= 3)
    <td class="no_print text-center d-none d-md-table-cell"><i class="fas fa-exclamation-triangle" style="color: green;" title="{{$item->priority}}"></i></td>
    @elseif($item->priority > 3 && $item->priority <= 6)
      <td class="no_print text-center d-none d-md-table-cell"><i class="fas fa-exclamation-triangle" style="color: orange;" title="{{$item->priority}}"></i></td>
      @elseif($item->priority > 6 && $item->priority <= 10)
        <td class="no_print text-center d-none d-md-table-cell"><i class="fas fa-exclamation-triangle" style="color: red;" title="{{$item->priority}}"></i></td>
        @else
        <td class="no_print text-center d-none d-md-table-cell"></td>
        @endif
        @endif

        <td class="no_print text-center d-none d-md-table-cell">{{ $item->getPointsAsTimeString() }}</td>

        

        <!-- Estado de la tarea (solo en escritorio) -->
        <td id="task_cel_{{$item->id}}" class="d-none d-md-table-cell" style="background-color: {{ $item->status->background_color ?? 'transparent' }};">
          <div class="task_container">
            <a id="#ref_{{$item->id}}" hrefu="#ref_{{$item->id}}" onclick="updateNextStatusAjax({{$item->id}});">
              @if(isset($item->status))
              {{$item->status->name}}
              @endif
            </a>
          </div>
        </td>


        <!-- Cancel y Update Tasks -->
        <!-- Mobile view -->
        <td class="d-md-none">
          <div class="d-flex align-items-start">
            <div>
            <a href="#" class="btn btn-sm btn-info my-1" onclick="event.preventDefault(); updateDate({{$item->id}});">
              <i class="fas fa-calendar-check" title="Update task due date"></i>
            </a>
              <a href="#" class="btn btn-sm btn-warning my-1">
                <i  onclick="updateDueDate({{$item->id}});" class="fas fa-calendar-alt" title="Update task due date"></i>
              </a>
            </div>
            <div class="d-flex flex'column">
              <div>
                <a href="#" class="btn btn-sm btn-warning my-1">
                  <i  onclick="setStatusAjax({{$item->id}}, 2);" class="fas fa-spinner" title="Ver task due date"></i>
                </a>
                <a href="#" class="btn btn-sm btn-primary my-1">
                  <i  onclick="setStatusAjax({{$item->id}}, 6);" class="fas fa-check" title="Ver task due date"></i>
                </a>
              </div>
              <div>
                <a href="#" class="btn btn-sm btn-success my-1">
                  <i  onclick="setStatusAjax({{$item->id}}, 56);" class="fas fa-file-invoice" title="Ver task due date"></i>
                </a>
                <a href="#" class="btn btn-sm btn-secondary my-1">
                  <i  onclick="setStatusAjax({{$item->id}}, 4);" class="fas fa-times" title="Cancel task due date"></i>
                </a>
              </div>
            </div>

          </div>
        </td>
        <!-- End Cancel y Update Tasks -->

        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-warning my-2 my-sm-0">
            <i  onclick="setStatusAjax({{$item->id}}, 2);" class="fas fa-spinner" title="Ver task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-primary my-2 my-sm-0">
            <i  onclick="setStatusAjax({{$item->id}}, 6);" class="fas fa-check" title="Ver task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-success my-2 my-sm-0">
            <i  onclick="setStatusAjax({{$item->id}}, 56);" class="fas fa-file-invoice" title="Ver task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-secondary my-2 my-sm-0">
            <i  onclick="setStatusAjax({{$item->id}}, 4);" class="fas fa-times" title="Cancel task due date"></i>
          </a>
        </td>
        <td class="no_print d-none d-md-table-cell" id="date_{{$item->id}}">
          @if(isset($item->due_date))
          <strong>{{ date('M-d', strtotime($item->due_date)) }}</strong>
          @else N/A @endif
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-info my-2 my-sm-0">
            <i  onclick="updateDate({{$item->id}});" class="fas fa-calendar-check" title="Update task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-warning my-2 my-sm-0">
            <i  onclick="updateDueDate({{$item->id}});" class="fas fa-calendar-alt" title="Update task due date"></i>
          </a>
        </td>


        @php
        $last_task_status_id = $item->status_id;
        $last_project_id = $item->project_id;
        if (isset($item->points)) {
          $sumPoints += $item->points;
        }
        $countTask++;
        @endphp

          <script>
            function handleValueGenerated(event, taskId) {
                event.preventDefault();

                let button = document.getElementById("ref_" + taskId);
                if (!button) {
                    console.error("No se encontró el botón con ID ref_" + taskId);
                    return;
                }

                let currentValue = button.style.backgroundColor === "rgb(72, 187, 120)" ? 0 : 1;

                fetch(`/tasks/update-value-generated/${taskId}/${currentValue}`, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.value_generated == 1) {
                        button.style.backgroundColor = "#48bb78"; // Verde (Activado)
                    } else {
                        button.style.backgroundColor = "#f56565"; // Rojo (Desactivado)
                    }
                })
                .catch(error => console.error("Error:", error));
            }
        </script>
</tr>
@endif

<!--- fin del ciclo de tareas -->