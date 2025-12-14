<?php  
    //dd($priority); 
 ?>
<?php if($priority): ?>
    <?php     $title = "Prioridades";     ?>
    <?php echo $__env->make("tasks.data_table_head", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
    <?php $__currentLoopData = $priority; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make("tasks.data_table_body", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php echo $__env->make("tasks.data_table_footer", array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
<?php endif; ?>