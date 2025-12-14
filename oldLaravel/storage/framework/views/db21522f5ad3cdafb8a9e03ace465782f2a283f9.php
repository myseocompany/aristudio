<?php $__env->startSection('content'); ?>


<div class="">
    <h2>Change User Password</h2>

    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('password.update', $user->id)); ?>">

        <div class="mb-3">
            <label for=""><?php echo e($user->name); ?></label>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nueva contraseña</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
                <div class="text-danger"><?php echo e($message); ?></div>
        </div>

        <div class="mb-3">
            <label for="new_password_confirmation" class="form-label">Confirmar nueva contraseña</label>
            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>