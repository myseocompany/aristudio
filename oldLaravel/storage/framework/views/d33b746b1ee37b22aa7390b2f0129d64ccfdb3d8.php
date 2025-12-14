<?php $__env->startSection('content'); ?>

<h1>Projects by state</h1>


	
	<!--  
	*
	*    Tabla reportes
	*
	-->

<?php echo $__env->make('reports.projects.filter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	

<?php 
	$sum_points = 0; 
 ?>
<br>
	<div class="report">
		<table  class="table table-bordered table-sm table-hover">
			<tr>
			<script> 
				statuses_color = []; 
				statuses_label_color = []; 
			</script>	
				<th class="title">Projects</th>
				<th class="input_bg text-center">Task at<br>Start</th>
				<th class="input_bg text-center">Montly<br>Goal</th>
			
			<?php $__currentLoopData = $task_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<th class="text-center" style="background-color:<?php echo e($item->background_color); ?>"><?php echo e($item->alias); ?></th>
				<script> 
					statuses_color[<?php echo e($item->id); ?>] = "<?php echo e($item->background_color); ?>"; 
					statuses_label_color[<?php echo e($item->id); ?>] = "<?php echo e($item->color); ?>";
				</script>
				<?php 
							$monthly_points_goal = $project->monthly_points_goal;
						?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				
				<th class="text-center title">Debt</th>
				<th class="input_bg text-center">Total</th>
				<th class="input_bg text-center">Goal<br> %</th>
				
			</tr>
			
			<?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>  
				<td class="subtitle"><a href="/tasks/?from_date=<?php echo e($request->from_date); ?>&to_date=<?php echo e($request->to_date); ?>&project_id=<?php echo e($project->id); ?>&user_id=<?php echo e($request->user_id); ?>"><?php echo e($project->name); ?></a></td>
				<td class="text-center"><?php $count = $project->countTaskInventoryBydDate($request->from_date); ?><?php if($count>0): ?><?php echo e($count); ?> <?php endif; ?></td>
				<td class="text-center"><?php echo e($project->monthly_points_goal); ?></td>
			<?php 
				$data= $project->countTaskByStatusAndDates($task_statuses, $request);
			 ?>
			 
			<?php $__currentLoopData = $task_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			
				<td class="text-center" id="<?php echo e($project->id); ?>_<?php echo e($subitem->id); ?>">
						
						<?php if(isset($data[$subitem->id])): ?>

						<?php 
							if($subitem->id != 4 || $subitem->id !=5 || $subitem->id !=7 || $subitem->id !=3  || $subitem->id !=57 || $subitem->id !=59){
								$sum_points += $data[$subitem->id][0];
							}
						?>
							<div id="task_points">
								<a id="task_points_<?php echo e($project->id); ?>_<?php echo e($subitem->id); ?>" href="/tasks/?from_date=<?php echo e($request->from_date); ?>&to_date=<?php echo e($request->to_date); ?>&project_id=<?php echo e($project->id); ?>&user_id=<?php echo e($request->user_id); ?>&status_id=<?php echo e($subitem->id); ?>">
								<?php echo e($data[$subitem->id][0]); ?> / <?php echo e($data[$subitem->id][1]); ?>

								</a>
							</div>
							<?php 

								
							?>
							<!--
							<br>
							<div id="task_content">
								<div id="task_content_left"><?php echo e($data[$subitem->id][1]); ?></div>
								<div id="task_content_right"><?php echo e($data[$subitem->id][2]); ?></div>
							</div>
						-->
						<?php endif; ?>

						<script>
							<?php 
								if(isset($data[$subitem->id][0])||isset($data[$subitem->id][1])||isset($data[$subitem->id][2])){
							?>
								document.getElementById("<?php echo e($project->id); ?>_<?php echo e($subitem->id); ?>").style.backgroundColor = statuses_color[<?php echo e($subitem->id); ?>];
								document.getElementById("task_points_<?php echo e($project->id); ?>_<?php echo e($subitem->id); ?>").style.color = statuses_label_color[<?php echo e($subitem->id); ?>];
							<?php
								}
							 ?>
						</script>
				</td>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<td class="text-center"><?php $count = $project->countTaskInventoryBydDate($request->to_date); ?><?php if($count>0): ?><?php echo e($count); ?><?php endif; ?></td>
				<td class="text-center"><?php echo e($sum_points); ?></td>
				<td class="text-center"><?php if(isset($project->monthly_points_goal)&&($project->monthly_points_goal>0)): ?>

								<?php echo e(number_format($sum_points * 100 / $project->monthly_points_goal, 1, '.', '')); ?>

								<?php endif; ?>
				</td>
				<?php $sum_points = 0; ?>
			</tr>
			
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</table>	

</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>