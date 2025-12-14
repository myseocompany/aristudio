<?php $__env->startSection('content'); ?>

<div class="form-login">
                <div class="form-group">
                    <div class="col-md-12">
                        <center>
                            <img class="img-logo" src="/images/logo_my_seo_company.png">
                        </center>
                        <br>
                    </div>
                </div>
                
                <?php ?>
                    <form class="form-horizontal" method="POST" action="<?php echo e(route('login')); ?>">
                        <?php echo e(csrf_field()); ?>


                        <div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?>">
                            <label for="email" class="col-md-4 control-label">Correo electrónico</label>

                            <div class="col-md-12">
                                <input id="email" type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required autofocus>

                                <?php if($errors->has('email')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
                            <label for="password" class="col-md-4 control-label">Contraseña</label>

                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control" name="password" required>

                                <?php if($errors->has('password')): ?>
                                    <span class="help-block">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> Recuérdame
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary" style="width: 100%">
                                    Login
                                </button>

                                <a class="btn btn-link" href="<?php echo e(route('password.request')); ?>">
                                    Olvidaste tu conrtraseña?
                                </a>
                            </div>
                        </div>
                    </form>
</div>

<style type="text/css">
 
    .form-login{
        margin-top: 25%;
    }
    

    a.btn.btn-link {
        float: right;
    }

    body {
        overflow-x: hidden;
    }

    .img-logo{
        width: 70%;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout_login', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>