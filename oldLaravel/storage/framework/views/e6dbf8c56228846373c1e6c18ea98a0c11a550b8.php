
<div>
    <form action="/reports/weeks_team" method="GET">
    	

      <div class="row">
      	<div class="col">
      		<select name="filter" class="custom-select" id="filter" onchange="update()">
	        <option value="">select time</option>

	        <option value="currentmonth" <?php if($request->filter == "currentmonth"): ?> selected="selected" <?php endif; ?>>this month</option>
	        <option value="currentyear" <?php if($request->filter == "currentyear"): ?> selected="selected" <?php endif; ?>>this year</option>
	        
	        <option value="lastmonth" <?php if($request->filter == "lastmonth"): ?> selected="selected" <?php endif; ?>>last month</option>
	      	
	      	<option value="-30" <?php if($request->filter == "-30"): ?> selected="selected" <?php endif; ?>>last 30 days</option>
	      	<option value="-60" <?php if($request->filter == "-60"): ?> selected="selected" <?php endif; ?>>last 60 days</option>
        	<option value="-90" <?php if($request->filter == "-90"): ?> selected="selected" <?php endif; ?>>last 90 days</option>
        
      	</select>
      	</div>
        <div class="col">
        	<input class="input-date" type="date" id="from_date" name="from_date" onchange="cleanFilter()" value="<?php echo e($request->from_date); ?>">
        </div>
        <div class="col">
          
          <input class="input-date" type="date" id="to_date" name="to_date" onchange="cleanFilter()" value="<?php echo e($request->to_date); ?>">
       	</div>
       

        <div class="col">
			
			<select  name="user_id" class="slectpicker custom-select" id="user_id" onchange="submit();">
	        <option value="">Select user </option>


	        <?php $__currentLoopData = $users_graph; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	        <option value="<?php echo e($item->id); ?>"  <?php if($item->id == $request->user_id): ?>  selected="selected" <?php endif; ?> >
	        	<?php echo e($item->name); ?>

	        </option>
	        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	       
	      </select>
      </div>
      <div class="col">
	     <input type="submit" class="btn btn-sm btn-primary my-2 my-sm-0" value="Filtrar" >
       </div>
      </div>
    </form>
</div>
