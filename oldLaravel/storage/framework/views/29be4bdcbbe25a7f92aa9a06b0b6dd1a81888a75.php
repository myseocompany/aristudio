<?php $__env->startSection('content'); ?>
<div class="body-timer">
  <nav class="navbar navbar-expand-md navbar-white fixed-top bg-white" id="center-nav">
    <div class="container">
            
      <div id="row-form">
        <div id="name-field">
          <input type="text" name="timer-name" id="timer-name" class="timer-input form-control" placeholder="What are you working on?" <?php if(isset($actual_task)): ?> value="<?php echo e($actual_task->name); ?>" <?php endif; ?>>
  
          <input type="hidden" name="timer-status_id" id="timer-status_id" <?php if(isset($actual_task)): ?> value="<?php echo e($actual_task->status_id); ?>" <?php endif; ?>>
            
        </div>
        <div id="timer-project">
          <!--  
    *
    *    Combo de proyectos
    *
    -->   
          <?php if(isset($projects)): ?>
          <select name="timer-project_id" class="custom-select" id="timer-project_id" >
            <option value="">select a project</option>
           <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($item->id); ?>" <?php if(isset($actual_task)&&($actual_task->project_id==$item->id)): ?> selected <?php endif; ?> >
              <?php echo e($item->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php endif; ?>
  
        </div>
        <div id="button-field">
          <svg id="play-button" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" <?php if(isset($actual_task)): ?> style="display:none" <?php endif; ?>>
            <g  fill="none" fill-rule="evenodd">
            <rect onclick="toggleButton('play');" width="36" height="36" fill="#4bc800" rx="18"></rect>
            <path onclick="toggleButton('play');" fill="#ffffff" d="M13 11.994c0-1.101.773-1.553 1.745-.997l10.51 6.005c.964.55.972 1.439 0 1.994l-10.51 6.007c-.964.55-1.745.102-1.745-.997V11.994z"></path></g>
          </svg>
  
  
          <svg id="stop-button" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" 
          <?php if(!isset($actual_task)): ?> 
          style="display:none" <?php endif; ?>>
            <g fill="none" fill-rule="evenodd">
              <rect onclick="toggleButton('stop');" width="36" height="36" fill="#e20505" rx="18"></rect>
              <rect onclick="toggleButton('stop');" width="14" height="14" x="11" y="11" fill="#ffffff" rx="1.5"></rect>
            </g>
          </svg>
  
          
        </div>
        <div id="timer-status">
          
  
  
        </div>
        <div id="chronotime"></div>
      </div>
            
  
            
    </div>
  </nav>
  <script type="text/javascript">
  
    
    var id_task = "";
    <?php if(isset($actual_task)): ?>
      id_task = <?php echo e($actual_task->id); ?>

    <?php endif; ?>
  
    function starTask(){
      end_point = "/timer";
      
      name = $("#timer-name").val();
      if(name!=""){
        $('#play-button').toggle();
        $('#stop-button').toggle();
  
        chronoReset();
        chronoStart();
        
        data = {
            "name": name,
            "user_id": <?php echo e(Auth::id()); ?>,
            "project_id": $("#timer-project_id").val(),
            "task_id": id_task
        };
  
  
  
        console.log(data);
        var posting = $.post(end_point, data);
        //$('#timer-status').html('...enviando');
        posting.done(function( data ) {
            console.log("enviado");
            console.log(data);
            id_task = data;
            //$('#timer-status').html(data);
          });
        posting.fail(function(XMLHttpRequest, textStatus, errorThrown){
          console.log(XMLHttpRequest.responseText);
          console.log(textStatus);
          console.log(errorThrown);
          
          //$('#timer-status').html(XMLHttpRequest.responseText);
        });
      }
    }
  
  
  
    function stopTask(id){
      console.log("hola "+ id_task);
  
  
      chronoStop();
      $('#play-button').toggle();
      $('#stop-button').toggle();
  
      end_point = "/timer/stop";
      var posting = $.post(end_point,{
          "id" : id_task,
          "name": $("#timer-name").val(),
          "status_id": $("#timer-status_id").val(),
          "project_id": $("#timer-project_id").val()
      });
      //$('#timer-status').html('parando');
      posting.done(function( data ) {
          console.log("parando");
          console.log(data);
          showTask(JSON.parse(data));
          id_task = "";
          //$('#timer-status').html('parado');
        });
       posting.fail(function(XMLHttpRequest, textStatus, errorThrown){
        console.log(XMLHttpRequest.responseText);
        console.log(textStatus);
        console.log(errorThrown);
        
        //$('#timer-status').html(XMLHttpRequest.responseText);
      });
    }
  
    function showTask(task){
      list = $("#timer-list");
      str = "<div class='row'>";
  
   
      
      str += "<div class='col-sm-12 col-md-9'>";
      str += task.name;
      
      str += "<span class='badge'>";
      str += task.project_name;
      str += "</span>";

      str += "<br><small>"
      str += task.due_date.date
      str += "</small>"
      str += "</div>";
      
      str += "<div class='col-sm-12 col-md-1'>[";
      str += getDurationAsTimeString(task.points)
      str += "]</div>";
     
  
      str += "<div class='col-sm-12 col-md-1'>";
      str += "";
      str += "</div>";
  
      str += "<div class='col-sm-12 col-md-1'>";
      str += "<a href='/tasks/"+task.id+"/edit'>edit</a>"
      str += "</div>";
  
      str += "</div>";
      
      list.prepend(str);
  
    }
  
  
  var startTime = 0
  var start = 0
  var end = 0
  var diff = 0
  var timerID = 0
  
  function chrono(){
    end = new Date()
    diff = end - start
    diff = new Date(diff);
    
    //console.log("ini " + start +" end " + end + " diff " + diff);
    
  
  
    var msec = diff.getMilliseconds();
    var sec = diff.getSeconds();
    var min = diff.getMinutes();
    var hr = diff.getHours();
    if (hr==19) hr=0;
  
    if (min < 10){
      min = "0" + min
    }
    if (sec < 10){
      sec = "0" + sec
    }
    if(msec < 10){
      msec = "00" +msec
    }
    else if(msec < 100){
      msec = "0" +msec
    }
    document.getElementById("chronotime").innerHTML = hr + ":" + min + ":" + sec + ":" + msec
    timerID = setTimeout("chrono()", 10)
  }
  function chronoStart(){
    
    start = new Date()
    chrono()
  }
  function chronoContinue(){
    
    start = new Date()-diff
    start = new Date(start)
    chrono()
  }
  function chronoReset(){
    document.getElementById("chronotime").innerHTML = "0:00:00:000"
    start = new Date()
  }
  function chronoStopReset(){
    document.getElementById("chronotime").innerHTML = "0:00:00:000"
    
  }
  function chronoStop(){
    
    clearTimeout(timerID)
  }
   
  function restartButton(tid, name, pid, sid){
    id_task = tid;
    //$('#task_'+tid).hide();
    $('#timer-name').val(name);
    $('#timer-project_id').val(pid);
    $('#timer-status_id').val(sid);
    starTask();
  }
  
  //-->
  
  </script>
    
  <h1>Tasks</h1>
  <div id="timer-list">
    <?php $__currentLoopData = $model; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class='row' id="task_<?php echo e($item->id); ?>">
          
          <div class='col-sm-12 col-md-9'>
            <?php echo e($item->name); ?><?php if(isset($item->project)): ?> <span class="badge" style="background-color: #EEEEEE; color: <?php echo e($item->project->color); ?>"><?php echo e($item->project->name); ?></span> <?php endif; ?>
            <br><small><?php echo e($item->due_date); ?></small>
          </div>
          <div class='col-sm-12 col-md-1'>[<?php echo e($item->getPointsAsTimeString()); ?>]</div>
        
          
          <div class='col-sm-12 col-md-1'>
            <div id="button-field">
              <svg id="play-button_<?php echo e($item->id); ?>" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                <g fill="none" fill-rule="evenodd">
                  <rect onclick="restartButton(<?php echo e($item->id); ?>, '<?php echo e($item->name); ?>', <?php echo e($item->project_id); ?>, <?php echo e($item->status_id); ?>);" width="36" height="36" fill="#4bc800" rx="18"></rect>
                  <path onclick="restartButton(<?php echo e($item->id); ?>, '<?php echo e($item->name); ?>', <?php echo e($item->project_id); ?>, <?php echo e($item->status_id); ?>);" fill="#ffffff" d="M13 11.994c0-1.101.773-1.553 1.745-.997l10.51 6.005c.964.55.972 1.439 0 1.994l-10.51 6.007c-.964.55-1.745.102-1.745-.997V11.994z"></path>
                </g>
              </svg>
            </div>
          </div>
          <div class='col-sm-12 col-md-1'>
            
            <a href='/tasks/<?php echo e($item->id); ?>/edit'>edit</a>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <script>
    function getDurationAsTimeString(hours) {
      // Convert hours to seconds
      let seconds = hours * 3600;
  
      // Calculate hours, minutes, and seconds
      let hoursFormatted = Math.floor(seconds / 3600);
      let minutesFormatted = Math.floor((seconds / 60) % 60);
      let secondsFormatted = Math.round(seconds % 60);
  
      // Format and return time string
      return [
          hoursFormatted.toString().padStart(2, '0'),
          minutesFormatted.toString().padStart(2, '0'),
          secondsFormatted.toString().padStart(2, '0')
      ].join(':');
  }
  
  
  </script>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>