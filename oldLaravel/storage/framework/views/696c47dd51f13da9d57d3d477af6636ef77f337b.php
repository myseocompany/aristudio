<h2>Documents</h2>
<div>
  <div id="documents-table" class="table-wrapper-scroll-y my-custom-scrollbar">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Internal Id</th>
          <th>Date</th>
          <th>Account</th>
          <th>Debit</th>
          <th>Credit</th>
          <th>Description</th>
          <th>File</th>
          <th>-</th>
          <th>-</th>

        </tr>  
      </thead>
      <tbody>
        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($item->internal_id); ?></td>
          <td><?php echo e($item->date); ?></td>
          <td><?php if(isset($item->account)): ?><a href="<?php echo e($item->url); ?>" target="_blank"><?php echo e($item->account->name); ?></a><?php endif; ?></td>
          <td><?php echo e(number_format($item->debit,0)); ?></td>
          <td><?php echo e(number_format($item->credit,0)); ?></td>
          <td><?php echo e($item->description); ?></td>
          <td><a href="<?php echo e($item->url); ?>">File</a></td>
          <td><a class="btn btn-sm btn-primary my-2 my-sm-0" href="/project_documents/<?php echo e($item->id); ?>/edit">Edit</a></td>
          <td><a class="btn btn-sm btn-primary my-2 my-sm-0" href="/project_documents/<?php echo e($item->id); ?>/delete">Delete</a></td>
          
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
      
    </table>
  </div>
</div>
<form method="POST" action="/project_documents" enctype="multipart/form-data">
    
  <div class="row"><?php echo e(csrf_field()); ?>

    <div class="col-md-6">
      <div class="form-group col-md-12">
        <label for="account_id" class=""><strong>Account</strong></label>
        <select name="account_id" id="account_id" class="col-md-12 form-control" >

          <option value="">Select account...</option>
          <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div class="form-group col-md-12">
        <label for="document_type_id" class=""><strong>Document Type</strong></label>
        <select name="document_type_id" id="document_type_id" class="col-md-12 form-control" >

          <option value="">Select document type...</option>
          <?php $__currentLoopData = $document_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="form-group col-md-12">
        <label for="debit"><strong>Debit</strong></label>
        <input class="form-control" type="text" name="debit"> 
      </div>
      <div class="form-group col-md-12">
      
        <label for="credit"><strong>Credit</strong></label>
        <input class="form-control" type="text" name="credit"> 
      </div>
         
    </div>
    <div class="col-md-6">
      <div class="form-group">
    
    <div class="container">
      <div class="row">
        
        <div class="col-md-12">
          <label for="file" ><strong>Seleccione el archivo</strong></label>
        </div>

        <div class="col-md-12">
          <input type="file" class="form-control" id="file" name="file" placeholder="email" >
        </div>
        <div class="col-md-12">
        <label for="date"><strong>Date</strong></label>
        <input class="form-control" type="date" name="date"> 
        </div>
        <div class="col-md-12">
        <label for="internal_id"><strong>Internal ID</strong></label>
        <input class="form-control" type="text" name="internal_id"> 
        </div>
        <div class="col-md-12">
        <label for="description"><strong>Descripcion</strong></label>
        <input class="form-control" type="text" name="description"> 
        </div>


        <input type="hidden" id="project_id" name="project_id" value="<?php echo e($model->id); ?>">

      </div>
    </div>
  </div>
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md-12">
      
      <input type="submit" class="btn btn-sm btn-primary glyphicon glyphicon-pencil" aria-hidden="true" value="submit">
    </div>
  </div>

</form>