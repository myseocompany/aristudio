<?php $__env->startSection('content'); ?>
<h1><?php echo e($model->name); ?></h1>
<form method="POST" action="/projects/<?php echo e($model->id); ?>/edit">
  <?php echo e(csrf_field()); ?>

  
  <div class="row">
    <div class="col-md-8">
      <div class="form-group">
        <label for="description"><strong>Description</strong></label>    
        <div class="help-block"><?php echo nl2br($model->description); ?></div>
      </div>
      
    </div>
 
    <div class="col-md-4">
    <div class="form-group">
        <label for="budget"><strong>Budget</strong></label>
        <div class="help-block"><?php echo e($model->budget); ?></div>
         
      </div>
      <div class="form-group">
    <label for="status"><strong>Status</strong></label>
    <div class="help-block"><?php echo e($model->getProjectStatusOptionsById()); ?></div>    
  </div>
   <div class="form-group">
    <label for="status"><strong>Sales</strong></label>
    <div class="help-block"><?php echo e($model->sales); ?></div>    
  </div>

    <div class="form-group">
          <label for="start_date"><strong>Start Date</strong></label>
          <div class="help-block"><?php echo e($model->start_date); ?></div>
            
      <label for="finish_date"><strong>Finish Date</strong></label>
        <div class="help-block"><?php echo e($model->finish_date); ?></div>    
      </div>
      
      <div class="form-group">
        <label for="status"><strong>Type</strong></label>
        <div class="help-block"><?php echo e($model->getProjectTypeOptionsById()); ?></div>    
      </div> 
      <div class="form-group">
        <label for="status"><strong>Lead Target</strong></label>
        <div class="help-block"><?php echo e($model->lead_target); ?></div>    
      </div> 
      <div class="form-group">
        <label for="status"><strong>Point Target</strong></label>
        <div class="help-block"><?php echo e($model->monthly_points_goal); ?></div>    
      </div> 
  
    </div>
  </div>
  <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Edit</button>
</form>




<?php echo $__env->make("projects.widget_files", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make("projects.widget_logins", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<!--  m_id,c,r,u,d,l) m=6-> logins -->
<?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,1,0,0,0,0) == 1): ?>
<form action="/project_logins" method="POST">
  <?php echo e(csrf_field()); ?>

  <h2>Create Login</h2>
  <div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="name"><strong>Name</strong></label>
      <input class="form-control" type="text" name="name">
    </div>

    <div class="form-group">
      <label for="url"><strong>URL</strong></label>
      <input class="form-control" type="text" name="url">
    </div>
  </div>

  <div class="col-md-6">
    <div class="form-group">
      <label for="user"><strong>User</strong></label>
      <input class="form-control" type="text" name="user">
    </div>

    <div class="form-group">
      <label for="password"><strong>Password</strong></label>
      <input class="form-control" type="text" name="password">
    </div>
    <input type="hidden" name="project_id" id="project_id" value="<?php echo e($model->id); ?>">
  </div>

</div>
<input type="submit" name="" value="Submit" class="btn btn-sm btn-primary my-2 my-sm-0">

</form>
<?php endif; ?>







<div>
  <div>
    <h2>Users</h2>
  
  
  <form action="/projects/<?php echo e($model->id); ?>/addUser" method="POST">
     <?php echo e(csrf_field()); ?>

    <select name="user_id" id="user_id">
      <option value="">Select user...</option>
      <?php $__currentLoopData = $pending_users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        
      <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  <button type="submit" class="btn btn-sm btn-primary my-2 my-sm-0">Submit</button>
  </form>
  </div>

  <div id="user-table" class="table-wrapper-scroll-y my-custom-scrollbar">
     <div class="table-responsive">
            <table class="table table-striped">
              <thead class="">
              <tr>
                <th>Name</th>
                <th>Role</th>
                <th></th>
              </tr>
              </thead>
              <?php $__currentLoopData = $model->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              
              <tr>
                <td><?php echo e($item->name); ?></td>
                <td><?php if(isset($item->role_id)): ?><?php echo e($item->role->name); ?><?php endif; ?></td>
                <td><a href="/projects/<?php echo e($model->id); ?>/deleteUser/<?php echo e($item->id); ?>">Delete</a></td>
                
              </tr>
              
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
   
          <script>
    function copyPassword(id_elemento) {
       
  var aux = document.createElement("input");
  aux.setAttribute("value", document.getElementById(id_elemento).innerHTML);
  document.body.appendChild(aux);
  aux.select();
  document.execCommand("copy");
  document.body.removeChild(aux);
}
</script>
    
  </div>
</div>
   
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>