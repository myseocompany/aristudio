<?php $__env->startSection('content'); ?>
<h1>Create User</h1>
<form method="POST" action="/users" enctype="multipart/form-data">
<?php echo e(csrf_field()); ?>

  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required="required">
  </div>
  <div class="form-group">
    <label for="email">email</label>    
    <input type="text" class="form-control" id="email" name="email" placeholder="Email" required="required">
  </div>
  <div class="form-group">
    <label for="phone">Phone</label>    
    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required="required">
  </div>
  <div class="form-group">
    <label for="document">Document</label>    
    <input type="text" class="form-control" id="document" name="document" placeholder="Document" required="required">
  </div>
  <div class="form-group">
    <label for="document">Position</label>    
    <input type="text" class="form-control" id="position" name="position" placeholder="Position" required="required">
  </div>

  <div class="form-group">
    <label for="document">Address</label>    
    <input type="text" class="form-control" id="address" name="address" placeholder="Address" >
  </div>

  <div class="form-group">
    <label for="budget">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password">    
  </div>
  

<div class="form-group">
   <label for="role_id">Role</label>
     <select name="role_id" id="role_id" class="form-control" >
        <option value="">Select a Role</option>
    <?php $__currentLoopData = $role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    ?>
    </select>
  </div>

  <!-- Estado-->
  <div class="form-group">
   <label for="role_id">Estado</label>
     <select name="status_id" id="status_id" class="form-control" >
        <option value="">Select a Status</option>
    <?php $__currentLoopData = $status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    ?>
    </select>
  </div>
  <div class="form-group">
    <label for="image_url">Foto</label>    
    <input type="file" id="image_url" name="image_url" placeholder="Foto">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>