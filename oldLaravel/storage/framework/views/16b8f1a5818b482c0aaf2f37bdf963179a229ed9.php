<!-- Inicio del ciclo de tareas -->
<tr class="task_row edit-container" data-id="<?php echo e($item->id); ?>" id="task_id_<?php echo e($item->id); ?>" draggable='true' ondragstart='dragStart()' ondragover='dragOver()'>
  <input type="hidden" name="token" id="token_id_<?php echo e($item->id); ?>" value="<?php echo e(csrf_token()); ?>">
  <input type="hidden" name="id" id="task_id__<?php echo e($item->id); ?>" value="<?php echo e($item->id); ?>">
 
<!-- Inicio de barra de estado --> 
  <!-- if(isset($item->type_id))
  <td colspan="13">
   <h4> <a href="/tasks/<?php echo e($item->id); ?>"><?php echo e($item->type->name); ?></a> </h4>
  </td>
-->
  <td class="text-center align-middle">
      <div class="d-md-none mr-2">
          <?php if(isset($item->status)): ?>
          <span class="badge badge-info justify-content-center align-items-center p-1" 
                id="mobile_status_<?php echo e($item->id); ?>" 
                style="background-color: <?php if(isset($item->status->background_color)): ?> <?php echo e($item->status->background_color); ?> <?php endif; ?>; 
                      font-size: 0.9rem;
                      height: 80px">
                      
          </span>
          
      </div>
  </td>

<!-- Foto de perfil usuario -->
<td class="no_print text-center mr-2">
  <div class="user_image_container no_print mr-3">
    <div class="user_image_mini_container no_print">
      <a href="#" onclick="showTeam(<?php echo e($item->id); ?>)" alt="Asignar responsable">
        <?php if(!empty($item->user->image_url)): ?>

        <img src="/laravel/storage/app/public/files/users/<?php echo e($item->user->image_url); ?>" alt="<?php echo e($item->user->name); ?>" class="user_image user_image_mini" id="task_user_image_<?php echo e($item->id); ?>">
        <?php else: ?>
        <img src="/laravel/storage/app/public/files/users/user.png" alt="Sin asignar" class="user_image user_image_mini" id="task_user_image_<?php echo e($item->id); ?>">
        <?php endif; ?>
      </a>
    </div>

    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="user_image_mini_container toggle_<?php echo e($item->id); ?>" id="user_<?php echo e($item->id); ?>_<?php echo e($user->id); ?>" style="display: none">

      <a hrefd="#user_<?php echo e($item->id); ?>_<?php echo e($user->id); ?>" onclick="updateUserAjax(<?php echo e($item->id); ?>,<?php echo e($user->id); ?>);" alt="<?php echo e($user->name); ?>">

        <?php if(isset($user->image_url)): ?>
        <img src="/laravel/storage/app/public/files/users/<?php echo e($user->image_url); ?>" alt="<?php echo e($user->name); ?>" title="<?php echo e($user->name); ?>" class="user_image_mini" border="0">
        <?php else: ?>
        <img src="/laravel/storage/app/public/files/users/user.png" alt="<?php echo e($user->name); ?>" title="<?php echo e($user->name); ?>" class="user_image_mini" border="0">

        <?php endif; ?>
      </a>

    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <!-- Mostrar solo en versión móvil -->
    <div class="d-md-none">
      <span><?php echo e($item->getPointsAsTimeString()); ?></span>
      <div>
        <?php if(isset($item->due_date)): ?>
        <strong id="mobile_date_<?php echo e($item->id); ?>"><?php echo e(date('M-d', strtotime($item->due_date))); ?></strong>
        <?php else: ?> N/A <?php endif; ?>
    </div>
    </div>
  </div>
</td>
<!-- Fin foto de perfil usuario -->



  

  <td id="<?php echo e($item->id); ?>">
    <?php if((Auth::user()!=null) && (Auth::user()->role_id!=3) ): ?>
    <div>
      <a href="/tasks/<?php echo e($item->id); ?>" class="no_print">
        <?php echo e(str_limit($item->name, 50)); ?>

      </a>
    </div>
    <a href="/projects/<?php echo e($item->project_id); ?>">
      <span class="badge" style="background-color: <?php echo e($item->project->color); ?>; color: white">
        <?php echo e($item->project->name); ?>

      </span>
    </a>

    <?php if((Auth::user()!=null) && (Auth::user()->role_id!=3) ): ?>
    <i class="fas fa-edit hide editTaskBtn" data-toggle="modal" data-target="#editTaskModal"
      data-task="<?php echo e(htmlspecialchars(json_encode($item->forModal()), ENT_QUOTES, 'UTF-8')); ?>"
      onclick="getMessages(<?php echo e($item->id); ?>,<?php echo e($item->getUser(Auth::user()->id)); ?>)"></i>
    <input type="hidden" id="task_id_m">
    <?php endif; ?>
    <i class="fas fa-filter hide select-container" id="demo_<?php echo e($item->id); ?>" onclick="hideSubTypes('<?php echo e($item->id); ?>')"></i>
    <!-- i onclick="openTaskModal(<?php echo e($item->project_id); ?>,<?php echo e(Auth::user()->id); ?>);" class="fas fa-plus-circle" style="color:<?php echo e($item->project->color); ?>"></i -->
    <a 
      id="ref_<?php echo e($item->id); ?>" 
      href="#ref_<?php echo e($item->id); ?>" 
      onclick="handleValueGenerated(event, <?php echo e($item->id); ?>);" 
      class="px-2 py-1 text-white rounded shadow-md transition-all duration-300 btn-sm"
      style="background-color: <?php echo e($item->value_generated == 1 ? '#48bb78' : '#f56565'); ?>;">
      <i class="fas fa-check-circle"></i>
    </a>



    <?php if(isset($item->url_finished) && ($item->url_finished!=null)): ?>
    <a href="<?php echo e($item->url_finished); ?>" target="_blanck"> <i class="fas fa-link"></i>
    </a>
    <?php endif; ?>

    <div class="sub-hide_<?php echo e($item->id); ?>" id="sub">
      <?php if(isset($task_types)): ?>
      <select name="type_id" id="type_id_<?php echo e($item->id); ?>" class="custom-select" onchange="updateTypeAjax(<?php echo e($item->id); ?>, this.value );">
        <option>Select a type...</option>
        <?php $__currentLoopData = $task_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($key); ?>" <?php if($item->type_id==$key): ?> selected="selected" <?php endif; ?> ><?php echo e(substr($value,0,40)); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php endif; ?>
    </div>
    <div class="just_print">::<?php echo e($item->nameSubstr(100)); ?> </div>

    <?php else: ?>
    <div>
      <h5 class="task_title"><?php echo e($item->name); ?></h5>
      <div class="task_description"><?php echo e($item->description); ?></div>
    </div>
    <?php endif; ?>
    <input type="hidden" id="observer_id" name="observer_id" value="<?php echo e(Auth::id()); ?>">
    <input type="hidden" id="status_id_<?php echo e($item->id); ?>" name="status_id" value="<?php echo e($item->status_id); ?>">

    <div id="mini_container no_print">
      <?php if((Auth::user()!=null) && (Auth::user()->role_id!=3) ): ?>
      <?php endif; ?>

  </td>

  <td class="d-none d-md-table-cell">
    <div class="flex flex-col">
        <span><?php echo e($item->due_date); ?></span>
    </div>
    <div class="flex flex-col">
        <span><?php echo e($item->delivery_date); ?></span>
    </div>
</td>

  <?php if((Auth::user()!=null) && (Auth::user()->role_id!=3) ): ?>
  <?php if($item->priority > 0 && $item->priority <= 3): ?>
    <td class="no_print text-center d-none d-md-table-cell"><i class="fas fa-exclamation-triangle" style="color: green;" title="<?php echo e($item->priority); ?>"></i></td>
    <?php elseif($item->priority > 3 && $item->priority <= 6): ?>
      <td class="no_print text-center d-none d-md-table-cell"><i class="fas fa-exclamation-triangle" style="color: orange;" title="<?php echo e($item->priority); ?>"></i></td>
      <?php elseif($item->priority > 6 && $item->priority <= 10): ?>
        <td class="no_print text-center d-none d-md-table-cell"><i class="fas fa-exclamation-triangle" style="color: red;" title="<?php echo e($item->priority); ?>"></i></td>
        <?php else: ?>
        <td class="no_print text-center d-none d-md-table-cell"></td>
        <?php endif; ?>
        <?php endif; ?>

        <td class="no_print text-center d-none d-md-table-cell"><?php echo e($item->getPointsAsTimeString()); ?></td>

        

        <!-- Estado de la tarea (solo en escritorio) -->
        <td id="task_cel_<?php echo e($item->id); ?>" class="d-none d-md-table-cell" style="background-color: <?php echo e($item->status->background_color ?? 'transparent'); ?>;">
          <div class="task_container">
            <a id="#ref_<?php echo e($item->id); ?>" hrefu="#ref_<?php echo e($item->id); ?>" onclick="updateNextStatusAjax(<?php echo e($item->id); ?>);">
              <?php if(isset($item->status)): ?>
              <?php echo e($item->status->name); ?>

              <?php endif; ?>
            </a>
          </div>
        </td>


        <!-- Cancel y Update Tasks -->
        <!-- Mobile view -->
        <td class="d-md-none">
          <div class="d-flex align-items-start">
            <div>
            <a href="#" class="btn btn-sm btn-info my-1" onclick="event.preventDefault(); updateDate(<?php echo e($item->id); ?>);">
              <i class="fas fa-calendar-check" title="Update task due date"></i>
            </a>
              <a href="#" class="btn btn-sm btn-warning my-1">
                <i  onclick="updateDueDate(<?php echo e($item->id); ?>);" class="fas fa-calendar-alt" title="Update task due date"></i>
              </a>
            </div>
            <div class="d-flex flex'column">
              <div>
                <a href="#" class="btn btn-sm btn-warning my-1">
                  <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 2);" class="fas fa-spinner" title="Ver task due date"></i>
                </a>
                <a href="#" class="btn btn-sm btn-primary my-1">
                  <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 6);" class="fas fa-check" title="Ver task due date"></i>
                </a>
              </div>
              <div>
                <a href="#" class="btn btn-sm btn-success my-1">
                  <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 56);" class="fas fa-file-invoice" title="Ver task due date"></i>
                </a>
                <a href="#" class="btn btn-sm btn-secondary my-1">
                  <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 4);" class="fas fa-times" title="Cancel task due date"></i>
                </a>
              </div>
            </div>

          </div>
        </td>
        <!-- End Cancel y Update Tasks -->

        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-warning my-2 my-sm-0">
            <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 2);" class="fas fa-spinner" title="Ver task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-primary my-2 my-sm-0">
            <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 6);" class="fas fa-check" title="Ver task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-success my-2 my-sm-0">
            <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 56);" class="fas fa-file-invoice" title="Ver task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-secondary my-2 my-sm-0">
            <i  onclick="setStatusAjax(<?php echo e($item->id); ?>, 4);" class="fas fa-times" title="Cancel task due date"></i>
          </a>
        </td>
        <td class="no_print d-none d-md-table-cell" id="date_<?php echo e($item->id); ?>">
          <?php if(isset($item->due_date)): ?>
          <strong><?php echo e(date('M-d', strtotime($item->due_date))); ?></strong>
          <?php else: ?> N/A <?php endif; ?>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-info my-2 my-sm-0">
            <i  onclick="updateDate(<?php echo e($item->id); ?>);" class="fas fa-calendar-check" title="Update task due date"></i>
          </a>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="#" class="btn btn-sm btn-warning my-2 my-sm-0">
            <i  onclick="updateDueDate(<?php echo e($item->id); ?>);" class="fas fa-calendar-alt" title="Update task due date"></i>
          </a>
        </td>


        <?php 
        $last_task_status_id = $item->status_id;
        $last_project_id = $item->project_id;
        if (isset($item->points)) {
          $sumPoints += $item->points;
        }
        $countTask++;
         ?>

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
<?php endif; ?>

<!--- fin del ciclo de tareas -->