<?php $__env->startSection('content'); ?>
<h1>Users</h1>
  <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,1,0,0,0,0) == 1): ?>
  <div><a href="/users/create">Create + <i class="fa fa-plus-square"></i></a></div>
  <?php endif; ?>
  <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>photo</th>
                  <th>User</th>
                  <th>Document</th>
                  <th>Email</th>
                  <th>Rol</th>
                  <th>Status</th>
                  <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,0,0,1,0,0) == 1): ?>  
                  <th>Edit</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                
                <tr>
                  <td><?php echo e($user->id); ?></td>
                  <td><?php if(isset($user->image_url)): ?><img src="/laravel/storage/app/public/files/users/<?php echo e($user->image_url); ?>" height="45"> <?php endif; ?></td>


                   <td>
                    <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,0,0,1,0,0) == 1): ?>  
                    <a href="/users/<?php echo e($user->id); ?>"><?php echo e($user->name); ?><br><?php echo e($user->phone); ?> <br><?php echo e($user->position); ?></a>
                  
                    <?php else: ?>
                     <a ><?php echo e($user->name); ?><br><?php echo e($user->phone); ?><br><?php echo e($user->position); ?></a>
                    <?php endif; ?> 

                  </td>

                  <td><?php echo e($user->document); ?></td>
                  <td><?php echo e($user->email); ?> <br><br> <?php echo e($user->address); ?></td>
             
                   
                  <td><?php if(isset($user->role_id)&&($user->role_id!="")): ?>
                    <?php echo e($user->role->name); ?> <?php endif; ?></td>
                  <td><?php if(isset($user->status_id)&&($user->status_id!="")): ?>
                    <?php echo e($user->status->name); ?> <?php endif; ?></td>
                   <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,4,0,0,1,0,0) == 1): ?>  
                  <td><a href="/users/<?php echo e($user->id); ?>/edit" class="btn btn-sm btn-primary my-2 my-sm-0">Edit</a></td>
                   <?php endif; ?> 
                </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>