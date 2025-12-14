<?php $__env->startSection('content'); ?>
<h1 class="text-center">SOPs, recipes and templates</h1>
  
<div><a href="#" data-toggle="modal" data-target="#sopModal" id="create-sop">Crear <i class="fa fa-plus-square"></i></a></div>

<!-- Modal -->
<div class="modal fade" id="sopModal" tabindex="-1" role="dialog" aria-labelledby="sopModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="sopModalLabel">Crear/Editar SOP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="sop-form" method="POST" action="">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" name="id" id="sop-id">
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" class="form-control" id="url" name="url" required>
            </div>
            <div class="form-group">
                <label for="type_id">Tipo</label>
                <select class="form-control" id="type_id" name="type_id" required>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="table-responsive">
    <div id="pdf-preview"></div>
    <table class="table table-striped">
        <thead>
        </thead>
        <tbody>
            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td colspan="12"><strong><?php echo e($type->name); ?></strong></td>
            </tr>
                <?php $__currentLoopData = $model; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $knowledge_management): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($type->id==$knowledge_management->type_id): ?>
                    <tr>
                        <td><?php echo e($knowledge_management->id); ?></td>
                        <td><?php echo e($knowledge_management->name); ?></td>
                        <td><a href="<?php echo e($knowledge_management->url); ?>" class="btn btn-primary">Open</a></td>
                        <td><a href="#" class="btn btn-secondary edit-sop" data-id="<?php echo e($knowledge_management->id); ?>" data-name="<?php echo e($knowledge_management->name); ?>" data-url="<?php echo e($knowledge_management->url); ?>" data-type_id="<?php echo e($knowledge_management->type_id); ?>" data-toggle="modal" data-target="#sopModal">Edit</a></td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById('create-sop').addEventListener('click', function() {
        document.getElementById('sop-id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('url').value = '';
        document.getElementById('type_id').value = '';
    });

    document.querySelectorAll('.edit-sop').forEach(function(button) {
        button.addEventListener('click', function() {
            document.getElementById('sop-id').value = this.dataset.id;
            document.getElementById('name').value = this.dataset.name;
            document.getElementById('url').value = this.dataset.url;
            document.getElementById('type_id').value = this.dataset.type_id;
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>