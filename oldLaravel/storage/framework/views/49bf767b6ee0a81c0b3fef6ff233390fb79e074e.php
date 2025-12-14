<?php $__env->startSection('content'); ?>

<h1>Users by status</h1>
	
	<!--  
	*
	*    Tabla reportes
	*
	-->
<?php echo $__env->make('reports.user_tasks.filter', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>	
<?php 
	$sum_points = 0; 
	$total_points = 0;
	$count_statuses = 0;
	$total_debt = 0;
	$initial_debt = 0;
		
  	$url_charge_account= "charge_account?filter=$request->filter&from_date=$request->from_date&to_date=$request->to_date&project_id=$request->project_id&status_id=56&type_id=$request->type_id&subtype_id=$request->subtype_id&querystr=$request->querystr&priority=$request->priority";
?>
<br>
	<div class="report">
		<table  class="table table-bordered table-sm table-hover">
			<tr>
				
				<th class="title">Users</th>
				
				<th class="input_bg text-center">Debt</th>
				
				
				<script> 
					statuses_color = []; 
					statuses_label_color = []; 
				</script>
			<?php $__currentLoopData = $task_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<th class="text-center" style="background-color:<?php echo e($item->background_color); ?>;color:white"><?php echo e($item->alias); ?></th>
				<script>
					statuses_color[<?php echo e($item->id); ?>] = "<?php echo e($item->background_color); ?>"; 
					statuses_label_color[<?php echo e($item->id); ?>] = "<?php echo e($item->color); ?>"; 
				</script>
				<?php  $count_statuses++  ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<th class="text-center title">Total points</th>	
				<th class="input_bg text-center">Daily Goal</th>
				<th class="text-center title">Acumulated</th>
				<!-- <th>Charge Account</th> -- -->
			</tr>
			
			<?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>  
				<td class="subtitle"><a href="/tasks/?from_date=<?php echo e($request->from_date); ?>&to_date=<?php echo e($request->to_date); ?>&user_id=<?php echo e($user->id); ?>"><?php echo e($user->name); ?></a></td>
				<?php 
				$count = $user->countTaskInventoryBydDate($request->from_date); 
				$initial_debt += $count;
				
				?>

				<td class="text-center"><?php if($count>0): ?><?php echo e($count); ?> <?php endif; ?></td>
			<?php 
				$data= $user->countTaskByStatusAndDates(
						$task_statuses, 
						$request);
				
			 ?>
			 <script> cont=0; </script>
			 	
			<?php $__currentLoopData = $task_statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subitem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

				<td class="text-center" id="<?php echo e($user->id); ?>_<?php echo e($subitem->id); ?>" 
				<?php if($data[$subitem->id][1]!=null || $data[$subitem->id][0]!= null): ?>
					style="background-color:<?php echo e($subitem->background_color); ?>;color:white;"
					<?php endif; ?> >
						
						<?php if(isset($data[$subitem->id])  ): ?>
							<?php 
							if($subitem->id == 6 || $subitem->id == 56)
								$sum_points += $data[$subitem->id][0] ?>
							<div id="task_points">
								<a style="color:white;" id="task_points_<?php echo e($user->id); ?>_<?php echo e($subitem->id); ?>" href="/tasks/?from_date=<?php echo e($request->from_date); ?>&to_date=<?php echo e($request->to_date); ?>&user_id=<?php echo e($user->id); ?>&status_id=<?php echo e($subitem->id); ?>">
									<?php if($subitem->id != 6 && $subitem->id != 56): ?>
									[<?php echo e($data[$subitem->id][1]); ?>]
									<?php else: ?>
									<?php echo e($data[$subitem->id][0]); ?> 
									<?php endif; ?>
								</a>
							</div>
							
							<script>
							document.getElementById("<?php echo e($user->id); ?>_<?php echo e($subitem->id); ?>").style.backgroundColor = statuses_color[<?php echo e($subitem->id); ?>];
							document.getElementById("task_points_<?php echo e($user->id); ?>_<?php echo e($subitem->id); ?>").style.color = statuses_label_color[<?php echo e($subitem->id); ?>];
							
							</script>
						<?php endif; ?>


				</td>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				
				<td class="text-center"><?php echo e($sum_points); ?></td>
				<td class="text-center" ><?php echo e($user->daily_goal); ?></td>
				<?php 
				$total_points += $sum_points;
				$sum_points = 0; 
				$count = $user->countTaskInventoryBydDate($request->to_date);
				$total_debt += $count;
				
				?>
				
				<td class="text-center"><?php if($count>0): ?><?php echo e($count); ?><?php endif; ?></td>
				<!--
				<td>
					<a href="/<?php echo e($url_charge_account); ?>&user_id=<?php echo e($user->id); ?>" target="_blanck">Download</a>
				</td>
				-->
			</tr>
			
			<?php  
			$total_goal += $user->daily_goal ;
			$total_debt += $user->daily_goal ;
			
				
			 ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td>Total</td>
				<td class="text-center"><?php echo e($initial_debt); ?></td>
				<td colspan="<?php echo e($count_statuses); ?>"></td>
				<td class="text-center"><?php echo e($total_points); ?></td>
				<td class="text-center"><?php echo e($total_goal); ?></td>
				<td colspan="1"></td>
			</tr>
		</table>	

</div>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>