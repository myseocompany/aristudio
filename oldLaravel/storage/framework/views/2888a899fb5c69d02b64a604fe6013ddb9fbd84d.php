<?php $__env->startSection('content'); ?>

<h1>Points by Week</h1>

<?php echo $__env->make('reports.weeks_by_team.graph', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?> 
<?php echo $__env->make('reports.weeks_by_team.filter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>  
  <!--  
  *
  *    Tabla reportes
  *
  -->

<table class="table table-striped table-hover table-responsive">
  <tr>
    <th>Week</th>
    <?php for($i=0; $i<count($users_graph); $i++): ?>
        
    <th>
      <?php echo e($users_graph[$i]->name); ?>

    </th>
    <?php endfor; ?>
  </tr>
  <?php for($j=0; $j<count($weeks_array); $j++): ?>
    
    <tr>
    <td><?php echo e($weeks_array[$j][0]); ?> - <?php echo e($weeks_array[$j][1]); ?></td>
    <?php for($i=0; $i<count($users_graph); $i++): ?>
  
    <td><?php echo e($data[$j][$i]); ?></td>
    <?php endfor; ?>
  </tr> 
  <?php endfor; ?>
  
</table>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>