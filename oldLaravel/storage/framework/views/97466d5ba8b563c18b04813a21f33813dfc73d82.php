<!-- Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="/tasks/update" id="modal_form" name="modal_form" method="POST" enctype="multipart/form-data">
        <?php echo e(csrf_field()); ?>

        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <?php if(isset($item->file_url)): ?>
              <img src="/laravel/storage/app/public/files/<?php echo e($item->file_url); ?>" id="preview_image" style="width: 100%;">
              <?php endif; ?>
            </div>
            <div class="col-md">
              <div class="form-group">
                <input type="hidden" name="id" id="id">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required" value="<?php echo e($item->name); ?>">
              </div>
              <div class="form-group">
                <label for="user_id">Project</label>
                <select name="project_id" id="project_id" class="form-control" required="required">
                  <option value="">Select a Project</option>
                  <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($project->id); ?>" <?php if($item->project_id == $project->id): ?> selected="" <?php endif; ?>><?php echo e($project->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="form-group">
                <label for="priority">Priority</label>
                <input type="text" class="form-control" id="priority" name="priority" placeholder="Priority" value="<?php echo e($item->priority); ?>">
              </div>

              <div class="form-group">
                <label for="due_date">Due Date</label>

                <input type="date" class="form-control" id="due_date" name="due_date" placeholder="YYYY/MMM/DD" required="required" value="<?php echo date("Y-m-d", strtotime($item->due_date)); ?>">
              </div>
              <div class="form-group">
                <label for="delivery_date">Commitment Date</label>

                <input type="date" class="form-control" id="delivery_date" name="delivery_date" placeholder="YYYY/MMM/DD" required="required" 
                valuea="<?php if(!isset($request->delivery_date)): ?><?php echo e(date('Y-m-d')); ?><?php else: ?><?php echo e($request->delivery_date); ?><?php endif; ?>"
                value="<?php echo date('Y-m-d'); ?>">
              </div>
              <div class="form-group">
                <label for="value_generated">Value generated:</label>
                <input name="no_value_generated" id="no_value_generated_yes" type="radio" class="check" value="1" 
                  <?php if(isset($request->no_value_generated) && $request->no_value_generated == 1): ?> checked <?php endif; ?>>
                <label for="no_value_generated_yes">Yes</label>

                <input name="no_value_generated" id="no_value_generated_no" type="radio" class="check" value="0" 
                  <?php if(isset($request->no_value_generated) && $request->no_value_generated == 0): ?> checked <?php endif; ?>>
                <label for="no_value_generated_no">No</label>
              </div>
            </div>

            <div class="col-md">
              <div class="form-group">
                <label for="user_id">User</label>
                <select name="user_id" id="user_id" class="form-control">
                  <option value="">Select a User</option>
                  <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($user->id); ?>" <?php if($item->user_id == $user->id): ?> selected="" <?php endif; ?>><?php echo e($user->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>

              <div class="form-group">
                <label for="status_id">Status</label>
                <select name="status_id" id="status_id" class="form-control">
                  <?php $__currentLoopData = $task_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($status->id); ?>" <?php if($item->status_id == $status->id): ?> selected="" <?php endif; ?>><?php echo e($status->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  ?>
                </select>
              </div>

              <div class="form-group">
                <label for="points">Points</label>
                <input type="text" class="form-control" id="points" name="points" placeholder="Points" value="<?php echo e($item->points); ?>" pattern="^(\d(\.\d{1,2})?|0?\.\d{1,2}|2(\.0{1,2})?)$" oninput="validateRange(this)">

              </div>
              <div class="form-group">
                <label for="estimated_points">estimated points</label>
                <input type="text" class="form-control" id="estimated_points" name="estimated_points" placeholder="estimated_points" value="<?php echo e($item->estimated_points); ?>">

              </div>



              <div class="form-group">
                <label for="file">File</label>
                <input type="file" class="form-control" id="file" name="file" placeholder="Name">
              </div>

              <div class="form-group">
                <label for="url_finished">Url Finished Task </label>
                <input class="form-control" name="url_finished" id="url_finished" placeholder="Url" value="<?php echo e($item->url_finished); ?>">
              </div>
            </div>
            <div class="form-group col-md-12">
              <label for="description">Description</label>
              <textarea class="form-control" name="description" id="description" cols="30" rows="2" value="<?php echo e($item->description); ?>"><?php echo e($item->description); ?></textarea>
            </div>
            <input type="hidden" name="from" id="from" class="form-control" value="project">
            <div class="form-group col-md-12">
              <button id="btnCancel" class="btn btn-sm btn-warning my-2 my-sm-0" data-dismiss="modal">Cancel</button>
              <button id="btn_edit" type="submit" onclick2="updateTaskFromModal();" class="btn btn-sm btn-primary my-2 my-sm-0">Update</button>
              <!--<a onclick="updateTaskFromModalAJAX(<?php echo e($item->id); ?>);" href="#">Save Ajax</a> -->

            </div>

      </form>

      <div class="card" style="width: 100%;">
        <div class="card-header">
          <h4>Comments</h4>
        </div>
      </div>
      <div class="card-body" id="comment" style="width: 100% !important;">
        <label for="description_message">Add Comment</label>
        <div class="input-group">
          <input class="form-control" style="width: 45%;" type="text" id="description_modal_edit" class="form-control">
          <input type="hidden" name="task_id" value="" id="task_id">
          <span class="input-group-btn">
            <button id="show_password" class="btn btn-primary" type="button" onclick="sendMessage({$item->id},{$item->getUser(Auth::user()->id)} );">
              <span class="far fa-paper-plane"></span>

            </button>
          </span>
        </div>
      </div>
      <div class="card-footer" style="width: 100% !important;">
        <div style="width: 100% !important;" id="count_messages">

        </div>
      </div>

    </div>

  </div>

</div>

</div>
</div>
</div>


<script>
  $(document).ready(function() {
    // Listener para el evento click en los botones de edición
    $('.editTaskBtn').on('click', function() {
      updateModal($(this)); // Pasa $(this) para mantener el contexto correcto
    });

    function updateModal(element) { // Cambia 'this' por 'element' o cualquier otro nombre
      // Extracción de los datos de la tarea desde el atributo data-task
      var taskData = element.data('task');

      // Agregar console.log para verificar los datos de taskData
      console.log('taskData:', taskData);

      // Actualización de los campos del modal con los datos extraídos
      $('#editTaskModal #id').val(taskData.id);
      $('#editTaskModal #name').val(taskData.name);
      $('#editTaskModal #project_id').val(taskData.project_id);
      $('#editTaskModal #priority').val(taskData.priority);
      $('#editTaskModal #due_date').val(taskData.due_date);
      $('#editTaskModal #value_generated_1').prop('checked', taskData.value_generated == 1);
      $('#editTaskModal #value_generated_0').prop('checked', taskData.value_generated == 0);
      $('#editTaskModal #user_id').val(taskData.user_id);
      $('#editTaskModal #status_id').val(taskData.status_id).trigger('change');
      $('#editTaskModal #points').val(taskData.points);
      $('#editTaskModal #estimated_points').val(taskData.estimated_points); // Asegúrate de que este campo se actualice
      $('#editTaskModal #description').val(taskData.description);

      // Si el modal incluye un campo para la URL del archivo o una imagen
      if (taskData.file_url) {
        $('#editTaskModal #preview_image').attr('src', taskData.file_url);
      }

      // Si el modal incluye un campo para la URL de la tarea terminada
      if (taskData.url_finished) {
        $('#editTaskModal #url_finished').val(taskData.url_finished);
      } else {
        $('#editTaskModal #url_finished').val("");
      }

      // Actualiza select2 o cualquier otro plugin que requiera una actualización después de cambiar el valor
      $('#editTaskModal #user_id').trigger('change');
      $('#editTaskModal #status_id').trigger('change');
    }
  });



  function updateTaskFromModal() {
    id = $('#editTaskModal #task_id').val;
    console.log(id);
    $("#btn_edit_"(id)).attr('disabled', 'disabled');
    objEvent = getDataGUIEdit(id);
    sendRequestEdit('', objEvent, "POST"(id));
  }




  function sendRequestEdit(action, objEvent, method, task_id, modal) {
    console.log(objEvent);
    var tid = task_id;
    var url = '/tasks/' + tid + '/ajax/update';
    $.ajax({
      type: method,
      url: url,
      data: objEvent,
      success: function(msg) {
        if (!modal) {
          $("#editModal_" + tid).modal('toggle');
          location.reload();
        }
      },
      error: function() {
        alert("Error");
      }
    });
  }
</script>