<!--myseo-->
<div>
  <form action="/tasks/" method="GET">
    <div class="row">
      <div class="col-md-3 col-sm-12">
        <select name="filter" class="form-control" id="filter" onchange="update()">
          <option value="">...select time</option>
          <option value="1" <?php if($request->filter == "1"): ?> selected="selected" <?php endif; ?>>🎯 tomorrow </option>
          <option value="nextweek" <?php if($request->filter == "nextweek"): ?> selected="selected" <?php endif; ?>>🎯 next week</option>
          <option value="nextmonth" <?php if($request->filter == "nextmonth"): ?> selected="selected" <?php endif; ?>>🎯 next month</option>
          <option value="7" <?php if($request->filter == "7"): ?> selected="selected" <?php endif; ?>>🎯 next 7 days</option>
          <option value="14" <?php if($request->filter == "14"): ?> selected="selected" <?php endif; ?>>🎯 next 14 days</option>
          
          <option value="0" <?php if($request->filter == "0"): ?> selected="selected" <?php endif; ?>>🎁 today</option>
          <option value="thisweek" <?php if($request->filter == "thisweek"): ?> selected="selected" <?php endif; ?>>🎁 this week</option>
          <option value="currentmonth" <?php if($request->filter == "currentmonth"): ?> selected="selected" <?php endif; ?>>🎁 this month</option>
          
          <option value="-1" <?php if($request->filter == "-1"): ?> selected="selected" <?php endif; ?>>🕰️ yesterday</option>
          <option value="lastweek" <?php if($request->filter == "lastweek"): ?> selected="selected" <?php endif; ?>>🕰️ last week</option>
          <option value="lastmonth" <?php if($request->filter == "lastmonth"): ?> selected="selected" <?php endif; ?>>🕰️ last month</option>
          <option value="-7" <?php if($request->filter == "-7"): ?> selected="selected" <?php endif; ?>>🕰️ last 7 days</option>
          <option value="-14" <?php if($request->filter == "-14"): ?> selected="selected" <?php endif; ?>>🕰️ last 14 days</option>
          <option value="-30" <?php if($request->filter == "-30"): ?> selected="selected" <?php endif; ?>>🕰️ last 30 days</option>
          
          
          
          <option value="currentyear" <?php if($request->filter == "currentyear"): ?>selected="selected" <?php endif; ?>>this year</option>
        </select>
      </div>
      <div class="col-md-3 col-sm-12">
        <input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="<?php echo e($request->from_date); ?>">
        <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="<?php echo e($request->to_date); ?>">
      </div>
       <!--  
    *
    *    Combo de proyectos
    *
    -->
      <div class="col-md-3 col-sm-12">
        <select name="project_id" class="form-control" id="project_id" onchange="submit();">
          <option value="">select a project</option>
         <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($project->id); ?>" <?php if($request->project_id == $project->id): ?> selected="selected" <?php endif; ?>>
            <?php echo substr($project->name, 0, 20); ?>
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

      </div>
<!--*
*    Combo de usuarios
*-->  
      <div class="col-md-3 col-sm-12">
        <select name="user_id" class="form-control" id="user_id" onchange="submit();">
          <option value="">select a user</option>
          <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($user->id); ?>" <?php if($request->user_id == $user->id): ?> selected="selected" <?php endif; ?>>
            <?php echo substr($user->name, 0, 20); ?>
          </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 col-sm-12">
        <select multiple="" name="status_id" class="slectpicker form-control" id="status_id">

          <option value="">select status</option>
          <?php $__currentLoopData = $task_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $selected = false;
              for($i=0; $i<count($statuses_id); $i++){
                if($statuses_id[$i] == $item->id){
                  $selected = true;
                }
              }

            ?>
            <option value="<?php echo e($item->id); ?>"  <?php if($selected): ?>  selected="selected" <?php endif; ?> ><?php echo e($item->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>   
          <!--<span class="badge" style="background: #007bff; color:white;" id="task_statuses" data-toggle="modal" data-target="#taskStatusesModal" data-backdrop="false">?</span>
   
           <a href="#" class="btn btn-info sidebar" data-action="toggle" data-side="right"><span>?</span></a>
         <div class="sidebar right" style="display: block; right: 0px;">I am on right!</div>
              <style>
                .sidebar.right {
                 
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 270px;
    background: rgb(68, 68, 68);
    color: white;
    padding: 30px;
    box-shadow: 0 0 5px black;
    font-size: 31px;
    text-align: center;
}       

              </style>
            -->
<a id="slide-button" class="menu-button btn mb-2" href="#"><i  style="background: #007bff; color:white;" class="fa fa-question-circle question"></i></a>
   <div id="local-navbar" class="local-navbar card card-body">                

     <div class="box">
        <table class="table">
          <thead class="thead-dark">
            <tr>
              <th scope="col">Estado</th>
              <th scope="col">Descripción</th>
            </tr>
          </thead>
          <tbody>
          <?php $__currentLoopData = $task_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
           <tr>
              <th scope="row" style="border-left: 5px solid <?php echo e($item->color); ?>!important;"><?php echo e($item->name); ?></th>
              <td scope="row"><?php echo e($item->description); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table></div>

   </div>

<style>




.local-navbar {
    display: flex;
    flex-direction: column;
    position: absolute;
    left: -750px;
    transition: all .24s ease-in;
    width: 450px;
    visibility:hidden;
}

.show {
  left:0;
}

.close-icon {
  line-height: 0;
  position: absolute;
  top: 0;
  right: 0;
}

.close-icon i {
  color: #B2B7FF;
  font-size: 2rem;
}

.close-icon i:hover {
  color: #E6E8FF;
}

.local-navbar-icon {
  background-color: #2F365F;
  border-radius: .125rem;
  color: #E6E8FF;
  margin-bottom: .5rem;
  padding: .75rem 1.25rem;
  text-decoration: none;
}

.local-navbar-icon:hover {
 color: #fff;
  text-decoration: none;
}
</style>

<script>
  // jQuery Version
jQuery(document).ready( function($) {
    $('#slide-button, .close-icon').click( function() {
        $('#local-navbar').toggleClass('show');
     });
});

// Vanilla JavaScript Version
// let menu_button = document.getElementById('slide-button');
// menu_button.addEventListener( 'click', show_menu);
// function show_menu() {
//     let menu_panel = document.getElementById('local-navbar');
//     if(menu_panel.classList.contains('show')) {
//         menu_panel.classList.remove('show');
//     } else {
//         menu_panel.classList.add('show');     
//     }
// }
</script>
            
      </div>
      <div class="col-md-3 col-sm-12">
        <div class="row">
          <div class="col-12">
            <select name="type_id" class="form-control" onchange="submit()">
              <option value="">Select type</option>
              <?php $__currentLoopData = $task_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($key); ?>" <?php if($request->type_id==$key): ?> selected="selected" <?php endif; ?>><?php echo e($value); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

        </div>
           
      </div>
      <div class="col-md-3 col-sm-12">
        <div class="row">
          <div class="col-12 mt-1 grid place-items-center">
            <label>Value Generated:</label>
            <label for="value_generated">Yes</label>
            <input name="no_value_generated" id="no_value_generated" type="radio" class="check" value="1" <?php if(isset($request->no_value_generated)&&($request->no_value_generated==1)): ?> checked <?php endif; ?>  onclick="submit()">
            <label for="value_generated">No</label>
            <input name="no_value_generated" id="no_value_generated" type="radio" class="check" value="0" <?php if(isset($request->no_value_generated)&&($request->no_value_generated==0)): ?> checked <?php endif; ?>  onclick="submit()">
          </div>
          <div class="col-12">
            <select name="priority" id="priority" class="form-control" onchange="submit();">
                <option value="">Select a Priority</option>
                <option value="1" <?php if($request->priority == "1"): ?> selected="selected" <?php endif; ?>>1</option>
                <option value="2" <?php if($request->priority == "2"): ?> selected="selected" <?php endif; ?> >2</option>
                <option value="3" <?php if($request->priority == "3"): ?> selected="selected" <?php endif; ?> >3</option>
                <option value="4" <?php if($request->priority == "4"): ?> selected="selected" <?php endif; ?> >4</option>
                <option value="5" <?php if($request->priority == "5"): ?> selected="selected" <?php endif; ?> >5</option>
                <option value="6" <?php if($request->priority == "6"): ?> selected="selected" <?php endif; ?> >6</option>
                <option value="7" <?php if($request->priority == "7"): ?> selected="selected" <?php endif; ?> >7</option>
                <option value="8" <?php if($request->priority == "8"): ?> selected="selected" <?php endif; ?> >8</option>
                <option value="9" <?php if($request->priority == "9"): ?> selected="selected" <?php endif; ?> >9</option>
                <option value="10" <?php if($request->priority == "10"): ?> selected="selected" <?php endif; ?> >10</option>
            </select>
          </div>
        </div>
      
      </div>
      <div class="col-md-3 col-sm-12">
      <input type="text" id="querystr" name="querystr" <?php if(isset($request->querystr)): ?>  value="<?php echo e($request->querystr); ?>" <?php endif; ?>>
        <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filter" >
      </div>
    </div>
    

    </form>
</div>

<script>
  /*
  $(".sidebar").on("click", function () {
  $(".sidebar.right").sidebar().trigger("sidebar:open");
});
// open a sidebar
$(".sidebar.right").sidebar().trigger("sidebar:open");

// close a sidebar
$(".sidebar.right").sidebar().trigger("sidebar:close");

// toggle a sidebar
$(".sidebar.right").sidebar().trigger("sidebar:toggle");
*/
</script>


