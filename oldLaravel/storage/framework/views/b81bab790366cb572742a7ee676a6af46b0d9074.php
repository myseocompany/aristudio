
<?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,1,0,0,0) == 1): ?>
<h2>Login</h2>
<a href="/projects/<?php echo e($model->id); ?>/login_download" class="btn btn-sm btn-primary">Download</a>
  <?php endif; ?>

<div id="project-login" class="table-wrapper-scroll-y my-custom-scrollbar">
  
 <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,1,0,0,0) == 1): ?> 
<table class="table">
  <thead>
  </thead>
  </tr>
  <tbody>  


    <?php $__currentLoopData = $logins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>                           <!--  m_id,c,r,u,d) m=6-> logins -->
  
 
    <tr>
      <th>Url <br>Name <br> User <br> Password</th>
      

      <td>
         <a <?php if( substr($item->url,0,8)== 'https://'): ?> href="<?php echo e($item->url); ?>" <?php else: ?> href="https://<?php echo e($item->url); ?>" <?php endif; ?> target="_blank"><?php echo e($item->url); ?><a><br>
        <?php echo e($item->name); ?><br>
        <?php echo e($item->user); ?><br>
     
        <div style="float: left";>
          <a>
           <img  onclick="copyPassword('password_<?php echo e($item->id); ?>')" style="width: 20px;"  src="/images/copy.png">
          </a>
        </div>
        <div id="password_<?php echo e($item->id); ?>">
        <?php echo e($item->password); ?>

        </div>
        </td>
  
       
      <td>
          <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,0,1,0,0) == 1): ?>
        <a href="" class="btn btn-sm btn-primary my-2 my-sm-0"  data-toggle="modal" data-target="#editModal<?php echo e($item->id); ?>">Edit</a>
          <?php endif; ?>


            <!-- Modal -->
        <div class="modal fade" id="editModal<?php echo e($item->id); ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Login Edit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <form action="/project_logins/<?php echo e($item->id); ?>/update" method="POST">
              <?php echo e(csrf_field()); ?>

                <div class="modal-body">
                  
                    <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="name"><strong>Name</strong></label>
                        <input class="form-control" type="text" name="name" value="<?php echo e($item->name); ?>">
                      </div>

                      <div class="form-group">
                        <label for="url"><strong>URL</strong></label>
                        <input class="form-control" type="text" name="url" value="<?php echo e($item->url); ?>">
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="user"><strong>User</strong></label>
                        <input class="form-control" type="text" name="user" value="<?php echo e($item->user); ?>">
                      </div>

                      <div class="form-group">
                        <label for="password"><strong>Password</strong></label>
                        <input class="form-control" type="text" name="password" value="<?php echo e($item->password); ?>">
                      </div>
                      <input type="hidden" name="project_id" id="project_id" value="<?php echo e($item->id); ?>">
                    </div>

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-sm btn-secondary my-2 my-sm-0" data-dismiss="modal">Close</button>
                  <input type="submit" name="" value="Submit" class="btn btn-sm btn-primary my-2 my-sm-0">
                </div>
              </form>
            </div>
          </div>
        </div>
      </td>
      <td>
        <?php if(  Auth::user()->getPermitsRoleModule(Auth::user()->role_id,6,0,0,0,1,0) == 1): ?>
        <a class="btn btn-sm btn-danger my-2 my-sm-0" href="/project_logins/<?php echo e($item->id); ?>/delete" type="submit">Delete</a>
        <?php endif; ?>
    </tr>
    
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
 
  </tbody>
</table>
</div>
<?php endif; ?>    