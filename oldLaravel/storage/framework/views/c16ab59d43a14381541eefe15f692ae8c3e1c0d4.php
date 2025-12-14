<?php $__env->startSection('content'); ?>
<h1>Edit Users</h1>
<form method="POST" action="/users/<?php echo e($model->id); ?>/update" enctype="multipart/form-data">
  <?php echo e(csrf_field()); ?>


  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Name" required value="<?php echo e($model->name); ?>">
      </div>
      <div class="form-group">
        <label for="document">Document</label>
        <input type="text" class="form-control" id="document" name="document" placeholder="Document" required value="<?php echo e($model->document); ?>">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required value="<?php echo e($model->email); ?>">
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" required value="<?php echo e($model->phone); ?>">
      </div>
      <div class="form-group">
        <label for="birth_date">Birth Date</label>
        <input type="date" name="birth_date" id="birth_date" class="form-control" value="<?php echo e($model->birth_date); ?>">
      </div>
      <div class="form-group">
        <label for="blood_type">Blood Type</label>
        <input type="text" name="blood_type" id="blood_type" class="form-control" value="<?php echo e($model->blood_type); ?>">
      </div>
      <div class="form-group">
        <label for="address">Address</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="<?php echo e($model->address); ?>">
      </div>
      <div class="form-group">
        <label for="position">Position</label>
        <input type="text" class="form-control" id="position" name="position" placeholder="Position" required value="<?php echo e($model->position); ?>">
      </div>
    </div>

    <div class="col-md-6">
      <div class="form-group">
        <label for="daily_goal">Daily Goal</label>
        <input type="number" class="form-control" id="daily_goal" name="daily_goal" value="<?php echo e($model->daily_goal); ?>">
      </div>
      <div class="form-group">
        <label for="hourly_rate">Hourly Rate</label>
        <input type="number" class="form-control" id="hourly_rate" name="hourly_rate"  value="<?php echo e($model->hourly_rate); ?>">
      </div>
      <div class="form-group">
        <label for="contract_type">Contract Type</label>
        <input type="text" class="form-control" id="contract_type" name="contract_type" value="<?php echo e($model->contract_type); ?>">
      </div>
      <div class="form-group">
        <label for="eps">E.P.S</label>
        <input type="text" class="form-control" id="eps" name="eps" value="<?php echo e($model->eps); ?>">
      </div>
      <div class="form-group">
        <label for="arl">A.R.L</label>
        <input type="text" class="form-control" id="arl" name="arl" value="<?php echo e($model->arl); ?>">
      </div>
      <div class="form-group">
        <label for="role_id">Role</label>
        <select class="form-control" name="role_id" id="role_id">
          <?php $__currentLoopData = $role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($item->id); ?>" <?php if($item->id==$model->role_id): ?> selected <?php endif; ?>><?php echo e($item->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group">
        <label for="status_id">Status</label>
        <select class="form-control" name="status_id" id="status_id">
          <?php $__currentLoopData = $user_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($item->id); ?>" <?php if($item->id==$model->status_id): ?> selected <?php endif; ?>><?php echo e($item->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group">
        <label for="image_url">Photo</label>
        <input type="file" id="image_url" name="image_url" class="form-control">
      </div>
    </div>
  </div>

  <div class="form-group mt-4">
    <button type="submit" class="btn btn-primary">Submit</button>
  </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>