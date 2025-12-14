<?php $__env->startSection('content'); ?>

<h1>Points by Users Month</h1>
<?php // dd($time_span_array); ?>
<?php echo $__env->make('reports.months_by_user.graph', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	
<?php echo $__env->make('reports.months_by_user.filter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	
	<!--  
	*
	*    Tabla reportes
	*
	-->

<table class="table table-striped table-hover table-responsive">
	<tr>
		<th>Team </th>
		<?php for($i=0; $i<$time_span; $i++): ?>
		<th>
			<div class="report-month"><?php echo e(substr($time_span_array[$i][0], 5, 5)); ?></div> 
			<div class="report-month"><?php echo e(substr($time_span_array[$i][1], 5, 5)); ?></div>
		</th>
		<?php endfor; ?>
	</tr><?php $j = 0; ?>
	<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<tr>
		<td><?php echo e($item->name); ?></td>
		<?php for($i=0; $i<$time_span; $i++): ?>
		<td><?php echo e($data[$j][$i]); ?></td>
		<?php endfor; ?>
		<?php $j++; ?>
	</tr>	
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	
</table>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>